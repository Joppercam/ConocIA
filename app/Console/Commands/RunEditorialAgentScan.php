<?php

namespace App\Console\Commands;

use App\Models\EditorialAgentTask;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class RunEditorialAgentScan extends Command
{
    protected $signature = 'editorial-agent:scan {--days=7 : Ventana de lecturas humanas a revisar}';

    protected $description = 'Detecta oportunidades editoriales y las deja pendientes para aprobación.';

    public function handle(): int
    {
        if (!Schema::hasTable('editorial_agent_tasks') || !Schema::hasTable('site_visit_events')) {
            $this->warn('Faltan tablas requeridas para el agente editorial.');

            return self::SUCCESS;
        }

        $days = max(1, (int) $this->option('days'));
        $since = now()->subDays($days)->startOfDay();
        $created = 0;

        foreach ($this->contentSources() as $source) {
            $created += $this->createUnreadContentTasks($source, $since, $days);
        }

        $this->info("Agente editorial completado. Propuestas nuevas: {$created}");

        return self::SUCCESS;
    }

    private function createUnreadContentTasks(array $source, Carbon $since, int $days): int
    {
        if (!Schema::hasTable($source['table'])) {
            return 0;
        }

        try {
            $visited = DB::table('site_visit_events')
                ->where('is_bot', false)
                ->where('content_type', $source['type'])
                ->whereNotNull('content_id')
                ->where('viewed_at', '>=', $since)
                ->select('content_id')
                ->distinct();

            $query = DB::table($source['table'])
                ->leftJoinSub($visited, 'human_visits', function ($join) use ($source) {
                    $join->on("{$source['table']}.id", '=', 'human_visits.content_id');
                })
                ->whereNull('human_visits.content_id')
                ->whereNotNull("{$source['table']}.{$source['date_column']}")
                ->select([
                    "{$source['table']}.id",
                    "{$source['table']}.{$source['title_column']} as title",
                    "{$source['table']}.{$source['slug_column']} as slug",
                    "{$source['table']}.{$source['date_column']} as published_at",
                ])
                ->orderByDesc("{$source['table']}.{$source['date_column']}")
                ->limit(5);

            if ($source['status_column']) {
                $query->where("{$source['table']}.{$source['status_column']}", $source['status_value']);
            }

            return $query->get()->sum(function ($item) use ($source, $days) {
                $url = route($source['route'], $item->slug);
                $dedupeKey = "content_push:{$source['type']}:{$item->id}:" . now()->format('Y-m-d');

                $task = EditorialAgentTask::firstOrCreate(
                    ['dedupe_key' => $dedupeKey],
                    [
                        'task_type' => 'content_push',
                        'priority' => $source['priority'],
                        'status' => 'pending',
                        'title' => "Impulsar: {$item->title}",
                        'summary' => "Contenido publicado sin lecturas humanas registradas en los ultimos {$days} dias.",
                        'suggested_action' => 'Revisar titulo/SEO y preparar republicacion en LinkedIn o X con un angulo mas directo.',
                        'content_type' => $source['type'],
                        'content_id' => $item->id,
                        'content_url' => $url,
                        'payload' => [
                            'published_at' => $item->published_at,
                            'source_table' => $source['table'],
                        ],
                    ]
                );

                return $task->wasRecentlyCreated ? 1 : 0;
            });
        } catch (Throwable $e) {
            $this->warn("No se pudo revisar {$source['table']}: {$e->getMessage()}");

            return 0;
        }
    }

    private function contentSources(): array
    {
        return [
            ['table' => 'news', 'type' => 'noticia', 'title_column' => 'title', 'slug_column' => 'slug', 'date_column' => 'published_at', 'status_column' => 'status', 'status_value' => 'published', 'route' => 'news.show', 'priority' => 'high'],
            ['table' => 'columns', 'type' => 'columna', 'title_column' => 'title', 'slug_column' => 'slug', 'date_column' => 'published_at', 'status_column' => null, 'status_value' => null, 'route' => 'columns.show', 'priority' => 'medium'],
            ['table' => 'conocia_papers', 'type' => 'paper', 'title_column' => 'title', 'slug_column' => 'slug', 'date_column' => 'published_at', 'status_column' => 'status', 'status_value' => 'published', 'route' => 'papers.show', 'priority' => 'high'],
            ['table' => 'conceptos_ia', 'type' => 'concepto', 'title_column' => 'title', 'slug_column' => 'slug', 'date_column' => 'published_at', 'status_column' => 'status', 'status_value' => 'published', 'route' => 'conceptos.show', 'priority' => 'medium'],
            ['table' => 'analisis_fondo', 'type' => 'analisis', 'title_column' => 'title', 'slug_column' => 'slug', 'date_column' => 'published_at', 'status_column' => 'status', 'status_value' => 'published', 'route' => 'analisis.show', 'priority' => 'medium'],
            ['table' => 'startups', 'type' => 'startup', 'title_column' => 'name', 'slug_column' => 'slug', 'date_column' => 'updated_at', 'status_column' => 'active', 'status_value' => 1, 'route' => 'startups.show', 'priority' => 'medium'],
        ];
    }
}
