<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Podcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenerateNewsPodcasts extends Command
{
    protected $signature = 'podcasts:generate {--days=1} {--limit=8}';
    protected $description = 'Genera podcasts de audio a partir de las noticias más leídas';

    public function handle()
    {
        $this->info('Iniciando generación de podcasts...');
        
        $days = $this->option('days');
        $limit = $this->option('limit');
        
        // Obtener las noticias más leídas usando el scope personalizado de su modelo
        $topNews = News::published()
                     ->where('created_at', '>=', now()->subDays($days))
                     ->orderBy('views', 'desc')
                     ->take($limit)
                     ->get();
                     
        $this->info('Se encontraron ' . $topNews->count() . ' noticias para convertir a audio');
        
        if ($topNews->isEmpty()) {
            $this->warn('No se encontraron noticias para el periodo especificado.');
            $this->warn('Pruebe con un periodo más largo usando --days=7');
            return;
        }
        
        // Asegurarnos que el directorio de destino existe
        $baseDir = 'podcasts' . DIRECTORY_SEPARATOR . date('Y-m-d');
        $fullBaseDir = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $baseDir));
        
        if (!file_exists($fullBaseDir)) {
            $this->info('Creando directorio: ' . $fullBaseDir);
            if (!mkdir($fullBaseDir, 0777, true)) {
                $this->error('No se pudo crear el directorio de destino');
                return;
            }
        }
        
        foreach ($topNews as $news) {
            $this->info('Procesando noticia: ' . $news->title);
            
            // Verificar si ya existe un podcast para esta noticia
            $existingPodcast = Podcast::where('news_id', $news->id)->first();
            
            if ($existingPodcast) {
                $this->info('Esta noticia ya tiene un podcast. Omitiendo...');
                continue;
            }
            
            // Preparar el contenido para la conversión a audio
            $content = $this->prepareContent($news);
            
            // Convertir a audio utilizando OpenAI TTS
            $audioPath = $this->convertToAudio($content, $news->id, $baseDir, $fullBaseDir);
            
            if ($audioPath) {
                // Crear registro de podcast
                Podcast::create([
                    'news_id' => $news->id,
                    'title' => $news->title,
                    'audio_path' => $audioPath,
                    'duration' => $this->getAudioDuration($audioPath),
                    'play_count' => 0,
                    'voice' => 'alloy',
                    'published_at' => now(),
                ]);
                
                $this->info('Podcast generado con éxito para: ' . $news->title);
            } else {
                $this->error('Error al generar el podcast para: ' . $news->title);
            }
        }
        
        $this->info('Proceso de generación de podcasts completado');
    }
    
    /**
     * Prepara el texto de la noticia para convertirlo a audio
     */
    private function prepareContent($news)
    {
        // Eliminar HTML y formatear el contenido
        $content = strip_tags($news->content);
        
        // Agregar una introducción al podcast
        $introduction = "A continuación escuchará la noticia titulada: {$news->title}. ";
        
        // Si tiene resumen, usarlo como introducción
        if (!empty($news->summary)) {
            $introduction .= "Un breve resumen: " . strip_tags($news->summary) . " ";
        }
        
        // Si tiene categoría, mencionarla
        if ($news->category) {
            $introduction .= "Esta noticia pertenece a la categoría de {$news->category->name}. ";
        }
        
        // Agregar un cierre al podcast
        $ending = " Esta ha sido una noticia de nuestro portal. ";
        
        if ($news->author) {
            if (is_object($news->author)) {
                $ending .= "Escrita por {$news->author->name}. ";
            } else {
                $ending .= "Escrita por {$news->author}. ";
            }
        }
        
        
        $ending .= "Gracias por escuchar.";
        
        return $introduction . $content . $ending;
    }
    
    /**
     * Convierte el texto a audio usando OpenAI TTS API - Optimizado para Windows
     */
    private function convertToAudio($text, $newsId, $baseDir, $fullBaseDir)
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            if (empty($apiKey)) {
                $this->error('No se ha configurado la clave API de OpenAI. Verifique el archivo .env');
                return null;
            }
            
            $this->info('Enviando solicitud a OpenAI...');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'tts-1',
                'input' => $text,
                'voice' => 'alloy', // Puedes elegir entre: alloy, echo, fable, onyx, nova, shimmer
                'response_format' => 'mp3',
            ]);
            
            if ($response->successful()) {
                // Crear nombre de archivo único
                $safeName = Str::slug($newsId) . '-' . Str::random(8);
                $fileName = $baseDir . DIRECTORY_SEPARATOR . $safeName . '.mp3';
                $fullPath = $fullBaseDir . DIRECTORY_SEPARATOR . $safeName . '.mp3';
                
                $this->info("Guardando archivo en: {$fullPath}");
                
                // Guardar el archivo usando file_put_contents para mayor compatibilidad con Windows
                if (file_put_contents($fullPath, $response->body()) !== false) {
                    $this->info('Archivo guardado correctamente');
                    
                    // Verificar físicamente que el archivo existe
                    if (file_exists($fullPath)) {
                        $this->info('✓ Verificación física: El archivo existe');
                        $this->info('Tamaño: ' . round(filesize($fullPath) / 1024) . ' KB');
                        
                        // Convertir las barras invertidas a diagonales para almacenamiento en BD
                        return str_replace('\\', '/', $fileName);
                    } else {
                        $this->error('× El archivo no existe a pesar de reportar éxito');
                        return null;
                    }
                } else {
                    $this->error('Error al guardar el archivo de audio');
                    return null;
                }
            } else {
                $this->error('Error en la API de OpenAI: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            $this->error('Excepción: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtiene la duración del archivo de audio (requiere extensión FFmpeg en servidor)
     */
    private function getAudioDuration($audioPath)
    {
        try {
            $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $audioPath));
            
            // Asegurarse de que el archivo existe antes de intentar obtener su duración
            if (!file_exists($fullPath)) {
                $this->warn("No se puede determinar la duración porque el archivo no existe: {$fullPath}");
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
            return 0; // Si no se puede determinar, devolver cero
        }
    }
}