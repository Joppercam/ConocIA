<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Podcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TestPodcastGeneration extends Command
{
    protected $signature = 'podcasts:test {news_id?} {--debug : Mostrar información detallada de depuración}';
    protected $description = 'Prueba la generación de un podcast para una noticia específica';

    public function handle()
    {
        $this->info('Iniciando prueba de generación de podcast...');
        $debug = $this->option('debug');
        
        // Verificar si storage link existe
        if (!file_exists(public_path('storage'))) {
            $this->warn('El enlace simbólico de storage no existe. Creando...');
            $this->call('storage:link');
        }
        
        // Verificar directorios de almacenamiento
        $directory = 'podcasts/test';
        $storagePath = storage_path('app/public/' . $directory);
        if (!file_exists($storagePath)) {
            $this->warn("Directorio no encontrado: $directory. Creando...");
            if (!File::makeDirectory($storagePath, 0775, true)) {
                $this->error("Error: No se pudo crear el directorio $storagePath");
                $this->error("Intente crear manualmente el directorio y establecer permisos:");
                $this->line("mkdir -p $storagePath");
                $this->line("chmod -R 775 $storagePath");
                return 1;
            }
        }
        
        // Prueba rápida de escritura
        $testFile = $directory . '/test_' . time() . '.txt';
        try {
            Storage::put('public/' . $testFile, 'Test de escritura');
            if (file_exists(storage_path('app/public/' . $testFile))) {
                $this->info("Prueba de escritura exitosa ✓");
                // Eliminar archivo de prueba
                Storage::delete('public/' . $testFile);
            } else {
                $this->error("Error: El archivo de prueba no existe físicamente después de guardarlo");
                $this->error("Verifique los permisos y la configuración del sistema de archivos");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error en prueba de escritura: " . $e->getMessage());
            return 1;
        }
        
        // Obtener ID de noticia de los argumentos o pedir al usuario
        $newsId = $this->argument('news_id');
        if (!$newsId) {
            // Obtener el ID de la última noticia publicada
            $latest = News::published()->latest('published_at')->first();
            if (!$latest) {
                $this->error('No hay noticias publicadas en la base de datos.');
                return 1;
            }
            $newsId = $latest->id;
            $this->info("Usando la última noticia publicada (ID: {$newsId}, título: {$latest->title})");
        }
        
        // Buscar la noticia
        $news = News::find($newsId);
        if (!$news) {
            $this->error("No se encontró la noticia con ID {$newsId}");
            return 1;
        }
        
        $this->info("Procesando noticia: {$news->title}");
        
        // Verificar existencia de podcast
        $existingPodcast = Podcast::where('news_id', $news->id)->first();
        if ($existingPodcast) {
            if (!$this->confirm('Esta noticia ya tiene un podcast. ¿Desea generar uno nuevo?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
            $existingPodcast->delete();
            $this->info('Podcast anterior eliminado.');
        }
        
        // Preparar contenido para test (versión corta)
        $content = $this->prepareShortContent($news);
        $this->info('Contenido preparado. Longitud: ' . strlen($content) . ' caracteres');
        
        // Mostrar el contenido a procesar y pedir confirmación
        $this->line('Primeros 200 caracteres del contenido a procesar:');
        $this->line(substr($content, 0, 200) . '...');
        
        if (!$this->confirm('¿Desea continuar con la generación del podcast?')) {
            $this->info('Operación cancelada.');
            return 0;
        }
        
        // Verificar API key
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            $this->error('No se ha configurado la clave API de OpenAI. Verifique el archivo .env');
            return 1;
        }
        
        // Convertir a audio usando OpenAI TTS
        $this->info('Convirtiendo a audio...');
        $audioPath = $this->convertToAudio($content, $news->id, $debug);
        
        if ($audioPath) {
            // Verificar físicamente si el archivo existe
            $fullPath = storage_path('app/public/' . $audioPath);
            if (!file_exists($fullPath)) {
                $this->error("Error: El archivo de audio no se creó físicamente en: $fullPath");
                $this->error("A pesar de que el método de guardado reportó éxito.");
                $this->error("Verifique los permisos del directorio y la configuración del sistema de archivos.");
                return 1;
            }
            
            $this->info("Archivo de audio confirmado físicamente en: $fullPath ✓");
            $fileSize = filesize($fullPath);
            $this->info("Tamaño del archivo: " . round($fileSize / 1024) . " KB");
            
            // Crear registro de podcast
            Podcast::create([
                'news_id' => $news->id,
                'title' => $news->title,
                'audio_path' => $audioPath,
                'duration' => 0, // Se actualizará después
                'play_count' => 0,
                'voice' => 'alloy',
                'published_at' => now(),
            ]);
            
            $this->info('Podcast generado con éxito para: ' . $news->title);
            $this->info('Ruta del archivo: ' . $audioPath);
            $this->info('URL accesible: ' . asset('storage/' . $audioPath));
            
            // Intentar detectar la duración
            try {
                $duration = $this->getAudioDuration($audioPath);
                if ($duration > 0) {
                    // Actualizar duración
                    Podcast::where('news_id', $news->id)->update(['duration' => $duration]);
                    $this->info("Duración del audio: " . gmdate("i:s", $duration));
                }
            } catch (\Exception $e) {
                $this->warn("No se pudo determinar la duración: " . $e->getMessage());
            }
        } else {
            $this->error('Error al generar el podcast para: ' . $news->title);
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Prepara una versión corta del contenido para pruebas
     */
    private function prepareShortContent($news)
    {
        // Versión corta para prueba
        $content = strip_tags($news->content);
        if (strlen($content) > 1000) {
            $content = substr($content, 0, 1000) . '...';
        }
        
        $introduction = "Esta es una prueba de podcast para la noticia titulada: {$news->title}. ";
        $ending = " Fin de la prueba.";
        
        return $introduction . $content . $ending;
    }
    
    /**
     * Convierte el texto a audio usando OpenAI TTS API
     */
    private function convertToAudio($text, $newsId, $debug = false)
    {
        try {
            $apiKey = config('services.openai.api_key');
            
            $this->info('Enviando solicitud a OpenAI...');
            
            // Si debug está activado, mostrar la longitud del texto
            if ($debug) {
                $this->line("Texto a convertir (longitud: " . strlen($text) . " caracteres):");
                $this->line(substr($text, 0, 100) . "...");
                $this->line("API Key (primeros 5 caracteres): " . substr($apiKey, 0, 5) . "...");
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/audio/speech', [
                'model' => 'tts-1',
                'input' => $text,
                'voice' => 'alloy',
                'response_format' => 'mp3',
            ]);
            
            if ($response->successful()) {
                // Si debug está activado, mostrar información de la respuesta
                if ($debug) {
                    $this->line("Respuesta exitosa de OpenAI");
                    $this->line("Código de estado: " . $response->status());
                    $this->line("Tamaño de la respuesta: " . strlen($response->body()) . " bytes");
                    $this->line("Headers: " . json_encode($response->headers()));
                }
                
                // Asegurar que el directorio existe
                $directory = 'podcasts/test';
                if (!Storage::exists('public/' . $directory)) {
                    Storage::makeDirectory('public/' . $directory);
                }
                
                $fileName = $directory . '/test-' . date('Y-m-d-His') . '-' . Str::slug($newsId) . '.mp3';
                $fullPath = storage_path('app/public/' . $fileName);
                
                // Dos métodos de guardado para mayor seguridad
                $saved = false;
                
                // Método 1: Usando Storage Facade
                if (Storage::put('public/' . $fileName, $response->body())) {
                    $this->info('Audio guardado correctamente usando Storage Facade.');
                    $saved = true;
                } else {
                    $this->warn('No se pudo guardar usando Storage Facade. Intentando método alternativo...');
                }
                
                // Método 2: Si el primero falla, usar file_put_contents directamente
                if (!$saved) {
                    // Asegurar que el directorio existe
                    $dirPath = dirname($fullPath);
                    if (!file_exists($dirPath)) {
                        mkdir($dirPath, 0775, true);
                    }
                    
                    if (file_put_contents($fullPath, $response->body()) !== false) {
                        $this->info('Audio guardado correctamente usando file_put_contents.');
                        $saved = true;
                    } else {
                        $this->error('No se pudo guardar el archivo usando ningún método.');
                    }
                }
                
                // Verificar si el archivo existe después de guardarlo
                if (file_exists($fullPath)) {
                    $this->info('Verificación: El archivo existe en la ruta esperada.');
                } else {
                    $this->error('El archivo no existe en la ruta esperada a pesar de intentar guardarlo.');
                    if ($debug) {
                        $this->line("Ruta completa verificada: $fullPath");
                        $this->line("Permisos del directorio: " . substr(sprintf('%o', fileperms(dirname($fullPath))), -4));
                    }
                    return null;
                }
                
                return $fileName;
            } else {
                $this->error('Error en la API de OpenAI: ' . $response->status() . ' - ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            $this->error('Excepción: ' . $e->getMessage());
            if ($debug) {
                $this->error($e->getTraceAsString());
            }
            return null;
        }
    }
    
    /**
     * Obtiene la duración del archivo de audio (requiere extensión FFmpeg en servidor)
     */
    private function getAudioDuration($audioPath)
    {
        try {
            $fullPath = storage_path('app/public/' . $audioPath);
            $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"{$fullPath}\"";
            $duration = shell_exec($command);
            
            return round(floatval($duration));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}