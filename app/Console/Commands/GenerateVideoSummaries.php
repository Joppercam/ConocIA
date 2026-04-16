<?php

namespace App\Console\Commands;

use App\Models\Video;
use App\Services\VideoSummaryService;
use Illuminate\Console\Command;

class GenerateVideoSummaries extends Command
{
    protected $signature = 'videos:generate-summaries
                            {--force : Regenerate even if summary exists}
                            {--limit=10 : Max videos to process in one run}';

    protected $description = 'Generate AI summaries and keywords for videos using Gemini';

    public function handle(VideoSummaryService $service): int
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');

        $query = Video::query();
        if (!$force) {
            $query->whereNull('ai_summary');
        }
        $videos = $query->limit($limit)->get();

        if ($videos->isEmpty()) {
            $this->info('All videos already have AI summaries.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$videos->count()} video(s)...");
        $ok = 0; $fail = 0;

        foreach ($videos as $video) {
            $this->line("  → {$video->title}");
            $result = $service->generate($video, $force);
            if ($result) {
                $ok++;
                $this->line("    ✓ Done");
            } else {
                $fail++;
                $this->warn("    ✗ Failed (check logs)");
            }
            // Respect free-tier rate limits
            if ($videos->count() > 1) sleep(2);
        }

        $this->info("Completed: {$ok} ok, {$fail} failed.");
        return $fail > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
