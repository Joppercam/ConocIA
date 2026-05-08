<?php

namespace Tests\Feature;

use App\Models\VideoPlatform;
use App\Services\Video\YoutubeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YoutubeServiceSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_sends_published_after_when_filtering_recent_videos(): void
    {
        VideoPlatform::create([
            'name' => 'YouTube',
            'code' => 'youtube',
            'embed_pattern' => 'https://www.youtube.com/embed/{id}',
            'api_key' => 'test-key',
            'is_active' => true,
        ]);

        Http::fake([
            'www.googleapis.com/youtube/v3/search*' => Http::response([
                'items' => [
                    [
                        'id' => ['videoId' => 'video123'],
                        'snippet' => [
                            'title' => 'Video reciente de IA',
                            'description' => 'Descripcion',
                            'publishedAt' => '2026-05-01T12:00:00Z',
                            'thumbnails' => ['high' => ['url' => 'https://example.com/thumb.jpg']],
                            'tags' => ['ia'],
                        ],
                    ],
                ],
            ]),
            'www.googleapis.com/youtube/v3/videos*' => Http::response([
                'items' => [
                    [
                        'id' => 'video123',
                        'contentDetails' => ['duration' => 'PT5M'],
                        'statistics' => ['viewCount' => 123],
                    ],
                ],
            ]),
        ]);

        $publishedAfter = Carbon::parse('2026-04-23 00:00:00', 'UTC');

        $videos = app(YoutubeService::class)->search(['inteligencia artificial'], 5, $publishedAfter);

        $this->assertCount(1, $videos);
        Http::assertSent(function (Request $request) use ($publishedAfter) {
            return str_contains($request->url(), '/youtube/v3/search')
                && $request['publishedAfter'] === $publishedAfter->toIso8601String()
                && $request['order'] === 'date';
        });
    }
}
