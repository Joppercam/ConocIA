<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $slug = 'ia-agentes-ciberseguridad-cyber-threat-inflation';

    public function up(): void
    {
        if (!Schema::hasTable('conocia_papers')) {
            return;
        }

        $now = Carbon::now();

        DB::table('conocia_papers')->update([
            'featured' => false,
            'updated_at' => $now,
        ]);

        DB::table('conocia_papers')
            ->where('slug', $this->slug)
            ->update([
                'featured' => true,
                'published_at' => $now,
                'updated_at' => $now,
            ]);

        $this->clearCaches();
    }

    public function down(): void
    {
        if (!Schema::hasTable('conocia_papers')) {
            return;
        }

        DB::table('conocia_papers')
            ->where('slug', $this->slug)
            ->update([
                'featured' => false,
                'updated_at' => Carbon::now(),
            ]);

        $this->clearCaches();
    }

    private function clearCaches(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'home_latest_papers',
            'home_featured_paper',
            'papers_featured',
            'papers_arxiv_cats',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
