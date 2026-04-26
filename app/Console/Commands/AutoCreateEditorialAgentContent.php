<?php

namespace App\Console\Commands;

use App\Models\EditorialAgentTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AutoCreateEditorialAgentContent extends Command
{
    protected $signature = 'editorial-agent:auto-create-content';

    protected $description = 'Crea propuestas editoriales automaticamente con limites diarios y control de pendientes.';

    public function handle(): int
    {
        if (!config('services.editorial_agent.auto_news_enabled', true)) {
            $this->info('Agente editorial automatico desactivado.');

            return self::SUCCESS;
        }

        if (!Schema::hasTable('editorial_agent_tasks')) {
            $this->warn('La tabla editorial_agent_tasks no existe.');

            return self::SUCCESS;
        }

        if (blank(config('services.gemini.api_key'))) {
            $this->warn('GEMINI_API_KEY no esta configurada.');

            return self::SUCCESS;
        }

        $dailyLimit = (int) config('services.editorial_agent.auto_news_daily_limit', 3);
        $maxPending = (int) config('services.editorial_agent.auto_news_max_pending', 6);

        $createdToday = EditorialAgentTask::query()
            ->where('task_type', 'news_draft')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($createdToday >= $dailyLimit) {
            $this->info("Limite diario alcanzado: {$createdToday}/{$dailyLimit}.");

            return self::SUCCESS;
        }

        $pendingDrafts = EditorialAgentTask::query()
            ->where('task_type', 'news_draft')
            ->where('status', 'pending')
            ->count();

        if ($pendingDrafts >= $maxPending) {
            $this->info("Hay {$pendingDrafts} borradores pendientes. Se pausa hasta que el editor revise.");

            return self::SUCCESS;
        }

        $topic = $this->nextTopic();
        $category = $topic['category'] ?? 'inteligencia-artificial';
        $days = (int) config('services.editorial_agent.auto_news_days', 2);

        $this->info("Creando propuesta automatica: {$topic['topic']}");

        $exitCode = Artisan::call('editorial-agent:create-news', [
            '--topic' => $topic['topic'],
            '--category' => $category,
            '--days' => $days,
            '--priority' => $topic['priority'] ?? 'high',
        ]);

        $this->output->write(Artisan::output());

        return $exitCode === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function nextTopic(): array
    {
        $topics = config('services.editorial_agent.auto_news_topics', []);

        if (empty($topics)) {
            $topics = [
                ['topic' => 'noticias recientes de inteligencia artificial con impacto empresarial', 'category' => 'inteligencia-artificial', 'priority' => 'high'],
            ];
        }

        $slot = (int) floor(now()->diffInMinutes(now()->copy()->startOfDay()) / 30);
        $index = ($slot + now()->dayOfYear) % count($topics);

        return $topics[$index];
    }
}
