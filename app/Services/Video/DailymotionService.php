<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailymotionService extends AbstractVideoService
{
    private const FIELDS = 'id,title,description,thumbnail_720_url,embed_url,url,created_time,duration,views_total,tags';

    public function __construct()
    {
        $this->resolvePlatform('dailymotion', 'services.dailymotion.key');
    }

    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);

        return $this->cachedApiCall(
            'dailymotion_search_' . md5($query . '_' . $limit),
            now()->addHours(6),
            function () use ($query, $limit) {
                $response = Http::get('https://api.dailymotion.com/videos', [
                    'search' => $query,
                    'limit'  => $limit,
                    'fields' => self::FIELDS,
                    'sort'   => 'relevance',
                ]);

                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de Dailymotion: ' . $response->body());
                    return [];
                }

                return $this->formatVideos($response->json()['list'] ?? []);
            }
        );
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->cachedApiCall(
            'dailymotion_popular_' . $limit,
            now()->addHours(12),
            function () use ($limit) {
                $response = Http::get('https://api.dailymotion.com/videos', [
                    'limit'  => $limit,
                    'fields' => self::FIELDS,
                    'sort'   => 'trending',
                ]);

                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de Dailymotion: ' . $response->body());
                    return [];
                }

                return $this->formatVideos($response->json()['list'] ?? []);
            }
        );
    }

    public function getVideoInfo(string $videoId): ?array
    {
        return $this->cachedApiCall(
            'dailymotion_video_' . $videoId,
            now()->addDays(1),
            function () use ($videoId) {
                $response = Http::get('https://api.dailymotion.com/video/' . $videoId, [
                    'fields' => self::FIELDS,
                ]);

                if (!$response->successful()) {
                    return null;
                }

                $data = $response->json();

                return [
                    'platform_id'      => $this->platform?->id,
                    'external_id'      => $videoId,
                    'title'            => $data['title'],
                    'description'      => $data['description'] ?? null,
                    'thumbnail_url'    => $data['thumbnail_720_url'] ?? null,
                    'embed_url'        => $data['embed_url'],
                    'original_url'     => $data['url'],
                    'published_at'     => date('Y-m-d H:i:s', $data['created_time']),
                    'duration_seconds' => $data['duration'],
                    'view_count'       => $data['views_total'] ?? 0,
                    'keywords'         => $data['tags'] ?? [],
                ];
            },
            null
        );
    }

    protected function formatVideos(array $items): array
    {
        return array_map(fn($item) => [
            'platform_id'      => $this->platform?->id,
            'external_id'      => $item['id'],
            'title'            => $item['title'],
            'description'      => $item['description'] ?? null,
            'thumbnail_url'    => $item['thumbnail_720_url'] ?? null,
            'embed_url'        => $item['embed_url'],
            'original_url'     => $item['url'],
            'published_at'     => date('Y-m-d H:i:s', $item['created_time']),
            'duration_seconds' => $item['duration'],
            'view_count'       => $item['views_total'] ?? 0,
            'keywords'         => $item['tags'] ?? [],
        ], $items);
    }
}
