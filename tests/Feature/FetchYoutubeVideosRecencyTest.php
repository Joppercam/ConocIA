<?php

namespace Tests\Feature;

use App\Console\Commands\FetchYoutubeVideos;
use Illuminate\Support\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class FetchYoutubeVideosRecencyTest extends TestCase
{
    public function test_youtube_fetcher_treats_only_recent_publish_dates_as_eligible(): void
    {
        $command = new FetchYoutubeVideos();
        $method = new ReflectionMethod($command, 'publishedAt');
        $method->setAccessible(true);

        $cutoff = Carbon::parse('2026-05-07 12:00:00');

        $recent = $method->invoke($command, '2026-04-30 12:00:00');
        $old = $method->invoke($command, '2026-04-20 12:00:00');
        $missing = $method->invoke($command, null);

        $this->assertTrue($recent->gte($cutoff->copy()->subDays(14)));
        $this->assertFalse($old->gte($cutoff->copy()->subDays(14)));
        $this->assertNull($missing);
    }
}
