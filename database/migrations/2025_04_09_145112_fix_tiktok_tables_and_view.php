<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la vista problemática
        DB::statement('DROP VIEW IF EXISTS all_news_view');
        
        // Ahora puedes crear tus tablas sin problemas
        DB::statement('CREATE TABLE IF NOT EXISTS tiktok_scripts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            news_id INTEGER,
            script_content TEXT,
            visual_suggestions TEXT NULL,
            hashtags VARCHAR NULL,
            status VARCHAR DEFAULT "draft",
            tiktok_score FLOAT DEFAULT 0,
            ai_response_raw TEXT NULL,
            published_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
        )');
        
        DB::statement('CREATE TABLE IF NOT EXISTS tiktok_metrics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tiktok_script_id INTEGER,
            tiktok_video_id VARCHAR NULL,
            views INTEGER DEFAULT 0,
            likes INTEGER DEFAULT 0,
            comments INTEGER DEFAULT 0,
            shares INTEGER DEFAULT 0,
            clicks_to_portal INTEGER DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (tiktok_script_id) REFERENCES tiktok_scripts (id) ON DELETE CASCADE
        )');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS tiktok_metrics');
        DB::statement('DROP TABLE IF EXISTS tiktok_scripts');
        
        // Opcional: recrear la vista si sabes su estructura correcta
        // DB::statement('CREATE VIEW all_news_view AS SELECT... (estructura correcta de la vista)');
    }
};