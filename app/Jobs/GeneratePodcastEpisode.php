<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\PodcastEpisode;
use App\Services\PodcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePodcastEpisode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(public News $news) {}

    public function handle(PodcastService $podcastService): void
    {
        $episode = PodcastEpisode::firstOrCreate(
            ['news_id' => $this->news->id],
            ['status' => 'pending', 'voice' => config('services.podcast.voice', 'nova')]
        );

        if ($episode->status === 'ready') {
            return;
        }

        $podcastService->generate($episode);

        Log::info('Podcast episode generated', [
            'news_id'    => $this->news->id,
            'episode_id' => $episode->id,
            'audio_url'  => $episode->audio_url,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('GeneratePodcastEpisode job failed', [
            'news_id' => $this->news->id,
            'error'   => $e->getMessage(),
        ]);

        PodcastEpisode::where('news_id', $this->news->id)
            ->where('status', 'processing')
            ->update(['status' => 'error', 'error_message' => $e->getMessage()]);
    }
}
