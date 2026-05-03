<?php

namespace App\Console\Commands;

use App\Models\EditorialAgentTask;
use App\Models\SearchConsoleMetric;
use App\Support\EditorialAgentLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateEditorialSeoOpportunities extends Command
{
    protected $signature = 'seo:generate-editorial-opportunities
                            {--days=28 : Ventana actual a analizar}
                            {--site-url= : Propiedad exacta de Search Console}
                            {--type=web : Tipo de búsqueda}
                            {--limit=12 : Máximo de tareas nuevas por ejecución}';

    protected $description = 'Convierte señales de Search Console en tareas del agente editorial.';

    public function handle(): int
    {
        if (!Schema::hasTable('search_console_metrics') || !Schema::hasTable('editorial_agent_tasks')) {
            $this->warn('Faltan tablas de Search Console o agente editorial.');

            return self::SUCCESS;
        }

        $siteUrl = (string) ($this->option('site-url') ?: config('services.search_console.site_url'));
        if ($siteUrl === '') {
            $this->warn('Falta GOOGLE_SEARCH_CONSOLE_SITE_URL o --site-url.');

            return self::SUCCESS;
        }

        $days = max(1, (int) $this->option('days'));
        $type = (string) $this->option('type');
        $limit = max(1, (int) $this->option('limit'));
        $startDate = now()->subDays($days - 1)->toDateString();
        $endDate = now()->toDateString();
        $created = 0;

        $pageMetrics = SearchConsoleMetric::query()
            ->where('site_url', $siteUrl)
            ->where('search_type', $type)
            ->where('dimension_type', 'page')
            ->whereDate('metric_date', '>=', $startDate)
            ->whereDate('metric_date', '<=', $endDate);

        $queryMetrics = SearchConsoleMetric::query()
            ->where('site_url', $siteUrl)
            ->where('search_type', $type)
            ->where('dimension_type', 'query')
            ->whereDate('metric_date', '>=', $startDate)
            ->whereDate('metric_date', '<=', $endDate);

        $created += $this->createCtrTasks($pageMetrics, $days, $limit - $created);
        $created += $this->createEvergreenTasks($queryMetrics, $days, $limit - $created);
        $created += $this->createClusterTasks($queryMetrics, $days, $limit - $created);

        $this->info("Oportunidades SEO editoriales creadas: {$created}");
        EditorialAgentLogger::info('seo_opportunities_generated', 'Se generaron oportunidades SEO editoriales.', [
            'created' => $created,
            'days' => $days,
            'site_url' => $siteUrl,
            'type' => $type,
        ]);

        return self::SUCCESS;
    }

    private function createCtrTasks($pageMetrics, int $days, int $remaining): int
    {
        if ($remaining <= 0) {
            return 0;
        }

        $rows = (clone $pageMetrics)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->havingRaw('SUM(impressions) >= ?', [10])
            ->havingRaw('SUM(clicks) = 0 OR AVG(ctr) < ?', [0.03])
            ->havingRaw('AVG(position) BETWEEN ? AND ?', [4, 15])
            ->orderByDesc('impressions')
            ->limit($remaining)
            ->get();

        return $rows->sum(function ($row) use ($days) {
            $page = (string) $row->page;
            $dedupeKey = 'seo_ctr:' . sha1($page . '|' . now()->startOfWeek()->toDateString());

            $task = EditorialAgentTask::firstOrCreate(
                ['dedupe_key' => $dedupeKey],
                [
                    'task_type' => 'seo_opportunity',
                    'priority' => 'high',
                    'status' => 'pending',
                    'title' => 'Mejorar CTR: ' . $this->pageLabel($page),
                    'summary' => sprintf(
                        'Google muestra esta página, pero captura pocos clicks: %s impresiones, CTR %s%%, posición %s en %s días.',
                        number_format((int) $row->impressions),
                        number_format(((float) $row->ctr) * 100, 2),
                        number_format((float) $row->position, 1),
                        $days
                    ),
                    'suggested_action' => 'Reescribir title/meta description con sujeto claro, dato concreto y contexto. Mantener tono ConocIA: sobrio, explicativo y sin clickbait.',
                    'content_type' => $this->contentTypeFromPage($page),
                    'content_url' => $page,
                    'source_urls' => [$page],
                    'payload' => [
                        'kind' => 'ctr_optimization',
                        'page' => $page,
                        'clicks' => (int) $row->clicks,
                        'impressions' => (int) $row->impressions,
                        'ctr' => (float) $row->ctr,
                        'position' => (float) $row->position,
                        'generated_from' => 'search_console',
                    ],
                ]
            );

            return $task->wasRecentlyCreated ? 1 : 0;
        });
    }

    private function createEvergreenTasks($queryMetrics, int $days, int $remaining): int
    {
        if ($remaining <= 0) {
            return 0;
        }

        $rows = (clone $queryMetrics)
            ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('query')
            ->groupBy('query')
            ->havingRaw('SUM(impressions) >= ?', [10])
            ->havingRaw('SUM(clicks) = 0 OR AVG(ctr) < ?', [0.02])
            ->orderByDesc('impressions')
            ->limit($remaining)
            ->get();

        return $rows->sum(function ($row) use ($days) {
            $query = trim((string) $row->query);
            if ($query === '' || $this->looksLikeNoiseQuery($query)) {
                return 0;
            }

            $dedupeKey = 'seo_evergreen:' . sha1(Str::lower($query) . '|' . now()->startOfWeek()->toDateString());
            $cluster = $this->resolveQueryCluster($query);

            $task = EditorialAgentTask::firstOrCreate(
                ['dedupe_key' => $dedupeKey],
                [
                    'task_type' => 'content_idea',
                    'priority' => $cluster === 'IA en Chile' || $cluster === 'Ciberseguridad e IA' ? 'high' : 'medium',
                    'status' => 'pending',
                    'title' => 'Crear evergreen: ' . Str::limit($query, 90, ''),
                    'summary' => sprintf(
                        'La query "%s" tuvo %s impresiones, CTR %s%% y posición promedio %s en %s días.',
                        $query,
                        number_format((int) $row->impressions),
                        number_format(((float) $row->ctr) * 100, 2),
                        number_format((float) $row->position, 1),
                        $days
                    ),
                    'suggested_action' => 'Crear una pieza evergreen breve y útil: concepto, guía o explicación. El agente puede convertir esta señal en borrador, pero no publicarla automáticamente.',
                    'payload' => [
                        'kind' => 'evergreen_candidate',
                        'query' => $query,
                        'cluster' => $cluster,
                        'category_slug' => $this->categoryForCluster($cluster),
                        'auto_create_news' => true,
                        'topic' => $this->topicForQuery($query, $cluster),
                        'clicks' => (int) $row->clicks,
                        'impressions' => (int) $row->impressions,
                        'ctr' => (float) $row->ctr,
                        'position' => (float) $row->position,
                        'generated_from' => 'search_console',
                    ],
                ]
            );

            return $task->wasRecentlyCreated ? 1 : 0;
        });
    }

    private function createClusterTasks($queryMetrics, int $days, int $remaining): int
    {
        if ($remaining <= 0) {
            return 0;
        }

        $clusters = (clone $queryMetrics)
            ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr')
            ->whereNotNull('query')
            ->groupBy('query')
            ->orderByDesc('impressions')
            ->limit(100)
            ->get()
            ->filter(fn ($row) => !$this->looksLikeNoiseQuery((string) $row->query))
            ->map(fn ($row) => (object) [
                'cluster' => $this->resolveQueryCluster((string) $row->query),
                'query' => (string) $row->query,
                'clicks' => (int) $row->clicks,
                'impressions' => (int) $row->impressions,
                'ctr' => (float) $row->ctr,
            ])
            ->groupBy('cluster')
            ->map(function (Collection $rows, string $cluster) {
                return (object) [
                    'cluster' => $cluster,
                    'queries' => $rows->count(),
                    'clicks' => (int) $rows->sum('clicks'),
                    'impressions' => (int) $rows->sum('impressions'),
                    'sample_queries' => $rows->sortByDesc('impressions')->take(5)->pluck('query')->values()->all(),
                ];
            })
            ->filter(fn ($row) => $row->impressions >= 20)
            ->sortByDesc('impressions')
            ->take($remaining);

        return $clusters->sum(function ($cluster) use ($days) {
            $dedupeKey = 'seo_cluster:' . sha1(Str::lower($cluster->cluster) . '|' . now()->startOfWeek()->toDateString());

            $task = EditorialAgentTask::firstOrCreate(
                ['dedupe_key' => $dedupeKey],
                [
                    'task_type' => 'seo_opportunity',
                    'priority' => 'medium',
                    'status' => 'pending',
                    'title' => 'Reforzar cluster: ' . $cluster->cluster,
                    'summary' => sprintf(
                        '%s queries del cluster "%s" acumulan %s impresiones en %s días.',
                        number_format($cluster->queries),
                        $cluster->cluster,
                        number_format($cluster->impressions),
                        $days
                    ),
                    'suggested_action' => 'Crear enlaces internos entre noticias, papers, investigaciones y conceptos del tema. Si falta una pieza puente, crear una explicación evergreen.',
                    'payload' => [
                        'kind' => 'cluster_internal_linking',
                        'cluster' => $cluster->cluster,
                        'category_slug' => $this->categoryForCluster($cluster->cluster),
                        'sample_queries' => $cluster->sample_queries,
                        'queries' => $cluster->queries,
                        'impressions' => $cluster->impressions,
                        'generated_from' => 'search_console',
                    ],
                ]
            );

            return $task->wasRecentlyCreated ? 1 : 0;
        });
    }

    private function pageLabel(string $page): string
    {
        $path = trim((string) parse_url($page, PHP_URL_PATH), '/');

        return $path !== '' ? Str::limit($path, 90, '') : 'home';
    }

    private function contentTypeFromPage(string $page): string
    {
        $path = trim((string) parse_url($page, PHP_URL_PATH), '/');

        return match (true) {
            str_starts_with($path, 'news/') => 'noticia',
            str_starts_with($path, 'papers/') => 'paper',
            str_starts_with($path, 'investigacion/') => 'investigacion',
            str_starts_with($path, 'columnas/') => 'columna',
            str_starts_with($path, 'conceptos-ia/') => 'concepto',
            str_starts_with($path, 'analisis/') => 'analisis',
            default => 'pagina',
        };
    }

    private function topicForQuery(string $query, string $cluster): string
    {
        return "Crear una pieza evergreen para la búsqueda \"{$query}\" dentro del cluster {$cluster}. Mantener tono ConocIA: claro, técnico cuando aporte, contextualizado y sin clickbait.";
    }

    private function categoryForCluster(string $cluster): string
    {
        return match ($cluster) {
            'IA en Chile' => 'ia-en-chile',
            'Ciberseguridad e IA' => 'ciberseguridad',
            'Agentes de IA' => 'ia-generativa',
            'Papers explicados' => 'investigacion',
            'Regulacion y datos' => 'regulacion-de-ia',
            default => 'inteligencia-artificial',
        };
    }

    private function resolveQueryCluster(string $query): string
    {
        $normalized = Str::of($query)->lower()->ascii()->toString();

        if (Str::contains($normalized, ['chile', 'anci', 'rut', 'gobierno', 'ley marco'])) {
            return 'IA en Chile';
        }

        if (Str::contains($normalized, ['ciber', 'seguridad', 'prompt injection', 'ransomware', 'datos', 'filtracion'])) {
            return 'Ciberseguridad e IA';
        }

        if (Str::contains($normalized, ['agente', 'agent', 'multiagente', 'autonomo'])) {
            return 'Agentes de IA';
        }

        if (Str::contains($normalized, ['paper', 'arxiv', 'investigacion', 'benchmark', 'stanford', 'mit'])) {
            return 'Papers explicados';
        }

        if (Str::contains($normalized, ['chatgpt', 'gemini', 'claude', 'openai', 'google', 'anthropic', 'modelo'])) {
            return 'Modelos y herramientas';
        }

        if (Str::contains($normalized, ['regulacion', 'ley', 'privacidad', 'biometr', 'derecho', 'copyright'])) {
            return 'Regulacion y datos';
        }

        return 'IA general';
    }

    private function looksLikeNoiseQuery(string $query): bool
    {
        $normalized = Str::lower(trim($query));

        return $normalized === ''
            || Str::startsWith($normalized, 'youtube')
            || preg_match('/\d{4,}/', $normalized) === 1
            || preg_match('/^[a-z0-9_-]{10,}$/i', $normalized) === 1;
    }
}
