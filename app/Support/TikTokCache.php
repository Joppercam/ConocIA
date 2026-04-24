<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class TikTokCache
{
    public const DASHBOARD_KEY = 'tiktok_dashboard_stats';
    public const DAILY_STATS_KEY = 'tiktok_daily_stats';
    public const CATEGORY_STATS_KEY = 'tiktok_category_stats';
    public const TOP_VIDEOS_KEY = 'tiktok_top_videos';

    public static function clearDashboard(): void
    {
        Cache::forget(self::DASHBOARD_KEY);
    }

    public static function clearStats(): void
    {
        Cache::forget(self::DAILY_STATS_KEY);
        Cache::forget(self::CATEGORY_STATS_KEY);
        Cache::forget(self::TOP_VIDEOS_KEY);
    }

    public static function clearAll(): void
    {
        self::clearDashboard();
        self::clearStats();
    }
}
