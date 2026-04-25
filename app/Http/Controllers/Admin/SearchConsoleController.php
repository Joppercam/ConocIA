<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\SearchConsoleMetric;
use App\Services\SearchConsoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchConsoleController extends Controller
{
    public function index(Request $request, SearchConsoleService $searchConsole)
    {
        $days = max((int) $request->integer('days', 28), 1);
        $siteUrl = $request->string('site_url')->toString() ?: $searchConsole->defaultSiteUrl();
        $type = $request->string('type')->toString() ?: 'web';
        $startDate = now()->subDays($days - 1)->toDateString();
        $endDate = now()->toDateString();

        $baseQuery = SearchConsoleMetric::query()
            ->when($siteUrl, fn ($query) => $query->where('site_url', $siteUrl))
            ->where('search_type', $type)
            ->whereDate('metric_date', '>=', $startDate)
            ->whereDate('metric_date', '<=', $endDate);

        $pageMetrics = (clone $baseQuery)->where('dimension_type', 'page');
        $queryMetrics = (clone $baseQuery)->where('dimension_type', 'query');

        $summary = [
            'clicks' => (int) $pageMetrics->sum('clicks'),
            'impressions' => (int) $pageMetrics->sum('impressions'),
            'avg_ctr' => (float) $pageMetrics->avg('ctr'),
            'avg_position' => (float) $pageMetrics->avg('position'),
            'pages' => (clone $pageMetrics)->whereNotNull('page')->distinct('page')->count('page'),
            'queries' => (clone $queryMetrics)->whereNotNull('query')->distinct('query')->count('query'),
            'last_synced_at' => (clone $baseQuery)->max('synced_at'),
        ];

        $dailyPerformance = (clone $pageMetrics)
            ->selectRaw('metric_date, SUM(clicks) as clicks, SUM(impressions) as impressions')
            ->groupBy('metric_date')
            ->orderBy('metric_date')
            ->get();

        $topPages = (clone $pageMetrics)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->orderByDesc('impressions')
            ->limit(15)
            ->get();

        $topQueries = (clone $queryMetrics)
            ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('query')
            ->groupBy('query')
            ->orderByDesc('impressions')
            ->limit(15)
            ->get();

        $opportunityPages = (clone $pageMetrics)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->havingRaw('SUM(impressions) >= ?', [10])
            ->havingRaw('SUM(clicks) = 0 OR AVG(ctr) < ?', [0.03])
            ->havingRaw('AVG(position) BETWEEN ? AND ?', [4, 15])
            ->orderByDesc('impressions')
            ->limit(12)
            ->get();

        $zeroClickPages = (clone $pageMetrics)
            ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('page')
            ->groupBy('page')
            ->havingRaw('SUM(impressions) > 0')
            ->havingRaw('SUM(clicks) = 0')
            ->orderByDesc('impressions')
            ->limit(12)
            ->get();

        $queryCandidates = (clone $queryMetrics)
            ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('query')
            ->groupBy('query')
            ->orderByDesc('impressions')
            ->limit(100)
            ->get();

        $weirdQueries = $queryCandidates
            ->filter(fn ($row) => $this->looksLikeNoiseQuery((string) $row->query))
            ->values()
            ->take(12);

        $hostBreakdown = $topPages
            ->groupBy(fn ($row) => parse_url((string) $row->page, PHP_URL_HOST) ?: 'sin-host')
            ->map(fn (Collection $rows, string $host) => [
                'host' => $host,
                'pages' => $rows->count(),
                'impressions' => (int) $rows->sum('impressions'),
                'clicks' => (int) $rows->sum('clicks'),
            ])
            ->sortByDesc('impressions')
            ->values();

        $canonicalHost = parse_url(config('app.url'), PHP_URL_HOST);
        $mixedHosts = $hostBreakdown->pluck('host')
            ->filter()
            ->unique()
            ->count() > 1;

        $actionableNews = $this->buildActionableNews($opportunityPages);

        return view('admin.seo.search-console', compact(
            'days',
            'siteUrl',
            'type',
            'startDate',
            'endDate',
            'summary',
            'dailyPerformance',
            'topPages',
            'topQueries',
            'opportunityPages',
            'zeroClickPages',
            'weirdQueries',
            'hostBreakdown',
            'canonicalHost',
            'mixedHosts',
            'actionableNews'
        ))->with([
            'isConfigured' => $searchConsole->isConfigured(),
        ]);
    }

    private function looksLikeNoiseQuery(string $query): bool
    {
        $normalized = Str::lower(trim($query));

        if ($normalized === '') {
            return false;
        }

        return Str::startsWith($normalized, 'youtube')
            || preg_match('/\d{4,}/', $normalized) === 1
            || preg_match('/^[a-z0-9_-]{10,}$/i', $normalized) === 1;
    }

    private function buildActionableNews(Collection $opportunityPages): Collection
    {
        $slugs = $opportunityPages
            ->map(function ($row) {
                $path = trim((string) parse_url((string) $row->page, PHP_URL_PATH), '/');

                if (!Str::startsWith($path, 'news/')) {
                    return null;
                }

                return Str::after($path, 'news/');
            })
            ->filter()
            ->unique()
            ->values();

        if ($slugs->isEmpty()) {
            return collect();
        }

        $newsBySlug = News::query()
            ->whereIn('slug', $slugs)
            ->get()
            ->keyBy('slug');

        return $opportunityPages
            ->map(function ($row) use ($newsBySlug) {
                $path = trim((string) parse_url((string) $row->page, PHP_URL_PATH), '/');
                $slug = Str::startsWith($path, 'news/') ? Str::after($path, 'news/') : null;
                $news = $slug ? $newsBySlug->get($slug) : null;

                if (!$news) {
                    return null;
                }

                $seoTitle = $news->seoTitle();
                $seoDescription = $news->seoDescription();

                return [
                    'news' => $news,
                    'page' => $row->page,
                    'clicks' => (int) $row->clicks,
                    'impressions' => (int) $row->impressions,
                    'ctr' => (float) $row->ctr,
                    'position' => (float) $row->position,
                    'seo_title_length' => Str::length($seoTitle),
                    'seo_description_length' => Str::length($seoDescription),
                    'has_summary' => filled($news->summary),
                ];
            })
            ->filter()
            ->values()
            ->take(10);
    }
}
