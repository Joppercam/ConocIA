<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // source_url es TEXT — MySQL requiere longitud de prefijo para indexar TEXT/BLOB
        DB::statement('ALTER TABLE `news` ADD INDEX `news_source_url_idx` (`source_url`(500))');

        Schema::table('news', function (Blueprint $table) {
            $table->index('status', 'news_status_idx');
            $table->index('published_at', 'news_published_at_idx');
            $table->index('featured', 'news_featured_idx');
            $table->index('category_id', 'news_category_id_idx');
            $table->index(['status', 'published_at'], 'news_status_published_at_idx');
            $table->index(['status', 'featured', 'published_at'], 'news_status_featured_published_idx');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_source_url_idx');
            $table->dropIndex('news_status_idx');
            $table->dropIndex('news_published_at_idx');
            $table->dropIndex('news_featured_idx');
            $table->dropIndex('news_category_id_idx');
            $table->dropIndex('news_status_published_at_idx');
            $table->dropIndex('news_status_featured_published_idx');
        });
    }
};
