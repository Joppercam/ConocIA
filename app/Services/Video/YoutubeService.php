<?php

namespace App\Services\Video;

use App\Models\VideoPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class YoutubeService implements VideoServiceInterface
{
    protected $apiKey;
    protected $platform;
    
    public function __construct()
    {
        // Verificamos la plataforma en la base de datos
        $this->platform = VideoPlatform::where('code', 'youtube')->first();
        
        // Verificamos todas las posibles fuentes de la API key
        $dbKey = $this->platform ? $this->platform->api_key : null;
        $configKey = config('services.youtube.key');
        $envKey = env('YOUTUBE_API_KEY');
        
        // Registramos cada valor para diagnóstico
        Log::info('API Key desde DB: ' . ($dbKey ?: 'NULL'));
        Log::info('API Key desde config: ' . ($configKey ?: 'NULL'));
        Log::info('API Key desde .env: ' . ($envKey ?: 'NULL'));
        
        // Asignamos la primera que no sea nula
        $this->apiKey = $dbKey ?: $configKey ?: $envKey;
        
        // Verificamos el resultado final
        Log::info('API key final: ' . ($this->apiKey ? substr($this->apiKey, 0, 5) . '...' : 'NULL'));
    }
    
    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);
        $cacheKey = 'youtube_search_' . md5($query . '_' . $limit);
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query, $limit) {
            try {
                $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                    'key' => $this->apiKey,
                    'q' => $query,
                    'part' => 'snippet',
                    'type' => 'video',
                    'maxResults' => $limit,
                    'relevanceLanguage' => 'es'
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de YouTube: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['items'])) {
                    return [];
                }
                
                $videoIds = array_map(function($item) {
                    return $item['id']['videoId'];
                }, $results['items']);
                
                // Obtener más información de los videos (duración, etc.)
                $videoDetails = $this->getVideosDetails($videoIds);
                
                return $this->formatVideos($results['items'], $videoDetails);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de YouTube: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getPopular(int $limit = 5): array
    {
        $cacheKey = 'youtube_popular_' . $limit;
        
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($limit) {
            try {
                $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'key' => $this->apiKey,
                    'chart' => 'mostPopular',
                    'part' => 'snippet,contentDetails,statistics',
                    'maxResults' => $limit,
                    'regionCode' => 'ES' // Ajustar según tu región objetivo
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de YouTube: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['items'])) {
                    return [];
                }
                
                return $this->formatVideos($results['items'], $results['items']);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de YouTube (populares): ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getVideoInfo(string $videoId): ?array
    {
        $cacheKey = 'youtube_video_' . $videoId;
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($videoId) {
            try {
                // Usar la función helper global o directamente la API key de la clase
                $apiKey = $this->apiKey; // Utiliza la API key definida en el constructor
                
                Log::info('Intentando obtener información del video: ' . $videoId);
                Log::info('API Key siendo usada: ' . substr($apiKey, 0, 5) . '...');

                $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'key' => $apiKey,
                    'id' => $videoId,
                    'part' => 'snippet,contentDetails,statistics'
                ]);
                
                // Registrar la respuesta para depuración
                Log::info('Respuesta de la API de YouTube: ' . $response->body());
                
                // Verificar la respuesta
                if (!$response->successful()) {
                    Log::error('Error en la API: ' . $response->status() . ' - ' . $response->body());
                    return null;
                }
                
                // Procesar la respuesta JSON
                $data = $response->json();
                
                // Verificar si hay items en la respuesta
                if (empty($data['items'])) {
                    Log::warning('No se encontraron items para el video ID: ' . $videoId);
                    return null;
                }
                
                // Obtener el primer item (debería ser el único, ya que consultamos por ID)
                $videoData = $data['items'][0];
                
                // Procesar la duración con manejo de errores
                $durationSeconds = 0;
                try {
                    $duration = $videoData['contentDetails']['duration'] ?? 'PT0S';
                    
                    // Extraer duración usando expresiones regulares que es más seguro
                    $hours = 0;
                    $minutes = 0;
                    $seconds = 0;
                    
                    // Extraer horas
                    if (preg_match('/(\d+)H/', $duration, $matches)) {
                        $hours = intval($matches[1]);
                    }
                    
                    // Extraer minutos
                    if (preg_match('/(\d+)M/', $duration, $matches)) {
                        $minutes = intval($matches[1]);
                    }
                    
                    // Extraer segundos
                    if (preg_match('/(\d+)S/', $duration, $matches)) {
                        $seconds = intval($matches[1]);
                    }
                    
                    $durationSeconds = $hours * 3600 + $minutes * 60 + $seconds;
                    Log::info('Duración procesada: ' . $durationSeconds . ' segundos');
                    
                } catch (\Exception $e) {
                    Log::warning('Error al procesar la duración: ' . $e->getMessage());
                    // Continuamos con durationSeconds = 0
                }
                
                // Formatear la información del video
                $formattedData = [
                    'platform_id' => $this->platform ? $this->platform->id : null,
                    'external_id' => $videoId,
                    'title' => $videoData['snippet']['title'] ?? 'Sin título',
                    'description' => $videoData['snippet']['description'] ?? null,
                    'thumbnail_url' => isset($videoData['snippet']['thumbnails']) 
                        ? ($videoData['snippet']['thumbnails']['high']['url'] ?? 
                        $videoData['snippet']['thumbnails']['default']['url'] ?? null)
                        : null,
                    'embed_url' => 'https://www.youtube.com/embed/' . $videoId,
                    'original_url' => 'https://www.youtube.com/watch?v=' . $videoId,
                    'published_at' => isset($videoData['snippet']['publishedAt']) 
                        ? date('Y-m-d H:i:s', strtotime($videoData['snippet']['publishedAt']))
                        : now()->format('Y-m-d H:i:s'),
                    'duration_seconds' => $durationSeconds,
                    'view_count' => isset($videoData['statistics']) ? ($videoData['statistics']['viewCount'] ?? 0) : 0,
                    'keywords' => isset($videoData['snippet']['tags']) ? $videoData['snippet']['tags'] : []
                ];
                
                Log::info('Video formateado correctamente: ' . $videoId);
                return $formattedData;
                
            } catch (\Exception $e) {
                Log::error('Error al obtener información de video de YouTube: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return null;
            }
        });
    }
    
    protected function getVideosDetails(array $videoIds): array
    {
        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'key' => $this->apiKey,
                'id' => implode(',', $videoIds),
                'part' => 'contentDetails,statistics'
            ]);
            
            if (!$response->successful()) {
                return [];
            }
            
            $result = $response->json();
            
            if (empty($result['items'])) {
                return [];
            }
            
            // Indexar por ID para facilitar el acceso
            $details = [];
            foreach ($result['items'] as $item) {
                $details[$item['id']] = $item;
            }
            
            return $details;
            
        } catch (\Exception $e) {
            Log::error('Error al obtener detalles de videos de YouTube: ' . $e->getMessage());
            return [];
        }
    }
    
    protected function formatVideos(array $items, array $details): array
    {
        $videos = [];
        
        foreach ($items as $item) {
            $videoId = isset($item['id']['videoId']) ? $item['id']['videoId'] : $item['id'];
            $detail = $details[$videoId] ?? null;
            
            if ($detail) {
                // Convertir duración ISO 8601 a segundos
                $duration = $detail['contentDetails']['duration'] ?? 'PT0S';
                $interval = new \DateInterval(substr($duration, 2));
                $durationSeconds = $interval->h * 3600 + $interval->i * 60 + $interval->s;
            } else {
                $durationSeconds = 0;
            }
            
            $videos[] = [
                'platform_id' => $this->platform->id,
                'external_id' => $videoId,
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'] ?? null,
                'thumbnail_url' => $item['snippet']['thumbnails']['high']['url'] ?? $item['snippet']['thumbnails']['default']['url'],
                'embed_url' => 'https://www.youtube.com/embed/' . $videoId,
                'original_url' => 'https://www.youtube.com/watch?v=' . $videoId,
                'published_at' => date('Y-m-d H:i:s', strtotime($item['snippet']['publishedAt'])),
                'duration_seconds' => $durationSeconds,
                'view_count' => $detail['statistics']['viewCount'] ?? 0,
                'keywords' => $item['snippet']['tags'] ?? []
            ];
        }
        
        return $videos;
    }
}