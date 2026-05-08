<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomeHeroRotationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_home_hero_rotates_across_four_hour_slots(): void
    {
        Cache::flush();
        $publishedAt = Carbon::parse('2030-01-01 12:00:00', config('app.timezone'));

        foreach (range(1, 6) as $index) {
            News::factory()->create([
                'title' => "Hero article {$index}",
                'slug' => "hero-article-{$index}",
                'image' => "https://example.com/hero-{$index}.jpg",
                'featured' => true,
                'published_at' => $publishedAt->copy()->subMinutes($index),
                'created_at' => $publishedAt->copy()->subMinutes($index),
            ]);
        }

        Carbon::setTestNow(Carbon::parse('2026-05-07 00:15:00', config('app.timezone')));
        $firstSlot = $this->get(route('home'));

        $firstSlot->assertOk();
        $this->assertMatchesRegularExpression('/<h1[^>]*>\s*Hero article 1\s*<\/h1>/', $firstSlot->getContent());

        Carbon::setTestNow(Carbon::parse('2026-05-07 04:15:00', config('app.timezone')));
        $secondSlot = $this->get(route('home'));

        $secondSlot->assertOk();
        $this->assertMatchesRegularExpression('/<h1[^>]*>\s*Hero article 2\s*<\/h1>/', $secondSlot->getContent());
    }
}
