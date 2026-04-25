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

        $summary = $this->buildAnalyticsSummary($startDate, $endDate);
        $dailyViews = $this->buildDailyViews($startDate, $endDate);
        $comparisonSummary = $this->buildComparisonSummary($summary['period_views'], $previousStartDate, $previousEndDate);
        $strategicSections = $this->buildStrategicSections($startDate, $endDate, $previousStartDate, $previousEndDate);
        $topNews = $this->buildTopNews($startDate, $endDate, $previousStartDate, $previousEndDate, 15);
        $topCategories = $this->buildTopCategories($startDate, $endDate, $previousStartDate, $previousEndDate, 10);
        $topAuthors = $this->buildTopAuthors($startDate, $endDate, $previousStartDate, $previousEndDate, 10);

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
            'topAuthors'
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
