<?php

namespace App\Jobs;

use App\Models\News;
use App\Services\PodcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePodcastEpisode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public News $news) {}

    public function handle(PodcastService $service): void
    {
        if ($this->news->podcastEpisode?->isReady()) {
            return;
        }

        $service->generate($this->news);
    }
}
