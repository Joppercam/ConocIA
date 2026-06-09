<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Duplicate check en RSS (50+ veces por run)
            $table->index('source_url', 'news_source_url_idx');

            // Scope published() — columna más consultada
            $table->index('status', 'news_status_idx');

            // Ordenamiento y filtros de fecha
            $table->index('published_at', 'news_published_at_idx');

            // Filtros de featured en home y hero
            $table->index('featured', 'news_featured_idx');

            // Filtros de categoría
            $table->index('category_id', 'news_category_id_idx');

            // Índice compuesto: patrón más frecuente (published + fecha)
            $table->index(['status', 'published_at'], 'news_status_published_at_idx');

            // Índice compuesto: home hero (published + featured + fecha)
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
