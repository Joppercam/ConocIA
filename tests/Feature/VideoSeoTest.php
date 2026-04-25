<?php

namespace Tests\Feature;

use App\Models\Video;
use App\Models\VideoPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_video_url_redirects_to_slugged_canonical_url(): void
    {
        $video = $this->createVideo([
            'title' => 'Guia completa de ChatGPT para equipos de producto',
            'description' => str_repeat('Contenido editorial fuerte. ', 10),
        ]);

        $response = $this->get('/videos/' . $video->id);

        $response->assertRedirect(route('videos.show', $video->routeParameters()));
    }

    public function test_thin_video_pages_are_served_with_noindex(): void
    {
        $video = $this->createVideo([
            'title' => 'youtube84591 video automatico',
            'description' => 'Breve.',
        ]);

        $response = $this->get(route('videos.show', $video->routeParameters()));

        $response->assertOk();
        $response->assertSee('noindex, follow');
    }

    private function createVideo(array $attributes = []): Video
    {
        $platform = VideoPlatform::create([
            'name' => 'YouTube',
            'code' => 'youtube',
            'embed_pattern' => 'https://www.youtube.com/embed/{id}',
            'is_active' => true,
        ]);

        return Video::create(array_merge([
            'platform_id' => $platform->id,
            'external_id' => 'abc123' . fake()->unique()->numberBetween(100, 999),
            'title' => 'Video de prueba sobre inteligencia artificial',
            'description' => str_repeat('Descripcion de prueba. ', 8),
            'thumbnail_url' => 'https://example.com/video.jpg',
            'embed_url' => 'https://www.youtube.com/embed/abc123',
            'original_url' => 'https://www.youtube.com/watch?v=abc123',
            'published_at' => now()->subDay(),
            'duration_seconds' => 320,
            'view_count' => 12,
            'is_featured' => false,
        ], $attributes));
    }
}
