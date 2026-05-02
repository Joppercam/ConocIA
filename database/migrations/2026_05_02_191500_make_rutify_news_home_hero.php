<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $slug = 'rutify-chile-anci-presunta-filtracion-datos-servicios-publicos';

    public function up(): void
    {
        if (!Schema::hasTable('news')) {
            return;
        }

        $now = Carbon::now();

        DB::table('news')->update(['featured' => false]);

        $payload = [
            'featured' => true,
            'image' => 'images/news/hero-news-3.jpg',
            'published_at' => $now,
            'updated_at' => $now,
        ];

        if (Schema::hasColumn('news', 'access_level')) {
            $payload['access_level'] = 'free';
        }

        if (Schema::hasColumn('news', 'is_premium')) {
            $payload['is_premium'] = false;
        }

        DB::table('news')
            ->where('slug', $this->slug)
            ->update($payload);

        $this->clearHomeCache();
    }

    public function down(): void
    {
        if (!Schema::hasTable('news')) {
            return;
        }

        DB::table('news')
            ->where('slug', $this->slug)
            ->update([
                'featured' => false,
                'image' => null,
                'updated_at' => Carbon::now(),
            ]);

        $this->clearHomeCache();
    }

    private function clearHomeCache(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'all_published_news',
            'all_published_news_v2',
            'popular_news',
            'secondary_news',
            'trending_ids',
            'news_index_list',
            'most_read_articles',
            'popular_tags',
            'all_categories',
            'featured_categories',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
