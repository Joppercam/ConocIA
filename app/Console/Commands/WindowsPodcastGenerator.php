<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Podcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WindowsPodcastGenerator extends Command
{
    protected $signature = 'podcasts:windows {news_id?} {--force : Forzar regeneración incluso si ya existe}';
    protected $description = 'Genera un podcast usando métodos optimizados para Windows';

    protected $podcastsDir;
    protected $fullPodcastsDir;

    public function __construct()
    {
        parent::__construct();
        
        // Usar rutas con formato Windows
        $this->podcastsDir = 'podcasts\\windows';
        $this->fullPodcastsDir = str_replace('/', '\\', storage_path('app\\public\\' . str_replace('\\', '\\\\', $this->podcastsDir)));
    }

    public function handle()
    {
        $this->info('Iniciando generación de podcast para Windows');
        
        // Paso 1: Verificar y crear directorios
        $this->setupDirectories();
        
        // Paso 2: Obtener la noticia
        $newsId = $this->argument('news_id');
        $news = $this->getNewsArticle($newsId);
        if (!$news) {
            return 1;
        }
        
        // Paso 3: Verificar si ya existe un podcast para esta noticia
        if (!$this->checkExistingPodcast($news)) {
            return 1;
        }
        
        // Paso 4: Preparar el contenido para el podcast
        $content = $this->prepareContent($news);
        $this->info('Contenido preparado. Longitud: ' . strlen($content) . ' caracteres');
        
        // Paso 5: Generar el podcast
        $audioPath = $this->generateAudio($content, $news);
        if (!$audioPath) {
            $this->error('No se pudo generar el podcast');
            return 1;
        }
        
        // Paso 6: Crear el registro en la base de datos
        $podcast = $this->createPodcastRecord($news, $audioPath);
        if (!$podcast) {
            $this->error('No se pudo crear el registro del podcast');
            return 1;
        }
        
        $this->info('Podcast generado exitosamente');
        $this->info('URL: ' . asset('storage/' . str_replace('\\', '/', $audioPath)));
        
        return 0;
    }
    
    protected function setupDirectories()
    {
        $this->info('Verificando directorios...');
        
        if (!file_exists($this->fullPodcastsDir)) {
            $this->info("Creando directorio: {$this->fullPodcastsDir}");
            
            if (!mkdir($this->fullPodcastsDir, 0777, true)) {
                $this->error("No se pudo crear el directorio: {$this->fullPodcastsDir}");
                $this->info("Intente crear manualmente el directorio usando CMD:");
                $this->line("mkdir \"{$this->fullPodcastsDir}\"");
                exit(1);
            }
        }
        
        $this->info("Directorio de podcasts: {$this->fullPodcastsDir} ✓");
    }
    
    protected function getNewsArticle($newsId)
    {
        if (!$newsId) {
            $news = News::published()->orderBy('views', 'desc')->first();
            if (!$news) {
                $this->error('No se encontraron noticias para convertir a podcast');
                return null;
            }
            $this->info("Usando la noticia más vista: {$news->title} (ID: {$news->id})");
        } else {
            $news = News::find($newsId);
            if (!$news) {
                $this->error("No se encontró la noticia con ID: {$newsId}");
                return null;
            }
            $this->info("Usando la noticia: {$news->title}");
        }
        
        return $news;
    }
    
    protected function checkExistingPodcast($news)
    {
        $existingPodcast = Podcast::where('news_id', $news->id)->first();
        
        if ($existingPodcast && !$this->option('force')) {
            $this->warn("Esta noticia ya tiene un podcast asociado.");
            if (!$this->confirm('¿Desea regenerar el podcast?')) {
                $this->info('Operación cancelada.');
                return false;
            }
            
            $this->info("Eliminando podcast anterior...");
            
            // Intentar eliminar el archivo físico si existe
            $oldPath = str_replace('/', '\\', storage_path('app\\public\\' . str_replace('/', '\\', $existingPodcast->audio_path)));
            if (file_exists($oldPath)) {
                @unlink($oldPath);
                $this->info("Archivo anterior eliminado: {$oldPath}");
            }
            
            // Eliminar el registro
            $existingPodcast->delete();
            $this->info("Registro anterior eliminado de la base de datos");
        }
        
        return true;
    }
    
    protected function prepareContent($news)
    {
        // Para prueba, usar un texto más corto
        $content = strip_tags($news->content);
        if (strlen($content) > 1000) {
            $content = substr($content, 0, 1000) . '...';
        }
        
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
    
    protected function generateAudio($text, $news)
    {
        $this->info('Generando audio con OpenAI...');
        
        // Verificar API key
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            $this->error('No se ha configurado la clave API de OpenAI. Verifique el archivo .env');
            return null;
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'tts-1',
                'input' => $text,
                'voice' => 'alloy',
                'response_format' => 'mp3',
            ]);
            
            if (!$response->successful()) {
                $this->error('Error en la API de OpenAI: ' . $response->body());
                return null;
            }
            
            $this->info('Respuesta de audio recibida: ' . strlen($response->body()) . ' bytes');
            
            // Generar nombre de archivo único con formato Windows
            $safeName = Str::slug($news->id) . '-' . Str::random(8);
            $fileName = $this->podcastsDir . '\\' . $safeName . '.mp3';
            $fullPath = str_replace('/', '\\', storage_path('app\\public\\' . str_replace('\\', '\\\\', $fileName)));
            
            $this->info("Guardando archivo en: {$fullPath}");
            
            // Guardar el archivo usando escritura directa
            if (file_put_contents($fullPath, $response->body()) !== false) {
                $this->info('Archivo guardado exitosamente ✓');
                
                // Verificar que el archivo exista
                if (file_exists($fullPath)) {
                    $this->info('Verificación: El archivo existe físicamente ✓');
                    $this->info('Tamaño: ' . round(filesize($fullPath) / 1024) . ' KB');
                    
                    // Devolver la ruta relativa con formato de base de datos (usando /)
                    return str_replace('\\', '/', $fileName);
                } else {
                    $this->error('El archivo no existe después de guardarlo');
                    return null;
                }
            } else {
                $this->error('No se pudo guardar el archivo de audio');
                return null;
            }
        } catch (\Exception $e) {
            $this->error('Error al generar el audio: ' . $e->getMessage());
            return null;
        }
    }
    
    protected function createPodcastRecord($news, $audioPath)
    {
        $this->info('Creando registro en la base de datos...');
        
        try {
            $podcast = Podcast::create([
                'news_id' => $news->id,
                'title' => $news->title,
                'audio_path' => $audioPath,
                'duration' => 0, // Se actualizará después si es posible
                'play_count' => 0,
                'voice' => 'alloy',
                'published_at' => now(),
            ]);
            
            $this->info('Registro creado con ID: ' . $podcast->id);
            
            // Intentar obtener la duración si ffprobe está disponible
            try {
                $fullPath = storage_path('app/public/' . $audioPath);
                $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"{$fullPath}\"";
                $duration = shell_exec($command);
                
                if ($duration) {
                    $podcast->duration = round(floatval($duration));
                    $podcast->save();
                    $this->info('Duración actualizada: ' . $podcast->duration . ' segundos');
                }
            } catch (\Exception $e) {
                $this->warn('No se pudo determinar la duración del audio');
            }
            
            return $podcast;
        } catch (\Exception $e) {
            $this->error('Error al crear el registro: ' . $e->getMessage());
            return null;
        }
    }
}