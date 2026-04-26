<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $this->clearCaches();
    }

    public function down(): void
    {
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
        ] as $key) {
            Cache::forget($key);
        }
    }
};
