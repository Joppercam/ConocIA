<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AdminDashboardCache
{
    public const STATS_KEY = 'admin_dashboard_stats';
    public const RECENT_NEWS_KEY = 'admin_dashboard_recent_news';
    public const POPULAR_NEWS_KEY = 'admin_dashboard_popular_news';
    public const PENDING_SOCIAL_KEY = 'admin_dashboard_pending_social_posts';
    public const DAILY_VIEWS_KEY = 'admin_dashboard_daily_views';
    public const TRENDING_NEWS_KEY = 'admin_dashboard_trending_news';
    public const VIEW_COMPARISON_KEY = 'admin_dashboard_view_comparison';
    public const CATEGORY_PERFORMANCE_KEY = 'admin_dashboard_category_performance';

    public static function clear(): void
    {
        Cache::forget(self::STATS_KEY);
        Cache::forget(self::RECENT_NEWS_KEY);
        Cache::forget(self::POPULAR_NEWS_KEY);
        Cache::forget(self::PENDING_SOCIAL_KEY);
        Cache::forget(self::DAILY_VIEWS_KEY);
        Cache::forget(self::TRENDING_NEWS_KEY);
        Cache::forget(self::VIEW_COMPARISON_KEY);
        Cache::forget(self::CATEGORY_PERFORMANCE_KEY);
    }
}
