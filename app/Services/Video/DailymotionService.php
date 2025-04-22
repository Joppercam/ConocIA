<?php

namespace App\Services\Video;

use App\Models\VideoPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DailymotionService implements VideoServiceInterface
{
    protected $apiKey;
    protected $platform;
    
    public function __construct()
    {
        $this->platform = VideoPlatform::where('code', 'dailymotion')->first();
        $this->apiKey = $this->platform ? $this->platform->api_key : config('services.dailymotion.key');
    }
    
    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);
        $cacheKey = 'dailymotion_search_' . md5($query . '_' . $limit);
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query, $limit) {
            try {
                $response = Http::get('https://api.dailymotion.com/videos', [
                    'search' => $query,
                    'limit' => $limit,
                    'fields' => 'id,title,description,thumbnail_720_url,embed_url,url,created_time,duration,views_total,tags',
                    'sort' => 'relevance'
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de Dailymotion: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['list'])) {
                    return [];
                }
                
                return $this->formatVideos($results['list']);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de Dailymotion: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getPopular(int $limit = 5): array
    {
        $cacheKey = 'dailymotion_popular_' . $limit;
        
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($limit) {
            try {
                $response = Http::get('https://api.dailymotion.com/videos', [
                    'limit' => $limit,
                    'fields' => 'id,title,description,thumbnail_720_url,embed_url,url,created_time,duration,views_total,tags',
                    'sort' => 'trending'
                ]);
                
                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de Dailymotion: ' . $response->body());
                    return [];
                }
                
                $results = $response->json();
                
                if (empty($results['list'])) {
                    return [];
                }
                
                return $this->formatVideos($results['list']);
                
            } catch (\Exception $e) {
                Log::error('Error en la API de Dailymotion (populares): ' . $e->getMessage());
                return [];
            }
        });
    }
    
    public function getVideoInfo(string $videoId): ?array
    {
        $cacheKey = 'dailymotion_video_' . $videoId;
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($videoId) {
            try {
                $response = Http::get('https://api.dailymotion.com/video/' . $videoId, [
                    'fields' => 'id,title,description,thumbnail_720_url,embed_url,url,created_time,duration,views_total,tags'
                ]);
                
                if (!$response->successful()) {
                    return null;
                }
                
                $videoData = $response->json();
                
                return [
                    'platform_id' => $this->platform->id,
                    'external_id' => $videoId,
                    'title' => $videoData['title'],
                    'description' => $videoData['description'] ?? null,
                    'thumbnail_url' => $videoData['thumbnail_720_url'] ?? null,
                    'embed_url' => $videoData['embed_url'],
                    'original_url' => $videoData['url'],
                    'published_at' => date('Y-m-d H:i:s', $videoData['created_time']),
                    'duration_seconds' => $videoData['duration'],
                    'view_count' => $videoData['views_total'] ?? 0,
                    'keywords' => $videoData['tags'] ?? []
                ];
                
            } catch (\Exception $e) {
                Log::error('Error al obtener información de video de Dailymotion: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    protected function formatVideos(array $items): array
    {
        $videos = [];
        
        foreach ($items as $item) {
            $videos[] = [
                'platform_id' => $this->platform->id,
                'external_id' => $item['id'],
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'thumbnail_url' => $item['thumbnail_720_url'] ?? null,
                'embed_url' => $item['embed_url'],
                'original_url' => $item['url'],
                'published_at' => date('Y-m-d H:i:s', $item['created_time']),
                'duration_seconds' => $item['duration'],
                'view_count' => $item['views_total'] ?? 0,
                'keywords' => $item['tags'] ?? []
            ];
        }
        
        return $videos;
    }
}