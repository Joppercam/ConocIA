<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ValidateNewsQualityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_quality_command_deactivates_incomplete_news_and_clears_broken_images(): void
    {
        Cache::flush();

        $brokenImageUrl = 'https://cdn.example.com/broken-news.jpg';

        Http::fake([
            $brokenImageUrl => Http::response('', 404),
        ]);

        $news = News::factory()->create([
            'content' => '<p>Cada vez más empresas usan IA.</p><p>Así lo …</p>',
            'image' => $brokenImageUrl,
            'status' => 'published',
            'featured' => true,
        ]);

        $news->forceFill(['is_published' => true])->save();

        $this->artisan('news:validate-quality --published --apply')
            ->assertExitCode(0);

        $news->refresh();

        $this->assertSame('draft', $news->status);
        $this->assertFalse((bool) $news->is_published);
        $this->assertFalse((bool) $news->featured);
        $this->assertNull($news->image);
    }

    public function test_incomplete_published_news_is_not_publicly_accessible(): void
    {
        $news = News::factory()->create([
            'slug' => 'nota-incompleta',
            'content' => '<p>Cada vez más empresas usan IA.</p><p>Así lo …</p>',
            'status' => 'published',
        ]);

        $news->forceFill(['is_published' => true])->save();

        $this->get('/news/nota-incompleta')->assertNotFound();
    }
}
