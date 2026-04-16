<?php

namespace App\Console\Commands;

use App\Services\DailyBriefingService;
use Illuminate\Console\Command;

class GenerateDailyBriefing extends Command
{
    protected $signature = 'briefing:generate {--force : Regenerate even if today\'s briefing exists}';

    protected $description = 'Generate (or regenerate) today\'s AI-powered daily news briefing';

    public function handle(DailyBriefingService $service): int
    {
        $this->info('Generating daily briefing...');

        $briefing = $service->generate(force: $this->option('force'));

        if (!$briefing) {
            $this->error('Failed to generate briefing. Check logs for details.');
            return Command::FAILURE;
        }

        $this->info("✓ Briefing generated for {$briefing->date->toDateString()}");
        $this->line("  Words: ~" . str_word_count($briefing->script));
        $this->line("  Duration: ~{$briefing->estimated_minutes}");
        $this->line("  News covered: {$briefing->news_count}");

        return Command::SUCCESS;
    }
}
