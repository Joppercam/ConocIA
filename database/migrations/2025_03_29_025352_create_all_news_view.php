<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            CREATE VIEW all_news_view AS
            SELECT id, title, slug, excerpt, content, summary, image, image_caption, 
                   category_id, author_id, views, status, tags, featured, source, 
                   source_url, published_at, reading_time, created_at, updated_at, 
                   'active' as source_table
            FROM news
            UNION ALL
            SELECT original_id as id, title, slug, excerpt, content, summary, image, 
                   image_caption, category_id, author_id, views, status, tags, 
                   featured, source, source_url, published_at, reading_time, 
                   created_at, updated_at, 'historic' as source_table
            FROM news_historics
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS all_news_view");
    }
};