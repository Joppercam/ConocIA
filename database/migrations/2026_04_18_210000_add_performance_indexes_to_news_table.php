<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // scopePublished() filtra por status + published_at en casi todas las queries
            $table->index(['status', 'published_at'], 'news_status_published_at');

            // ordenar por views (briefing, home, trending)
            $table->index('views', 'news_views');

            // filtros de home page y destacados
            $table->index('featured', 'news_featured');

            // category_id ya tiene índice por FK pero lo agregamos explícito si no existe
            // (MySQL crea uno automáticamente con la FK, así que lo omitimos)
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('news_status_published_at');
            $table->dropIndex('news_views');
            $table->dropIndex('news_featured');
        });
    }
};
