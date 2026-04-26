<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\User;
use App\Models\SocialMediaQueue;
use App\Support\AdminDashboardCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Mostrar el panel de control
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Estadísticas generales
        $stats = Cache::remember(AdminDashboardCache::STATS_KEY, 300, function () {
            return [
                'total_news' => News::count(),
                'published_news' => News::published()->count(),
                'total_views' => News::sum('views'),
                'categories' => Category::count(),
                'users' => User::count(),
            ];
        });

        $recentNews = Cache::remember(AdminDashboardCache::RECENT_NEWS_KEY, 120, function () {
            return News::with('category')
                ->latest()
                ->take(5)
                ->get();
        });

        $popularNews = Cache::remember(AdminDashboardCache::POPULAR_NEWS_KEY, 120, function () {
            return News::with('category')
                ->published()
                ->orderByDesc('views')
                ->take(5)
                ->get();
        });

        $pendingSocialPosts = Cache::remember(AdminDashboardCache::PENDING_SOCIAL_KEY, 120, function () {
            return SocialMediaQueue::where('status', 'pending')
                ->with('news')
                ->latest()
                ->take(5)
                ->get();
        });

        $dailyViews = Cache::remember(AdminDashboardCache::DAILY_VIEWS_KEY, 300, function () {
            if (!Schema::hasTable('news_views_stats')) {
                return collect();
            }

            return DB::table('news_views_stats')
                ->selectRaw('view_date, SUM(views) as total_views')
                ->where('view_date', '>=', now()->subDays(6)->toDateString())
                ->groupBy('view_date')
                ->orderBy('view_date')
                ->get();
        });

        $trendingNews = Cache::remember(AdminDashboardCache::TRENDING_NEWS_KEY, 300, function () {
            if (!Schema::hasTable('news_views_stats')) {
                return collect();
            }

            return News::query()
                ->join('news_views_stats', 'news.id', '=', 'news_views_stats.news_id')
                ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
                ->selectRaw('news.id, news.title, news.slug, categories.name as category_name, SUM(news_views_stats.views) as recent_views')
                ->where('news_views_stats.view_date', '>=', now()->subDays(6)->toDateString())
                ->groupBy('news.id', 'news.title', 'news.slug', 'categories.name')
                ->orderByDesc('recent_views')
                ->take(5)
                ->get();
        });

        $viewComparison = Cache::remember(AdminDashboardCache::VIEW_COMPARISON_KEY, 300, function () {
            if (!Schema::hasTable('news_views_stats')) {
                return [
                    'today' => 0,
                    'yesterday' => 0,
                    'last_7_days' => 0,
                    'previous_7_days' => 0,
                ];
            }

            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            $last7Start = now()->subDays(6)->toDateString();
            $prev7Start = now()->subDays(13)->toDateString();
            $prev7End = now()->subDays(7)->toDateString();

            return [
                'today' => (int) DB::table('news_views_stats')->where('view_date', $today)->sum('views'),
                'yesterday' => (int) DB::table('news_views_stats')->where('view_date', $yesterday)->sum('views'),
                'last_7_days' => (int) DB::table('news_views_stats')->whereBetween('view_date', [$last7Start, $today])->sum('views'),
                'previous_7_days' => (int) DB::table('news_views_stats')->whereBetween('view_date', [$prev7Start, $prev7End])->sum('views'),
            ];
        });

        $categoryPerformance = Cache::remember(AdminDashboardCache::CATEGORY_PERFORMANCE_KEY, 300, function () {
            if (!Schema::hasTable('news_views_stats')) {
                return collect();
            }

            return DB::table('news_views_stats')
                ->join('news', 'news.id', '=', 'news_views_stats.news_id')
                ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
                ->selectRaw('COALESCE(categories.name, ?) as category_name, SUM(news_views_stats.views) as total_views', ['Sin categoría'])
                ->where('news_views_stats.view_date', '>=', now()->subDays(6)->toDateString())
                ->groupBy('categories.name')
                ->orderByDesc('total_views')
                ->limit(5)
                ->get();
        });

        return view('admin.dashboard', compact(
            'stats',
            'recentNews',
            'popularNews',
            'pendingSocialPosts',
            'dailyViews',
            'trendingNews',
            'viewComparison',
            'categoryPerformance'
        ));
    }

    public function analytics(Request $request)
    {
        [$startDate, $endDate] = $this->resolveAnalyticsRange($request);
        [$previousStartDate, $previousEndDate] = $this->resolvePreviousAnalyticsRange($startDate, $endDate);
        $selectedPreset = $this->resolveAnalyticsPreset($request);
        $visitAudience = $this->resolveVisitAudience($request);
        $visitType = $this->resolveVisitType($request);

        $summary = $this->buildAnalyticsSummary($startDate, $endDate);
        $dailyViews = $this->buildDailyViews($startDate, $endDate);
        $comparisonSummary = $this->buildComparisonSummary($summary['period_views'], $previousStartDate, $previousEndDate);
        $strategicSections = $this->buildStrategicSections($startDate, $endDate, $previousStartDate, $previousEndDate);
        $topNews = $this->buildTopNews($startDate, $endDate, $previousStartDate, $previousEndDate, 15);
        $topCategories = $this->buildTopCategories($startDate, $endDate, $previousStartDate, $previousEndDate, 10);
        $topAuthors = $this->buildTopAuthors($startDate, $endDate, $previousStartDate, $previousEndDate, 10);
        $visitSummary = $this->buildSiteVisitSummary($startDate, $endDate);
        $humanTopPages = $this->buildHumanTopPages($startDate, $endDate, 10);
        $humanTopChannels = $this->buildHumanTopChannels($startDate, $endDate, 8);
        $contentWithoutHumanViews = $this->buildContentWithoutHumanViews($startDate, $endDate, 12);
        $latestVisits = $this->buildLatestSiteVisits($startDate, $endDate, 40, $visitAudience, $visitType);

        return view('admin.analytics.news', compact(
            'startDate',
            'endDate',
            'previousStartDate',
            'previousEndDate',
            'selectedPreset',
            'summary',
            'comparisonSummary',
            'dailyViews',
            'strategicSections',
            'topNews',
            'topCategories',
            'topAuthors',
            'visitAudience',
            'visitType',
            'visitSummary',
            'humanTopPages',
            'humanTopChannels',
            'contentWithoutHumanViews',
            'latestVisits'
        ));
    }

    public function exportAnalytics(Request $request): StreamedResponse
    {
        [$startDate, $endDate] = $this->resolveAnalyticsRange($request);
        [$previousStartDate, $previousEndDate] = $this->resolvePreviousAnalyticsRange($startDate, $endDate);
        $topNews = $this->buildTopNews($startDate, $endDate, $previousStartDate, $previousEndDate, 100);

        $fileName = sprintf('analitica-noticias-%s-a-%s.csv', $startDate, $endDate);

        return response()->streamDownload(function () use ($topNews) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Titulo', 'Categoria', 'Visitas del periodo', 'Visitas totales']);

            foreach ($topNews as $news) {
                fputcsv($file, [
                    $news->id,
                    $news->title,
                    $news->category_name ?? 'Sin categoría',
                    $news->period_views,
                    $news->total_views,
                ]);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveAnalyticsRange(Request $request): array
    {
        $preset = $this->resolveAnalyticsPreset($request);

        if ($preset !== null) {
            return $this->resolvePresetDates($preset);
        }

        $endDate = $request->date('end_date')?->toDateString() ?? now()->toDateString();
        $startDate = $request->date('start_date')?->toDateString() ?? now()->subDays(6)->toDateString();

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$startDate, $endDate];
    }

    private function resolveAnalyticsPreset(Request $request): ?string
    {
        $preset = $request->string('preset')->toString();
        $allowedPresets = ['today', 'last_7_days', 'last_30_days', 'current_month', 'previous_month'];

        return in_array($preset, $allowedPresets, true) ? $preset : null;
    }

    private function resolveVisitAudience(Request $request): string
    {
        $audience = $request->string('visit_audience')->toString();

        return in_array($audience, ['all', 'humans', 'bots'], true) ? $audience : 'all';
    }

    private function resolveVisitType(Request $request): string
    {
        $type = $request->string('visit_type')->toString();
        $allowed = [
            'all',
            'noticia',
            'columna',
            'paper',
            'concepto',
            'analisis',
            'estado_del_arte',
            'startup',
            'investigacion',
            'video',
            'pagina',
        ];

        return in_array($type, $allowed, true) ? $type : 'all';
    }

    private function resolvePresetDates(string $preset): array
    {
        return match ($preset) {
            'today' => [now()->toDateString(), now()->toDateString()],
            'last_30_days' => [now()->subDays(29)->toDateString(), now()->toDateString()],
            'current_month' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'previous_month' => [
                now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            ],
            default => [now()->subDays(6)->toDateString(), now()->toDateString()],
        };
    }

    private function resolvePreviousAnalyticsRange(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end) + 1;

        $previousEnd = $start->copy()->subDay();
        $previousStart = $previousEnd->copy()->subDays($days - 1);

        return [$previousStart->toDateString(), $previousEnd->toDateString()];
    }

    private function buildAnalyticsSummary(string $startDate, string $endDate): array
    {
        if (!Schema::hasTable('news_views_stats')) {
            return [
                'period_views' => 0,
                'active_news' => 0,
                'average_views_per_day' => 0,
            ];
        }

        $periodViews = (int) DB::table('news_views_stats')
            ->whereBetween('view_date', [$startDate, $endDate])
            ->sum('views');

        $activeNews = (int) DB::table('news_views_stats')
            ->whereBetween('view_date', [$startDate, $endDate])
            ->distinct('news_id')
            ->count('news_id');

        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);

        return [
            'period_views' => $periodViews,
            'active_news' => $activeNews,
            'average_views_per_day' => (int) round($periodViews / $days),
        ];
    }

    private function buildDailyViews(string $startDate, string $endDate)
    {
        if (!Schema::hasTable('news_views_stats')) {
            return collect();
        }

        return DB::table('news_views_stats')
            ->selectRaw('view_date, SUM(views) as total_views')
            ->whereBetween('view_date', [$startDate, $endDate])
            ->groupBy('view_date')
            ->orderBy('view_date')
            ->get();
    }

    private function buildComparisonSummary(int $currentPeriodViews, string $previousStartDate, string $previousEndDate): array
    {
        if (!Schema::hasTable('news_views_stats')) {
            return [
                'previous_period_views' => 0,
                'delta_percentage' => null,
            ];
        }

        $previousPeriodViews = (int) DB::table('news_views_stats')
            ->whereBetween('view_date', [$previousStartDate, $previousEndDate])
            ->sum('views');

        return [
            'previous_period_views' => $previousPeriodViews,
            'delta_percentage' => $previousPeriodViews > 0
                ? (($currentPeriodViews - $previousPeriodViews) / $previousPeriodViews) * 100
                : null,
        ];
    }

    private function buildTopNews(string $startDate, string $endDate, string $previousStartDate, string $previousEndDate, int $limit)
    {
        if (!Schema::hasTable('news_views_stats')) {
            return collect();
        }

        $currentPeriodSubquery = DB::table('news_views_stats')
            ->selectRaw('news_id, SUM(views) as period_views')
            ->whereBetween('view_date', [$startDate, $endDate])
            ->groupBy('news_id');

        $previousPeriodSubquery = DB::table('news_views_stats')
            ->selectRaw('news_id, SUM(views) as previous_period_views')
            ->whereBetween('view_date', [$previousStartDate, $previousEndDate])
            ->groupBy('news_id');

        return DB::table('news')
            ->joinSub($currentPeriodSubquery, 'current_period_stats', function ($join) {
                $join->on('news.id', '=', 'current_period_stats.news_id');
            })
            ->leftJoinSub($previousPeriodSubquery, 'previous_period_stats', function ($join) {
                $join->on('news.id', '=', 'previous_period_stats.news_id');
            })
            ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
            ->selectRaw('news.id, news.title, news.views as total_views, COALESCE(categories.name, ?) as category_name, current_period_stats.period_views, COALESCE(previous_period_stats.previous_period_views, 0) as previous_period_views', ['Sin categoría'])
            ->orderByDesc('period_views')
            ->limit($limit)
            ->get();
    }

    private function buildTopCategories(string $startDate, string $endDate, string $previousStartDate, string $previousEndDate, int $limit)
    {
        if (!Schema::hasTable('news_views_stats')) {
            return collect();
        }

        $currentPeriodSubquery = DB::table('news_views_stats')
            ->join('news', 'news.id', '=', 'news_views_stats.news_id')
            ->whereBetween('news_views_stats.view_date', [$startDate, $endDate])
            ->selectRaw('news.category_id, SUM(news_views_stats.views) as period_views')
            ->groupBy('news.category_id');

        $previousPeriodSubquery = DB::table('news_views_stats')
            ->join('news', 'news.id', '=', 'news_views_stats.news_id')
            ->whereBetween('news_views_stats.view_date', [$previousStartDate, $previousEndDate])
            ->selectRaw('news.category_id, SUM(news_views_stats.views) as previous_period_views')
            ->groupBy('news.category_id');

        return DB::table('categories')
            ->rightJoinSub($currentPeriodSubquery, 'current_period_stats', function ($join) {
                $join->on('categories.id', '=', 'current_period_stats.category_id');
            })
            ->leftJoinSub($previousPeriodSubquery, 'previous_period_stats', function ($join) {
                $join->on('current_period_stats.category_id', '=', 'previous_period_stats.category_id');
            })
            ->selectRaw('COALESCE(categories.name, ?) as category_name, current_period_stats.period_views, COALESCE(previous_period_stats.previous_period_views, 0) as previous_period_views', ['Sin categoría'])
            ->orderByDesc('current_period_stats.period_views')
            ->limit($limit)
            ->get();
    }

    private function buildTopAuthors(string $startDate, string $endDate, string $previousStartDate, string $previousEndDate, int $limit)
    {
        if (!Schema::hasTable('news_views_stats')) {
            return collect();
        }

        $currentPeriodSubquery = DB::table('news_views_stats')
            ->join('news', 'news.id', '=', 'news_views_stats.news_id')
            ->whereBetween('news_views_stats.view_date', [$startDate, $endDate])
            ->selectRaw('news.user_id, SUM(news_views_stats.views) as period_views, COUNT(DISTINCT news.id) as news_count')
            ->groupBy('news.user_id');

        $previousPeriodSubquery = DB::table('news_views_stats')
            ->join('news', 'news.id', '=', 'news_views_stats.news_id')
            ->whereBetween('news_views_stats.view_date', [$previousStartDate, $previousEndDate])
            ->selectRaw('news.user_id, SUM(news_views_stats.views) as previous_period_views')
            ->groupBy('news.user_id');

        return DB::table('users')
            ->rightJoinSub($currentPeriodSubquery, 'current_period_stats', function ($join) {
                $join->on('users.id', '=', 'current_period_stats.user_id');
            })
            ->leftJoinSub($previousPeriodSubquery, 'previous_period_stats', function ($join) {
                $join->on('current_period_stats.user_id', '=', 'previous_period_stats.user_id');
            })
            ->selectRaw('COALESCE(users.name, ?) as author_name, current_period_stats.period_views, current_period_stats.news_count, COALESCE(previous_period_stats.previous_period_views, 0) as previous_period_views', ['Sin autor'])
            ->orderByDesc('current_period_stats.period_views')
            ->limit($limit)
            ->get();
    }

    private function buildStrategicSections(string $startDate, string $endDate, string $previousStartDate, string $previousEndDate)
    {
        if (!Schema::hasTable('news_views_stats')) {
            return collect();
        }

        $rows = DB::table('news_views_stats')
            ->join('news', 'news.id', '=', 'news_views_stats.news_id')
            ->leftJoin('categories', 'news.category_id', '=', 'categories.id')
            ->selectRaw("
                news.id,
                news.title,
                news.slug,
                COALESCE(categories.slug, '') as category_slug,
                COALESCE(categories.name, 'Sin categoría') as category_name,
                SUM(CASE WHEN news_views_stats.view_date BETWEEN ? AND ? THEN news_views_stats.views ELSE 0 END) as period_views,
                SUM(CASE WHEN news_views_stats.view_date BETWEEN ? AND ? THEN news_views_stats.views ELSE 0 END) as previous_period_views
            ", [$startDate, $endDate, $previousStartDate, $previousEndDate])
            ->whereBetween('news_views_stats.view_date', [$previousStartDate, $endDate])
            ->groupBy('news.id', 'news.title', 'news.slug', 'categories.slug', 'categories.name')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $classified = $rows
            ->map(function ($row) {
                $section = $this->resolveStrategicSection((string) $row->title, (string) $row->slug, (string) $row->category_slug);

                return (object) [
                    'section' => $section,
                    'news_id' => $row->id,
                    'period_views' => (int) $row->period_views,
                    'previous_period_views' => (int) $row->previous_period_views,
                ];
            });

        $totalViews = max(1, (int) $classified->sum('period_views'));

        return $classified
            ->groupBy('section')
            ->map(function ($rows, $section) use ($totalViews) {
                $periodViews = (int) $rows->sum('period_views');
                $previousPeriodViews = (int) $rows->sum('previous_period_views');

                return (object) [
                    'section' => $section,
                    'period_views' => $periodViews,
                    'previous_period_views' => $previousPeriodViews,
                    'news_count' => $rows->pluck('news_id')->unique()->count(),
                    'share' => $periodViews / $totalViews,
                    'delta_percentage' => $previousPeriodViews > 0
                        ? (($periodViews - $previousPeriodViews) / $previousPeriodViews) * 100
                        : null,
                ];
            })
            ->sortByDesc('period_views')
            ->values();
    }

    private function buildLatestSiteVisits(string $startDate, string $endDate, int $limit, string $audience = 'all', string $type = 'all')
    {
        if (!Schema::hasTable('site_visit_events')) {
            return collect();
        }

        return $this->siteVisitBaseQuery($startDate, $endDate)
            ->select([
                'id',
                'content_type',
                'content_id',
                'title',
                'url',
                'route_name',
                'referrer',
                'ip_hash',
                'user_agent',
                'is_bot',
                'viewed_at',
            ])
            ->when($audience === 'humans', fn($query) => $query->where('is_bot', false))
            ->when($audience === 'bots', fn($query) => $query->where('is_bot', true))
            ->when($type !== 'all', fn($query) => $query->where('content_type', $type))
            ->orderByDesc('viewed_at')
            ->limit($limit)
            ->get()
            ->map(function ($visit) {
                $visit->section_label = $this->visitSectionLabel((string) $visit->content_type);
                $visit->referrer_label = $this->referrerLabel($visit->referrer);
                $visit->channel_label = $this->channelLabel($visit->referrer, (bool) $visit->is_bot);
                $visit->visitor_label = $visit->ip_hash ? Str::substr($visit->ip_hash, 0, 8) : 'sin-ip';

                return $visit;
            });
    }

    private function buildSiteVisitSummary(string $startDate, string $endDate): array
    {
        if (!Schema::hasTable('site_visit_events')) {
            return [
                'total' => 0,
                'humans' => 0,
                'bots' => 0,
                'unique_pages' => 0,
                'top_channels' => collect(),
            ];
        }

        $base = $this->siteVisitBaseQuery($startDate, $endDate);

        $rows = (clone $base)
            ->select(['referrer', 'is_bot'])
            ->get();

        $topChannels = $rows
            ->map(fn($row) => $this->channelLabel($row->referrer, (bool) $row->is_bot))
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(fn($count, $channel) => (object) ['channel' => $channel, 'count' => $count])
            ->values();

        return [
            'total' => (int) (clone $base)->count(),
            'humans' => (int) (clone $base)->where('is_bot', false)->count(),
            'bots' => (int) (clone $base)->where('is_bot', true)->count(),
            'unique_pages' => (int) (clone $base)->distinct('url')->count('url'),
            'top_channels' => $topChannels,
        ];
    }

    private function buildHumanTopPages(string $startDate, string $endDate, int $limit)
    {
        if (!Schema::hasTable('site_visit_events')) {
            return collect();
        }

        return $this->siteVisitBaseQuery($startDate, $endDate)
            ->where('is_bot', false)
            ->selectRaw('
                COALESCE(content_type, ?) as content_type,
                COALESCE(title, url) as title,
                url,
                COUNT(*) as human_views,
                COUNT(DISTINCT ip_hash) as unique_visitors,
                MAX(viewed_at) as last_viewed_at
            ', ['pagina'])
            ->groupBy('content_type', 'title', 'url')
            ->orderByDesc('human_views')
            ->orderByDesc('last_viewed_at')
            ->limit($limit)
            ->get()
            ->map(function ($page) {
                $page->section_label = $this->visitSectionLabel((string) $page->content_type);

                return $page;
            });
    }

    private function buildHumanTopChannels(string $startDate, string $endDate, int $limit)
    {
        if (!Schema::hasTable('site_visit_events')) {
            return collect();
        }

        return $this->siteVisitBaseQuery($startDate, $endDate)
            ->where('is_bot', false)
            ->select(['referrer'])
            ->get()
            ->map(fn($row) => $this->channelLabel($row->referrer, false))
            ->countBy()
            ->sortDesc()
            ->take($limit)
            ->map(fn($count, $channel) => (object) ['channel' => $channel, 'count' => $count])
            ->values();
    }

    private function buildContentWithoutHumanViews(string $startDate, string $endDate, int $limit)
    {
        if (!Schema::hasTable('site_visit_events')) {
            return collect();
        }

        $visited = DB::table('site_visit_events')
            ->where('is_bot', false)
            ->whereNotNull('content_type')
            ->whereNotNull('content_id')
            ->whereBetween('viewed_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->selectRaw('content_type, content_id')
            ->distinct();

        $items = collect()
            ->concat($this->unreadContentQuery('news', 'noticia', 'title', 'slug', 'published_at', 'status', 'published', 'news.show', $visited))
            ->concat($this->unreadContentQuery('columns', 'columna', 'title', 'slug', 'published_at', null, null, 'columns.show', $visited))
            ->concat($this->unreadContentQuery('conocia_papers', 'paper', 'title', 'slug', 'published_at', 'status', 'published', 'papers.show', $visited))
            ->concat($this->unreadContentQuery('conceptos_ia', 'concepto', 'title', 'slug', 'published_at', 'status', 'published', 'conceptos.show', $visited))
            ->concat($this->unreadContentQuery('analisis_fondo', 'analisis', 'title', 'slug', 'published_at', 'status', 'published', 'analisis.show', $visited))
            ->concat($this->unreadContentQuery('startups', 'startup', 'name', 'slug', 'updated_at', 'active', 1, 'startups.show', $visited));

        return $items
            ->sortByDesc('published_at')
            ->take($limit)
            ->values();
    }

    private function unreadContentQuery(
        string $table,
        string $type,
        string $titleColumn,
        string $slugColumn,
        string $dateColumn,
        ?string $statusColumn,
        mixed $statusValue,
        string $routeName,
        $visitedSubquery
    ) {
        if (!Schema::hasTable($table)) {
            return collect();
        }

        $query = DB::table($table)
            ->leftJoinSub(clone $visitedSubquery, 'human_visits', function ($join) use ($table, $type) {
                $join->on("{$table}.id", '=', 'human_visits.content_id')
                    ->where('human_visits.content_type', '=', $type);
            })
            ->whereNull('human_visits.content_id')
            ->select([
                "{$table}.id",
                "{$table}.{$titleColumn} as title",
                "{$table}.{$slugColumn} as slug",
                "{$table}.{$dateColumn} as published_at",
            ])
            ->selectRaw('? as content_type', [$type])
            ->orderByDesc("{$table}.{$dateColumn}")
            ->limit(8);

        if ($statusColumn !== null) {
            $query->where("{$table}.{$statusColumn}", $statusValue);
        }

        return $query->get()->map(function ($item) use ($routeName, $type) {
            $item->section_label = $this->visitSectionLabel($type);
            $item->url = route($routeName, $item->slug);

            return $item;
        });
    }

    private function siteVisitBaseQuery(string $startDate, string $endDate)
    {
        return DB::table('site_visit_events')
            ->whereBetween('viewed_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
    }

    private function visitSectionLabel(string $type): string
    {
        return match ($type) {
            'noticia', 'news' => 'Noticia',
            'columna', 'columns' => 'Columna',
            'paper', 'papers' => 'Paper',
            'concepto', 'conceptos' => 'Concepto IA',
            'analisis' => 'Análisis',
            'estado_del_arte', 'estado_arte' => 'Estado del Arte',
            'startup', 'startups' => 'Startup',
            'investigacion', 'research' => 'Investigación',
            'video', 'videos' => 'Video',
            'pagina' => 'Página',
            default => Str::headline(str_replace('_', ' ', $type ?: 'pagina')),
        };
    }

    private function referrerLabel(?string $referrer): string
    {
        if (!$referrer) {
            return 'Directo';
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        if (!$host) {
            return 'Referido';
        }

        return Str::of($host)
            ->replace('www.', '')
            ->limit(32)
            ->toString();
    }

    private function channelLabel(?string $referrer, bool $isBot): string
    {
        if ($isBot) {
            return 'Bot / crawler';
        }

        if (!$referrer) {
            return 'Directo';
        }

        $host = Str::lower((string) parse_url($referrer, PHP_URL_HOST));

        if ($host === '') {
            return 'Referido';
        }

        if (Str::contains($host, ['conocia.cl'])) {
            return 'Interno';
        }

        if (Str::contains($host, ['google.', 'bing.', 'duckduckgo.', 'yahoo.', 'ecosia.'])) {
            return 'Buscador';
        }

        if (Str::contains($host, ['linkedin.', 'lnkd.in'])) {
            return 'LinkedIn';
        }

        if (Str::contains($host, ['twitter.', 'x.com', 't.co'])) {
            return 'X';
        }

        if (Str::contains($host, ['facebook.', 'instagram.', 'threads.'])) {
            return 'Meta';
        }

        return 'Referido';
    }

    private function resolveStrategicSection(string $title, string $slug, string $categorySlug): string
    {
        $title = mb_strtolower($title);
        $slug = Str::lower($slug);
        $categorySlug = Str::lower($categorySlug);

        if ($categorySlug === 'ia-en-chile') {
            return 'IA en Chile';
        }

        if (
            Str::contains($title, ['guia', 'comparativa', 'qué es', 'que es', 'para qué sirve', 'para que sirve', 'mejores', 'ideal para'])
            || Str::contains($slug, ['guia-', 'comparativa-', '-vs-', 'que-es-', 'para-que-sirve', 'mejores-', 'ideal-'])
        ) {
            return 'Guías y Comparativas';
        }

        if (in_array($categorySlug, ['openai', 'google-ai', 'anthropic', 'meta-ai'], true)) {
            return 'Modelos y Empresas';
        }

        if (in_array($categorySlug, ['regulacion-de-ia', 'etica-de-la-ia', 'privacidad-y-seguridad', 'impacto-laboral'], true)) {
            return 'Regulación y Ética';
        }

        return 'Noticias Generales';
    }
}
