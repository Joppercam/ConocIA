<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchConsoleMetric;
use App\Services\SearchConsoleService;
use Illuminate\Http\Request;

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
            ->orderByDesc('clicks')
            ->limit(15)
            ->get();

        $topQueries = (clone $queryMetrics)
            ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
            ->whereNotNull('query')
            ->groupBy('query')
            ->orderByDesc('clicks')
            ->limit(15)
            ->get();

        return view('admin.seo.search-console', compact(
            'days',
            'siteUrl',
            'type',
            'startDate',
            'endDate',
            'summary',
            'dailyPerformance',
            'topPages',
            'topQueries'
        ))->with([
            'isConfigured' => $searchConsole->isConfigured(),
        ]);
    }
}
