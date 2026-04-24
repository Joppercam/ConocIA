<?php

namespace App\Console\Commands;

use App\Models\SearchConsoleMetric;
use App\Services\SearchConsoleService;
use Illuminate\Console\Command;

class SyncSearchConsoleMetrics extends Command
{
    protected $signature = 'seo:sync-search-console
                            {--days=28 : Número de días hacia atrás a sincronizar}
                            {--site-url= : Propiedad exacta de Search Console}
                            {--type=web : Tipo de búsqueda (web, discover, googleNews, image, video, news)}
                            {--dry-run : Muestra resultados sin guardar}';

    protected $description = 'Sincroniza métricas de Google Search Console a la base local';

    public function __construct(private readonly SearchConsoleService $searchConsole)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $siteUrl = $this->option('site-url') ?: $this->searchConsole->defaultSiteUrl();
        $days = max((int) $this->option('days'), 1);
        $type = (string) $this->option('type');
        $dryRun = (bool) $this->option('dry-run');

        if (!$siteUrl) {
            $this->error('Falta definir la propiedad de Search Console. Usa --site-url o GOOGLE_SEARCH_CONSOLE_SITE_URL.');
            return self::FAILURE;
        }

        if (!$this->searchConsole->isConfigured()) {
            $this->error('Search Console no está configurado. Revisa GOOGLE_SEARCH_CONSOLE_* en el entorno.');
            return self::FAILURE;
        }

        $endDate = now()->toDateString();
        $startDate = now()->subDays($days - 1)->toDateString();

        $this->info("Sincronizando Search Console para {$siteUrl} ({$type}) del {$startDate} al {$endDate}...");

        $pageRows = $this->searchConsole->querySearchAnalytics($siteUrl, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => ['date', 'page'],
            'rowLimit' => 25000,
            'type' => $type,
        ]);

        $queryRows = $this->searchConsole->querySearchAnalytics($siteUrl, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => ['date', 'query'],
            'rowLimit' => 25000,
            'type' => $type,
        ]);

        $this->line('Filas por página: ' . count($pageRows));
        $this->line('Filas por query: ' . count($queryRows));

        if ($dryRun) {
            $this->warn('Dry run: no se guardaron cambios.');
            return self::SUCCESS;
        }

        SearchConsoleMetric::where('site_url', $siteUrl)
            ->where('search_type', $type)
            ->whereBetween('metric_date', [$startDate, $endDate])
            ->whereIn('dimension_type', ['page', 'query'])
            ->delete();

        $syncedAt = now();

        foreach ($pageRows as $row) {
            $this->storeRow($siteUrl, $type, 'page', $row, $syncedAt);
        }

        foreach ($queryRows as $row) {
            $this->storeRow($siteUrl, $type, 'query', $row, $syncedAt);
        }

        $this->info('Sincronización completada.');

        return self::SUCCESS;
    }

    private function storeRow(string $siteUrl, string $type, string $dimensionType, array $row, $syncedAt): void
    {
        $keys = $row['keys'] ?? [];
        $metricDate = $keys[0] ?? null;
        $value = $keys[1] ?? null;

        if (!$metricDate || !$value) {
            return;
        }

        $page = $dimensionType === 'page' ? $value : null;
        $query = $dimensionType === 'query' ? $value : null;
        $hash = hash('sha256', implode('|', [$metricDate, $dimensionType, $page, $query, $type]));

        SearchConsoleMetric::updateOrCreate(
            [
                'site_url' => $siteUrl,
                'metric_date' => $metricDate,
                'search_type' => $type,
                'dimension_type' => $dimensionType,
                'dimension_key_hash' => $hash,
            ],
            [
                'page' => $page,
                'query' => $query,
                'clicks' => (int) round($row['clicks'] ?? 0),
                'impressions' => (int) round($row['impressions'] ?? 0),
                'ctr' => (float) ($row['ctr'] ?? 0),
                'position' => (float) ($row['position'] ?? 0),
                'synced_at' => $syncedAt,
            ]
        );
    }
}
