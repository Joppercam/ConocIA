<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Podcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateDailySummary extends Command
{
    protected $signature = 'podcasts:daily-summary {--days=1} {--limit=6} {--timeout=120} {--retries=3}';
    protected $description = 'Genera un podcast resumen diario con las noticias más leídas, con resúmenes mejorados usando OpenAI';
    
    // Límite de caracteres para la API TTS de OpenAI
    protected $maxApiChars = 4000;
    
    // Asignación de caracteres por noticia en el resumen (mínimo 550)
    protected $charsPerNews = 550;
    
    // Nombre del portal de noticias
    protected $portalName = 'conocía.cl'; // Nombre correcto con acento en la í
    protected $portalSlogan = 'El futuro del conocimiento es artificialmente inteligente';

    public function handle()
    {
        // Aumentar el límite de tiempo de ejecución de PHP
        set_time_limit(300); // 5 minutos
        
        $this->info('Iniciando generación de podcast resumen diario...');
        
        // Verificar si la tabla podcasts permite NULL en news_id
        $allowsNull = $this->checkIfNewsIdAllowsNull();
        
        if (!$allowsNull) {
            $this->warn('La columna news_id en la tabla podcasts no permite valores NULL.');
            $this->warn('Se usará una noticia como referencia para el resumen diario.');
        }
        
        $days = $this->option('days');
        $limit = $this->option('limit');
        $timeout = $this->option('timeout'); 
        $maxRetries = $this->option('retries');
        
        // Obtener las noticias más leídas 
        $topNews = News::published()
                     ->where('created_at', '>=', now()->subDays($days))
                     ->orderBy('views', 'desc')
                     ->take($limit)
                     ->get();
                     
        $this->info('Se encontraron ' . $topNews->count() . ' noticias para el resumen diario');
        
        if ($topNews->isEmpty()) {
            $this->warn('No se encontraron noticias para el periodo especificado.');
            return;
        }
        
        // Generar resúmenes mejorados para cada noticia usando OpenAI
        $this->info('Generando resúmenes mejorados para cada noticia...');
        $newsWithSummaries = $this->generateAISummaries($topNews);
        
        // Recalcular el espacio por noticia basado en el número real de noticias
        // Asegurarse que el espacio por noticia no sea menor a 550 caracteres
        $this->charsPerNews = max(550, floor(($this->maxApiChars - 400) / $newsWithSummaries->count()));
        $this->info("Asignando aproximadamente {$this->charsPerNews} caracteres por noticia");
        
        // Determinar la ruta de directorio para almacenar el podcast
        $baseDir = 'podcast' . DIRECTORY_SEPARATOR . 'daily-summary' . DIRECTORY_SEPARATOR . date('Y-m-d');
        
        // Corregido: Usar Storage::disk para manejo de almacenamiento en Laravel
        if (!Storage::disk('public')->exists($baseDir)) {
            $this->info('Creando directorio: ' . $baseDir);
            try {
                // Usar el filesystem de Laravel para crear directorios
                Storage::disk('public')->makeDirectory($baseDir, 0755, true);
            } catch (\Exception $e) {
                Log::error("Error al crear directorio: " . $e->getMessage());
                $this->error('No se pudo crear el directorio: ' . $e->getMessage());
                return;
            }
        }
        
        // Ruta completa accesible desde PHP para operaciones de archivo
        $fullBaseDir = storage_path('app/public/' . $baseDir);
        
        // Preparar el contenido compilado para todas las noticias
        $compiledContent = $this->prepareCompiledContent($newsWithSummaries);
        
        // Verificar el tamaño del contenido
        $this->info('Tamaño del contenido: ' . strlen($compiledContent) . ' caracteres');
        if (strlen($compiledContent) > $this->maxApiChars) {
            $this->warn('El contenido excede el límite de la API. Se aplicará un ajuste adicional.');
            $compiledContent = $this->adjustContentToFitLimit($compiledContent);
            $this->info('Nuevo tamaño del contenido: ' . strlen($compiledContent) . ' caracteres');
        }
        
        // Nombre del archivo único para el resumen diario
        $dateSlug = date('Y-m-d');
        $audioPath = $this->convertToAudio($compiledContent, 'daily-summary-' . $dateSlug, $baseDir, $fullBaseDir, $timeout, $maxRetries);
        
        if ($audioPath) {
            try {
                // IMPORTANTE: Verificar si ya existe un resumen diario para hoy y eliminarlo
                $existingDaily = Podcast::where('is_daily_summary', true)
                                 ->whereDate('created_at', now()->toDateString())
                                 ->first();
                
                if ($existingDaily) {
                    $this->info('Se encontró un resumen diario existente para hoy (ID: ' . $existingDaily->id . '). Será reemplazado.');
                    $existingDaily->delete();
                }
                
                // Crear registro de podcast para el resumen diario
                $podcastData = [
                    'title' => 'Resumen diario de noticias: ' . date('d/m/Y'),
                    'audio_path' => $audioPath,
                    'duration' => $this->getAudioDuration($audioPath),
                    'play_count' => 0,
                    'voice' => 'nova',
                    'published_at' => now(),
                    'is_daily_summary' => true, // Aseguramos que se guarde como true (1)
                    'news_count' => $newsWithSummaries->count(),
                ];
                
                // Si news_id no permite NULL, usar la primera noticia como referencia
                if (!$allowsNull && $newsWithSummaries->isNotEmpty()) {
                    $podcastData['news_id'] = $newsWithSummaries->first()->id;
                    $this->info('Usando la noticia #' . $newsWithSummaries->first()->id . ' como referencia para el resumen diario');
                } else {
                    $podcastData['news_id'] = null;
                }
                
                // Crear el podcast y verificar que is_daily_summary se guarde correctamente
                $podcast = Podcast::create($podcastData);

                // Guardar relación con las noticias incluidas en el resumen
                foreach ($newsWithSummaries as $news) {
                    $podcast->news()->attach($news->id);
                }
                
                // Verificación extra para asegurar que is_daily_summary es true
                if (!$podcast->is_daily_summary) {
                    $this->warn('is_daily_summary no se guardó como true. Intentando actualizar manualmente...');
                    $podcast->is_daily_summary = true;
                    $podcast->save();
                    
                    // Verificar nuevamente
                    $podcast->refresh();
                    if ($podcast->is_daily_summary) {
                        $this->info('is_daily_summary actualizado correctamente a true');
                    } else {
                        $this->error('No se pudo actualizar is_daily_summary a true');
                    }
                }
                
                $this->info('Resumen diario generado con éxito (ID: ' . $podcast->id . ')');
                $this->info('Ruta del audio: ' . $podcast->audio_path);
                $this->info('is_daily_summary: ' . ($podcast->is_daily_summary ? 'true' : 'false'));
            } catch (\Exception $e) {
                $this->error('Error al guardar el podcast en la base de datos: ' . $e->getMessage());
                Log::error('Error al guardar el podcast: ' . $e->getMessage());
                return;
            }
        } else {
            $this->error('Error al generar el resumen diario');
        }
        
        $this->info('Proceso de generación de resumen diario completado');
    }
    
    /**
     * Verifica si la columna news_id en la tabla podcasts permite valores NULL
     */
    private function checkIfNewsIdAllowsNull()
    {
        try {
            // Verificar si la columna permite NULL
            $column = DB::select("PRAGMA table_info(podcasts)");
            foreach ($column as $col) {
                if ($col->name === 'news_id') {
                    // En SQLite, 'notnull' = 1 significa que NO permite NULL
                    return $col->notnull != 1;
                }
            }
            return false;
        } catch (\Exception $e) {
            $this->warn('No se pudo verificar si news_id permite NULL: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Genera resúmenes mejorados de cada noticia utilizando OpenAI
     */
    private function generateAISummaries($newsList)
    {
        $apiKey = config('services.openai.api_key');
        
        if (empty($apiKey)) {
            $this->error('No se ha configurado la clave API de OpenAI. Verifique el archivo .env');
            return $newsList;
        }
        
        foreach ($newsList as $key => $news) {
            $this->info("Generando resumen para noticia #{$news->id}: {$news->title}");
            
            try {
                // Preparar el contenido para el resumen (título + contenido limpio)
                $cleanContent = strip_tags($news->content);
                
                // Limitar el contenido a procesar para evitar tokens excesivos
                if (strlen($cleanContent) > 2000) {
                    $cleanContent = substr($cleanContent, 0, 2000) . '...';
                }
                
                // Petición a OpenAI para generar un resumen conciso y contextualizado
                $response = Http::timeout(60)->withHeaders([  // Aumento del timeout a 60 segundos
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres un asistente especializado en crear resúmenes periodísticos concisos, informativos y atractivos. El resumen debe tener entre 3-5 oraciones completas, mantener el contexto esencial y ser fácil de entender al escucharlo. Usa oraciones simples, evita jerga técnica compleja, terminología especializada o nombres difíciles de pronunciar. Usa puntos para separar ideas. Evita abreviaturas. Asegúrate que cada oración tenga sentido completo y termine adecuadamente. El contenido debe estar preparado para ser leído fluidamente por un sintetizador de voz.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Crea un resumen para la siguiente noticia. Título: \"{$news->title}\". Contenido: \"{$cleanContent}\""
                        ]
                    ],
                    'temperature' => 0.5,
                    'max_tokens' => 150,
                ]);
                
                if ($response->successful()) {
                    $aiSummary = $response->json()['choices'][0]['message']['content'] ?? null;
                    
                    if ($aiSummary) {
                        // Verificar que el resumen termina adecuadamente
                        if (!preg_match('/[.!?]$/', $aiSummary)) {
                            $aiSummary .= '.';
                        }
                        
                        // Optimizar para TTS
                        $aiSummary = $this->optimizeSummaryForTTS($aiSummary);
                        
                        // Guardar el resumen generado en la propiedad de la noticia
                        $newsList[$key]->ai_summary = trim($aiSummary);
                        $this->info('Resumen generado con éxito');
                    } else {
                        $this->warn('La respuesta de OpenAI no contiene un resumen válido');
                    }
                } else {
                    $this->warn('Error al generar resumen con OpenAI: ' . $response->body());
                    Log::warning('Error al generar resumen con OpenAI: ' . $response->body());
                }
            } catch (\Exception $e) {
                $this->warn('Excepción al generar resumen para noticia #' . $news->id . ': ' . $e->getMessage());
                Log::warning('Excepción al generar resumen para noticia #' . $news->id . ': ' . $e->getMessage());
            }
            
            // Pequeña pausa para evitar límites de rate en la API
            usleep(500000); // 500ms
        }
        
        return $newsList;
    }
    
    /**
     * Optimiza el resumen para mejor pronunciación en TTS
     */
    private function optimizeSummaryForTTS($text)
    {
        // Reemplazar abreviaturas comunes
        $replacements = [
            '/\bEE\.UU\./i' => 'Estados Unidos',
            '/\bUE\b/i' => 'Unión Europea',
            '/\bONU\b/i' => 'Organización de las Naciones Unidas',
            '/\bOMS\b/i' => 'Organización Mundial de la Salud',
            '/\bFMI\b/i' => 'Fondo Monetario Internacional',
            '/\bPIB\b/i' => 'Producto Interno Bruto',
            '/\bCOVID-19\b/i' => 'COVID diecinueve',
            '/\bCOVID\b/i' => 'COVID',
            '/\b([A-Z]{2,})\b/' => function($matches) {
                // Si es una sigla, separarla con espacios para mejor pronunciación
                if (strlen($matches[1]) <= 5) {
                    return implode(' ', str_split($matches[1]));
                }
                return $matches[1];
            },
            // Mejorar la pronunciación de números
            '/(\d+)%/' => '$1 por ciento',
            '/(\d+),(\d+)/' => '$1 coma $2',
            // Agregar pausas después de frases largas
            '/([^.!?]{60,})([,;])/' => '$1$2 ',
        ];
        
        // Aplicar reemplazos
        foreach ($replacements as $pattern => $replacement) {
            if (is_callable($replacement)) {
                $text = preg_replace_callback($pattern, $replacement, $text);
            } else {
                $text = preg_replace($pattern, $replacement, $text);
            }
        }
        
        return $text;
    }
    
    /**
     * Prepara el texto compilado de todas las noticias para convertirlo a audio
     * Versión mejorada con transiciones y resúmenes generados por IA
     */
    private function prepareCompiledContent($newsList)
    {
        // Introducción más clara y pausada
        $introduction = "Bienvenidos al resumen diario de " . $this->portalName . ". ";
        $introduction .= "A continuación escuchará las noticias más importantes de hoy, " . date('d \d\e F \d\e\l Y') . ". ";
        
        $content = "";
        
        foreach ($newsList as $index => $news) {
            // Incluir título y categoría para mejor contexto
            $category = $news->category ? $news->category->name : 'General';
            $segmentStart = "En la sección de {$category}: {$news->title}. ";
            $segmentContent = "";
            
            // Priorizar el resumen generado por IA
            if (!empty($news->ai_summary)) {
                $segmentContent .= $news->ai_summary . " ";
            } 
            // Usar el resumen existente como respaldo
            else if (!empty($news->summary)) {
                $segmentContent .= strip_tags($news->summary) . " ";
            } 
            // Como último recurso, utilizar un fragmento del contenido
            else {
                $cleanContent = strip_tags($news->content);
                
                // Extraer oraciones completas para evitar cortes abruptos
                $sentences = preg_split('/(?<=[.!?])\s+/', $cleanContent, -1, PREG_SPLIT_NO_EMPTY);
                $usableSentences = [];
                $usedChars = 0;
                $targetChars = $this->charsPerNews - strlen($segmentStart) - 20; // 20 caracteres de margen
                
                foreach ($sentences as $sentence) {
                    if ($usedChars + strlen($sentence) <= $targetChars) {
                        $usableSentences[] = $sentence;
                        $usedChars += strlen($sentence) + 1; // +1 para el espacio
                    } else {
                        break;
                    }
                }
                
                if (empty($usableSentences)) {
                    // Si no hay oraciones completas usables, tomar un fragmento con corte limpio
                    $limitedContent = substr($cleanContent, 0, $targetChars - 3);
                    // Cortar en el último espacio para no interrumpir palabras
                    $lastSpace = strrpos($limitedContent, ' ');
                    if ($lastSpace !== false) {
                        $limitedContent = substr($limitedContent, 0, $lastSpace);
                    }
                    $segmentContent .= $limitedContent . "... ";
                } else {
                    $segmentContent .= implode(' ', $usableSentences) . " ";
                }
            }
            
            // Asegurarse de que el segmento no exceda el límite y termine correctamente
            $availableSpace = $this->charsPerNews;
            $fullSegment = $segmentStart . $segmentContent;
            
            if (strlen($fullSegment) > $availableSpace) {
                // Corte inteligente: buscar el último punto o final de oración
                $truncatedSegment = substr($fullSegment, 0, $availableSpace - 3);
                $lastPeriod = strrpos($truncatedSegment, '.');
                $lastQuestion = strrpos($truncatedSegment, '?');
                $lastExclamation = strrpos($truncatedSegment, '!');
                
                $lastSentenceEnd = max($lastPeriod, $lastQuestion, $lastExclamation);
                
                if ($lastSentenceEnd !== false && $lastSentenceEnd > strlen($truncatedSegment) * 0.7) {
                    // Si encontramos un final de oración en el último tercio, cortamos ahí
                    $truncatedSegment = substr($truncatedSegment, 0, $lastSentenceEnd + 1);
                    $fullSegment = $truncatedSegment . " ";
                } else {
                    // Si no hay un buen punto de corte, añadir indicador de continuación
                    $lastSpace = strrpos($truncatedSegment, ' ');
                    if ($lastSpace !== false) {
                        $truncatedSegment = substr($truncatedSegment, 0, $lastSpace);
                    }
                    $fullSegment = $truncatedSegment . "... ";
                }
            }
            
            // Agregar cierre explícito para la noticia actual
            if (!preg_match('/[.!?]\s*$/', $fullSegment)) {
                $fullSegment .= ". ";
            }
            
            // Añadir el segmento al contenido general
            $content .= $fullSegment;
            
            // Agregar transición clara entre noticias, excepto en la última
            if ($index < count($newsList) - 1) {
                $content .= "Pasamos a la siguiente noticia. ";
            }
        }
        
        // Cierre con alusión al portal de noticias
        $ending = "Este ha sido el resumen diario de noticias de {$this->portalName} - {$this->portalSlogan}. Para acceder a la información completa, entrevistas exclusivas y contenido especializado, visite nuestro portal web en {$this->portalName} o descargue nuestra aplicación móvil. Manténgase informado con nosotros, su fuente confiable de noticias. Gracias por su atención.";
        
        return $introduction . $content . $ending;
    }
    
    /**
     * Ajusta el contenido si aún excede el límite de la API eliminando noticias completas
     */
    private function adjustContentToFitLimit($content)
    {
        // Extraer partes estructurales: introducción, noticias y conclusión
        $introductionPattern = '/^(.*?)En la sección de/s';
        preg_match($introductionPattern, $content, $introMatches);
        $introduction = $introMatches[1] ?? '';
        
        // Encuentra el punto de inicio de la conclusión
        $conclusionStart = strpos($content, "Este ha sido el resumen");
        $ending = $conclusionStart !== false ? substr($content, $conclusionStart) : '';
        
        // Extraer las secciones de noticias
        $newsContent = [];
        $pattern = '/En la sección de.*?((?=En la sección de)|(?=Este ha sido el resumen)|$)/s';
        preg_match_all($pattern, $content, $matches);
        
        if (!empty($matches[0])) {
            $newsContent = $matches[0];
        }
        
        $this->info('Se identificaron ' . count($newsContent) . ' noticias en el contenido');
        
        // Calcular espacio disponible para noticias
        $availableSpace = $this->maxApiChars - strlen($introduction) - strlen($ending) - 50; // 50 para margen
        
        // Determinar cuántas noticias completas podemos incluir
        $newsToInclude = [];
        $currentSize = 0;
        
        foreach ($newsContent as $index => $newsItem) {
            $newsSize = strlen($newsItem);
            
            if ($currentSize + $newsSize <= $availableSpace) {
                $newsToInclude[] = $newsItem;
                $currentSize += $newsSize;
            } else {
                // Si la noticia no cabe, la omitimos completamente
                break;
            }
        }
        
        $removedCount = count($newsContent) - count($newsToInclude);
        if ($removedCount > 0) {
            $this->warn("El contenido es demasiado grande. Se eliminaron {$removedCount} noticias para ajustarse al límite.");
        }
        
        // Reconstruir el contenido
        $newContent = $introduction;
        
        // Agregar las noticias que sí caben
        $newContent .= implode('', $newsToInclude);
        
        // Ajustar el texto de cierre para reflejar el número correcto de noticias
        if ($removedCount > 0) {
            $originalCountPattern = '/Este ha sido el resumen diario de noticias/';
            $replacementText = "Este ha sido el resumen diario de " . count($newsToInclude) . " noticias";
            $ending = preg_replace($originalCountPattern, $replacementText, $ending);
        }
        
        // Añadir el cierre
        $newContent .= $ending;
        
        // Verificación final
        if (strlen($newContent) > $this->maxApiChars) {
            $this->warn("Después de eliminar noticias, el contenido aún excede el límite. Se aplicará una reducción adicional.");
            
            // Si aún es demasiado largo, reducir el número de noticias más
            while (count($newsToInclude) > 1 && strlen($newContent) > $this->maxApiChars) {
                $lastNews = array_pop($newsToInclude);
                $newContent = $introduction . implode('', $newsToInclude) . $ending;
                $this->info("Eliminando noticia adicional. Quedan " . count($newsToInclude) . " noticias.");
            }
            
            // Si aún excede, como último recurso acortar la introducción y la conclusión
            if (strlen($newContent) > $this->maxApiChars) {
                $this->warn("Aplicando reducción de emergencia a la introducción y conclusión.");
                $introduction = "Bienvenidos al resumen diario de " . $this->portalName . ". ";
                $ending = "Este ha sido el resumen diario. Gracias por escuchar.";
                $newContent = $introduction . implode('', $newsToInclude) . $ending;
            }
        }
        
        return $newContent;
    }
    
    /**
     * Convierte el texto a audio usando OpenAI TTS API con manejo de timeout y reintentos
     */
    private function convertToAudio($text, $identifier, $baseDir, $fullBaseDir, $timeout = 120, $maxRetries = 3)
    {
        // Si el texto es demasiado largo, aplicar ajustes para reducirlo
        if (strlen($text) > $this->maxApiChars) {
            $this->warn('El texto excede el límite de la API (' . strlen($text) . ' caracteres). Aplicando ajustes...');
            $text = $this->adjustContentToFitLimit($text);
            $this->info('Tamaño del texto después de ajustes: ' . strlen($text) . ' caracteres');
            
            // Verificar si aún excede el límite
            if (strlen($text) > $this->maxApiChars) {
                $this->error('No se pudo reducir el texto por debajo del límite máximo.');
                return null;
            }
        }
        
        $retryCount = 0;
        $lastException = null;
        
        // Pre-procesamiento adicional para mejorar la vocalización
        $text = $this->prepareTextForTTS($text);
        
        while ($retryCount < $maxRetries) {
            try {
                $apiKey = config('services.openai.api_key');
                
                if (empty($apiKey)) {
                    $this->error('No se ha configurado la clave API de OpenAI. Verifique el archivo .env');
                    return null;
                }
                
                $this->info('Enviando solicitud a OpenAI... (Intento ' . ($retryCount + 1) . ' de ' . $maxRetries . ')');
                $this->info('Timeout configurado: ' . $timeout . ' segundos');
                
                // Verificación final del tamaño del texto
                if (strlen($text) > $this->maxApiChars) {
                    $this->error('El texto excede el límite máximo de ' . $this->maxApiChars . ' caracteres.');
                    return null;
                }
                
                // Mostrar solo una muestra del texto
                $this->info('Muestra del texto a convertir: ' . substr($text, 0, 100) . '...');
                
                // Usar un cliente HTTP con timeout aumentado
                $response = Http::timeout($timeout)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/audio/speech', [
                    'model' => 'tts-1',
                    'input' => $text,
                    'voice' => 'nova', // Cambio a una voz más clara, opciones: alloy, echo, fable, onyx, nova, shimmer
                    'response_format' => 'mp3',
                    'speed' => 0.9, // Ligeramente más lento para mejor comprensión
                ]);
                
                if ($response->successful()) {
                    // Crear nombre de archivo único
                    $safeName = Str::slug($identifier);
                    
                    // Nombre de archivo para la base de datos (esto no cambia)
                    $fileName = $baseDir . '/' . $safeName . '.mp3';
                    
                    // Corregido: Guardar usando Storage de Laravel
                    try {
                        // Guardar el archivo usando Storage en lugar de file_put_contents
                        Storage::disk('public')->put($fileName, $response->body());
                        
                        $this->info('Archivo guardado correctamente en: ' . $fileName);
                        
                        // Verificar físicamente que el archivo existe
                        if (Storage::disk('public')->exists($fileName)) {
                            $fileSize = Storage::disk('public')->size($fileName);
                            $this->info('✓ Verificación: El archivo existe. Tamaño: ' . round($fileSize / 1024) . ' KB');
                            
                            // Usar formato de rutas con slash para almacenamiento en BD
                            return str_replace('\\', '/', $fileName);
                        } else {
                            $this->error('× El archivo no existe a pesar de reportar éxito');
                        }
                    } catch (\Exception $e) {
                        $this->error('Error al guardar archivo: ' . $e->getMessage());
                        Log::error('Error al guardar archivo: ' . $e->getMessage());
                    }
                } else {
                    $errorMsg = 'Error en la API de OpenAI: ' . $response->body();
                    $this->error($errorMsg);
                    Log::error($errorMsg);
                }
                
                // Si llegamos aquí, es porque hubo un error pero no una excepción
                $retryCount++;
                if ($retryCount < $maxRetries) {
                    $this->warn('Reintentando en 5 segundos...');
                    sleep(5);
                }
                
            } catch (\Exception $e) {
                $lastException = $e;
                $retryCount++;
                $this->error('Excepción: ' . $e->getMessage());
                Log::error('Error al convertir a audio: ' . $e->getMessage());
                
                if ($retryCount < $maxRetries) {
                    $this->warn('Reintentando en 5 segundos...');
                    sleep(5);
                }
            }
        }
        
        if ($lastException) {
            $this->error('Se agotaron los reintentos. Última excepción: ' . $lastException->getMessage());
        } else {
            $this->error('Se agotaron los reintentos sin éxito.');
        }
        
        return null;
    }
    
    /**
     * Prepara el texto para mejor pronunciación en TTS
     */
    private function prepareTextForTTS($text)
    {
        // Reemplazar abreviaturas y mejorar pronunciación
        $replacements = [
            // Abreviaturas comunes
            '/\bEE\.UU\./i' => 'Estados Unidos',
            '/\bUE\b/i' => 'Unión Europea',
            '/\bONU\b/i' => 'Organización de las Naciones Unidas',
            '/\bOMS\b/i' => 'Organización Mundial de la Salud',
            '/\bFMI\b/i' => 'Fondo Monetario Internacional',
            '/\bPIB\b/i' => 'Producto Interno Bruto',
            '/\bCEO\b/i' => 'Director Ejecutivo',
            '/\bCOVID-19\b/i' => 'COVID diecinueve',
            '/\bCOVID\b/i' => 'COVID',
            
            // Números y caracteres especiales
            '/(\d+)%/' => '$1 por ciento',
            '/(\d+),(\d+)/' => '$1 coma $2',
            '/\$(\d+)/' => '$1 dólares',
            '/€(\d+)/' => '$1 euros',
            
            // Agregar pausas estratégicas para mejorar la fluidez
            '/([^.!?]{60,})([,;])/' => '$1$2 ',
            
            // Reemplazar caracteres problemáticos
            '/&/' => ' y ',
            '/\//' => ' o ',
            
            // Asegurar pausas después de finales de oración
            '/\.\s+/' => '. ',
            '/\?\s+/' => '? ',
            '/\!\s+/' => '! ',
        ];
        
        // Aplicar reemplazos
        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        // Agregar pausas adicionales entre secciones principales
        $text = str_replace("En la sección de", " . En la sección de", $text);
        $text = str_replace("Este ha sido el resumen", " . Este ha sido el resumen", $text);
        $text = str_replace("Pasamos a la siguiente noticia", " . Pasamos a la siguiente noticia", $text);
        
        return $text;
    }
    
    /**
     * Obtiene la duración del archivo de audio (requiere extensión FFmpeg en servidor)
     */
    private function getAudioDuration($audioPath)
    {
        try {
            // Obtener la ruta completa del archivo
            $fullPath = Storage::disk('public')->path($audioPath);
            
            // Asegurarse de que el archivo existe antes de intentar obtener su duración
            if (!file_exists($fullPath)) {
                $this->warn("No se puede determinar la duración porque el archivo no existe: {$fullPath}");
                return 0;
            }
            
            // Verificar si ffprobe está disponible
            $testCommand = 'ffprobe -version';
            $output = shell_exec($testCommand);
            
            if ($output === null) {
                $this->warn("ffprobe no está disponible en este sistema. No se puede determinar la duración del audio.");
                return 0;
            }
            
            $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"{$fullPath}\"";
            $duration = shell_exec($command);
            
            if ($duration === null) {
                $this->warn("No se pudo ejecutar ffprobe. Asegúrese de que está instalado.");
                return 0;
            }
            
            return round(floatval($duration));
        } catch (\Exception $e) {
            $this->warn('No se pudo determinar la duración del audio: ' . $e->getMessage());
            Log::warning('No se pudo determinar la duración del audio: ' . $e->getMessage());
            return 0; // Si no se puede determinar, devolver cero
        }
    }
}