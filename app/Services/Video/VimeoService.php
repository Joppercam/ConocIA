<?php

namespace App\Services\Video;

use App\Models\VideoPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VimeoService implements VideoServiceInterface
{
    protected $accessToken;
    protected $platform;
    
    public function __construct()
    {
        $this->platform = VideoPlatform::where('code', 'vimeo')->first();
        $this->accessToken = $this->platform ? $this->platform->api_key : config('services.vimeo.access_token');
    }
    
    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);
        $cacheKey = 'vimeo_search_' . md5($query . '_' . $limit);
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query, $limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken
                ])->get('https://api.vimeo.com/videos', [
                    'query' => $query,
                    'per_page' => $limit,
                    'sort' => 'relevant',
                    'fields' => 'uri,name,description,pictures,link,duration,created_time,stats'
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de Vimeo: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['data'])) {
                    return [];
                }
                
                return $this->formatVideos($results['data']);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de Vimeo: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getPopular(int $limit = 5): array
    {
        $cacheKey = 'vimeo_popular_' . $limit;
        
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken
                ])->get('https://api.vimeo.com/videos', [
                    'per_page' => $limit,
                    'sort' => 'popularity',
                    'fields' => 'uri,name,description,pictures,link,duration,created_time,stats',
                    'filter' => 'featured'
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de Vimeo: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['data'])) {
                    return [];
                }
                
                return $this->formatVideos($results['data']);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de Vimeo (populares): ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getVideoInfo(string $videoId): ?array
    {
        $cacheKey = 'vimeo_video_' . $videoId;
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($videoId) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken
                ])->get('https://api.vimeo.com/videos/' . $videoId, [
                    'fields' => 'uri,name,description,pictures,link,duration,created_time,stats,tags'
                ]);
                
                if (!$response->successful()) {
                    return null;
                }
                
                $videoData = $response->json();
                
                // Extraer ID de la URL
                $uri = $videoData['uri'];
                $externalId = substr($uri, strrpos($uri, '/') + 1);
                
                // Obtener palabras clave de los tags
                $keywords = [];
                if (!empty($videoData['tags'])) {
                    foreach ($videoData['tags'] as $tag) {
                        $keywords[] = $tag['name'];
                    }
                }
                
                return [
                    'platform_id' => $this->platform->id,
                    'external_id' => $externalId,
                    'title' => $videoData['name'],
                    'description' => $videoData['description'] ?? null,
                    'thumbnail_url' => $videoData['pictures']['sizes'][3]['link'] ?? $videoData['pictures']['sizes'][0]['link'],
                    'embed_url' => 'https://player.vimeo.com/video/' . $externalId,
                    'original_url' => $videoData['link'],
                    'published_at' => date('Y-m-d H:i:s', strtotime($videoData['created_time'])),
                    'duration_seconds' => $videoData['duration'],
                    'view_count' => $videoData['stats']['plays'] ?? 0,
                    'keywords' => $keywords
                ];
                
            } catch (\Exception $e) {
                Log::error('Error al obtener información de video de Vimeo: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    protected function formatVideos(array $items): array
    {
        $videos = [];
        
        foreach ($items as $item) {
            // Extraer ID de la URL
            $uri = $item['uri'];
            $videoId = substr($uri, strrpos($uri, '/') + 1);
            
            // Obtener palabras clave de los tags si están disponibles
            $keywords = [];
            if (!empty($item['tags'])) {
                foreach ($item['tags'] as $tag) {
                    $keywords[] = $tag['name'];
                }
            }
            
            $videos[] = [
                'platform_id' => $this->platform->id,
                'external_id' => $videoId,
                'title' => $item['name'],
                'description' => $item['description'] ?? null,
                'thumbnail_url' => $item['pictures']['sizes'][3]['link'] ?? $item['pictures']['sizes'][0]['link'],
                'embed_url' => 'https://player.vimeo.com/video/' . $videoId,
                'original_url' => $item['link'],
                'published_at' => date('Y-m-d H:i:s', strtotime($item['created_time'])),
                'duration_seconds' => $item['duration'],
                'view_count' => $item['stats']['plays'] ?? 0,
                'keywords' => $keywords
            ];
        }
        
        return $videos;
    }
}