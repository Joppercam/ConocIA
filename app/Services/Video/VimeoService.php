<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VimeoService extends AbstractVideoService
{
    public function __construct()
    {
        if (Schema::hasTable('video_platforms')) {
            $this->resolvePlatform('vimeo', 'services.vimeo.access_token');
        } else {
            $this->apiKey = config('services.vimeo.access_token', '');
        }
    }

    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);

        return $this->cachedApiCall(
            'vimeo_search_' . md5($query . '_' . $limit),
            now()->addHours(6),
            function () use ($query, $limit) {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                    ->get('https://api.vimeo.com/videos', [
                        'query'  => $query,
                        'per_page' => $limit,
                        'sort'   => 'relevant',
                        'fields' => 'uri,name,description,pictures,link,duration,created_time,stats',
                    ]);

                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de Vimeo: ' . $response->body());
                    return [];
                }

                return $this->formatVideos($response->json()['data'] ?? []);
            }
        );
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->cachedApiCall(
            'vimeo_popular_' . $limit,
            now()->addHours(12),
            function () use ($limit) {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                    ->get('https://api.vimeo.com/videos', [
                        'per_page' => $limit,
                        'sort'     => 'popularity',
                        'fields'   => 'uri,name,description,pictures,link,duration,created_time,stats',
                        'filter'   => 'featured',
                    ]);

                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de Vimeo: ' . $response->body());
                    return [];
                }

                return $this->formatVideos($response->json()['data'] ?? []);
            }
        );
    }

    public function getVideoInfo(string $videoId): ?array
    {
        return $this->cachedApiCall(
            'vimeo_video_' . $videoId,
            now()->addDays(1),
            function () use ($videoId) {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                    ->get('https://api.vimeo.com/videos/' . $videoId, [
                        'fields' => 'uri,name,description,pictures,link,duration,created_time,stats,tags',
                    ]);

                if (!$response->successful()) {
                    return null;
                }

                $data       = $response->json();
                $externalId = substr($data['uri'], strrpos($data['uri'], '/') + 1);
                $keywords   = array_column($data['tags'] ?? [], 'name');

                return [
                    'platform_id'      => $this->platform?->id,
                    'external_id'      => $externalId,
                    'title'            => $data['name'],
                    'description'      => $data['description'] ?? null,
                    'thumbnail_url'    => $data['pictures']['sizes'][3]['link'] ?? $data['pictures']['sizes'][0]['link'] ?? null,
                    'embed_url'        => 'https://player.vimeo.com/video/' . $externalId,
                    'original_url'     => $data['link'],
                    'published_at'     => date('Y-m-d H:i:s', strtotime($data['created_time'])),
                    'duration_seconds' => $data['duration'],
                    'view_count'       => $data['stats']['plays'] ?? 0,
                    'keywords'         => $keywords,
                ];
            },
            null
        );
    }

    protected function formatVideos(array $items): array
    {
        return array_map(function ($item) {
            $videoId  = substr($item['uri'], strrpos($item['uri'], '/') + 1);
            $keywords = array_column($item['tags'] ?? [], 'name');

            return [
                'platform_id'      => $this->platform?->id,
                'external_id'      => $videoId,
                'title'            => $item['name'],
                'description'      => $item['description'] ?? null,
                'thumbnail_url'    => $item['pictures']['sizes'][3]['link'] ?? $item['pictures']['sizes'][0]['link'] ?? null,
                'embed_url'        => 'https://player.vimeo.com/video/' . $videoId,
                'original_url'     => $item['link'],
                'published_at'     => date('Y-m-d H:i:s', strtotime($item['created_time'])),
                'duration_seconds' => $item['duration'],
                'view_count'       => $item['stats']['plays'] ?? 0,
                'keywords'         => $keywords,
            ];
        }, $items);
    }
}
