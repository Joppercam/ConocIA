<?php

namespace App\Console\Commands;

use App\Models\SearchConsoleMetric;
use App\Services\SearchConsoleService;
use Illuminate\Console\Command;

class AuditSearchConsoleMetrics extends Command
{
    protected $signature = 'seo:audit-search-console
                            {--days=28 : Ventana actual a comparar}
                            {--site-url= : Propiedad exacta de Search Console}
                            {--type=web : Tipo de búsqueda (web, discover, googleNews, image, video, news)}';

    protected $description = 'Audita tendencias SEO desde Search Console comparando con el período anterior';

    public function __construct(private readonly SearchConsoleService $searchConsole)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $siteUrl = $this->option('site-url') ?: $this->searchConsole->defaultSiteUrl();
        $days = max((int) $this->option('days'), 1);
        $type = (string) $this->option('type');

        if (!$siteUrl) {
            $this->error('Falta definir la propiedad de Search Console. Usa --site-url o GOOGLE_SEARCH_CONSOLE_SITE_URL.');
            return self::FAILURE;
        }

        $currentStart = now()->subDays($days - 1)->toDateString();
        $currentEnd = now()->toDateString();
        $previousStart = now()->subDays(($days * 2) - 1)->toDateString();
        $previousEnd = now()->subDays($days)->toDateString();

        $current = SearchConsoleMetric::query()
            ->where('site_url', $siteUrl)
            ->where('search_type', $type)
            ->where('dimension_type', 'page')
            ->whereDate('metric_date', '>=', $currentStart)
            ->whereDate('metric_date', '<=', $currentEnd)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->get()
            ->keyBy('page');

        $previous = SearchConsoleMetric::query()
            ->where('site_url', $siteUrl)
            ->where('search_type', $type)
            ->where('dimension_type', 'page')
            ->whereDate('metric_date', '>=', $previousStart)
            ->whereDate('metric_date', '<=', $previousEnd)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->get()
            ->keyBy('page');

        $comparison = $current->map(function ($row, $page) use ($previous) {
            $prev = $previous->get($page);

            return [
                'page' => $page,
                'impressions' => (int) $row->impressions,
                'position' => round((float) $row->position, 1),
                'delta_impressions' => (int) $row->impressions - (int) ($prev->impressions ?? 0),
            ];
        })->values();

        $falling = $comparison
            ->filter(fn ($row) => $row['delta_impressions'] <= -10)
            ->sortBy('delta_impressions')
            ->take(10)
            ->values();

        $rising = $comparison
            ->filter(fn ($row) => $row['delta_impressions'] >= 10)
            ->sortByDesc('delta_impressions')
            ->take(10)
            ->values();

        $newOpportunities = $comparison
            ->filter(function ($row) use ($previous) {
                $prev = $previous->get($row['page']);

                return ((int) ($prev->impressions ?? 0) === 0)
                    && $row['impressions'] >= 10
                    && $row['position'] >= 4
                    && $row['position'] <= 15;
            })
            ->sortByDesc('impressions')
            ->take(10)
            ->values();

        $this->info("Auditoría SEO {$siteUrl} ({$type})");
        $this->line("Actual: {$currentStart} a {$currentEnd}");
        $this->line("Previo: {$previousStart} a {$previousEnd}");

        $this->newLine();
        $this->info('Páginas cayendo');
        $this->table(['Página', 'Imp.', 'Delta'], $falling->map(fn ($row) => [
            $row['page'],
            number_format($row['impressions']),
            number_format($row['delta_impressions']),
        ])->all());

        $this->newLine();
        $this->info('Páginas subiendo');
        $this->table(['Página', 'Imp.', 'Delta'], $rising->map(fn ($row) => [
            $row['page'],
            number_format($row['impressions']),
            '+' . number_format($row['delta_impressions']),
        ])->all());

        $this->newLine();
        $this->info('Nuevas oportunidades');
        $this->table(['Página', 'Imp.', 'Pos.'], $newOpportunities->map(fn ($row) => [
            $row['page'],
            number_format($row['impressions']),
            number_format($row['position'], 1),
        ])->all());

        return self::SUCCESS;
    }
}
