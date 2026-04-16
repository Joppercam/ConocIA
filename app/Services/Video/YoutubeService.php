<?php

namespace App\Services\Video;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YoutubeService extends AbstractVideoService
{
    public function __construct()
    {
        $this->resolvePlatform('youtube', 'services.youtube.key');
    }

    public function search(array $keywords, int $limit = 5): array
    {
        $query = implode(' ', $keywords);

        return $this->cachedApiCall(
            'youtube_search_' . md5($query . '_' . $limit),
            now()->addHours(6),
            function () use ($query, $limit) {
                $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                    'key'               => $this->apiKey,
                    'q'                 => $query,
                    'part'              => 'snippet',
                    'type'              => 'video',
                    'maxResults'        => $limit,
                    'relevanceLanguage' => 'es',
                ]);

                if (!$response->successful()) {
                    Log::error('Error en la búsqueda de YouTube: ' . $response->body());
                    return [];
                }

                $items    = $response->json()['items'] ?? [];
                $videoIds = array_map(fn($i) => $i['id']['videoId'], $items);
                $details  = $this->getVideosDetails($videoIds);

                return $this->formatVideos($items, $details);
            }
        );
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->cachedApiCall(
            'youtube_popular_' . $limit,
            now()->addHours(12),
            function () use ($limit) {
                $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'key'        => $this->apiKey,
                    'chart'      => 'mostPopular',
                    'part'       => 'snippet,contentDetails,statistics',
                    'maxResults' => $limit,
                    'regionCode' => 'ES',
                ]);

                if (!$response->successful()) {
                    Log::error('Error al obtener videos populares de YouTube: ' . $response->body());
                    return [];
                }

                $items = $response->json()['items'] ?? [];
                return $this->formatVideos($items, $items);
            }
        );
    }

    public function getVideoInfo(string $videoId): ?array
    {
        return $this->cachedApiCall(
            'youtube_video_' . $videoId,
            now()->addDays(1),
            function () use ($videoId) {
                $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'key'  => $this->apiKey,
                    'id'   => $videoId,
                    'part' => 'snippet,contentDetails,statistics',
                ]);

                if (!$response->successful()) {
                    Log::error('Error en YouTube API: ' . $response->status() . ' - ' . $response->body());
                    return null;
                }

                $items = $response->json()['items'] ?? [];
                if (empty($items)) {
                    return null;
                }

                $data    = $items[0];
                $snippet = $data['snippet'] ?? [];

                return [
                    'platform_id'      => $this->platform?->id,
                    'external_id'      => $videoId,
                    'title'            => $snippet['title'] ?? 'Sin título',
                    'description'      => $snippet['description'] ?? null,
                    'thumbnail_url'    => $snippet['thumbnails']['high']['url'] ?? $snippet['thumbnails']['default']['url'] ?? null,
                    'embed_url'        => 'https://www.youtube.com/embed/' . $videoId,
                    'original_url'     => 'https://www.youtube.com/watch?v=' . $videoId,
                    'published_at'     => isset($snippet['publishedAt']) ? date('Y-m-d H:i:s', strtotime($snippet['publishedAt'])) : now()->format('Y-m-d H:i:s'),
                    'duration_seconds' => $this->parseDuration($data['contentDetails']['duration'] ?? 'PT0S'),
                    'view_count'       => $data['statistics']['viewCount'] ?? 0,
                    'keywords'         => $snippet['tags'] ?? [],
                ];
            },
            null
        );
    }

    protected function getVideosDetails(array $videoIds): array
    {
        try {
            $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                'key'  => $this->apiKey,
                'id'   => implode(',', $videoIds),
                'part' => 'contentDetails,statistics',
            ]);

            if (!$response->successful()) {
                return [];
            }

            $details = [];
            foreach ($response->json()['items'] ?? [] as $item) {
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
        return array_map(function ($item) use ($details) {
            $videoId = isset($item['id']['videoId']) ? $item['id']['videoId'] : $item['id'];
            $detail  = $details[$videoId] ?? null;

            $durationSeconds = $detail
                ? $this->parseDuration($detail['contentDetails']['duration'] ?? 'PT0S')
                : 0;

            return [
                'platform_id'      => $this->platform?->id,
                'external_id'      => $videoId,
                'title'            => $item['snippet']['title'],
                'description'      => $item['snippet']['description'] ?? null,
                'thumbnail_url'    => $item['snippet']['thumbnails']['high']['url'] ?? $item['snippet']['thumbnails']['default']['url'] ?? null,
                'embed_url'        => 'https://www.youtube.com/embed/' . $videoId,
                'original_url'     => 'https://www.youtube.com/watch?v=' . $videoId,
                'published_at'     => date('Y-m-d H:i:s', strtotime($item['snippet']['publishedAt'])),
                'duration_seconds' => $durationSeconds,
                'view_count'       => $detail['statistics']['viewCount'] ?? 0,
                'keywords'         => $item['snippet']['tags'] ?? [],
            ];
        }, $items);
    }

    /**
     * Convierte duración ISO 8601 (PT1H2M3S) a segundos.
     */
    protected function parseDuration(string $duration): int
    {
        $hours = $minutes = $seconds = 0;
        if (preg_match('/(\d+)H/', $duration, $m)) $hours   = (int) $m[1];
        if (preg_match('/(\d+)M/', $duration, $m)) $minutes = (int) $m[1];
        if (preg_match('/(\d+)S/', $duration, $m)) $seconds = (int) $m[1];
        return $hours * 3600 + $minutes * 60 + $seconds;
    }
}
