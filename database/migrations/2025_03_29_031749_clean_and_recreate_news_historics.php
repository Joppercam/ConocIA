<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero eliminamos posibles tablas temporales
        try {
            DB::statement('DROP TABLE IF EXISTS "__temp__news_historics"');
        } catch (\Exception $e) {
            // Ignorar errores
        }

        // Eliminar vista si existe
        DB::statement('DROP VIEW IF EXISTS all_news_view');
        
        // Eliminar tabla original si existe
        Schema::dropIfExists('news_historics');
        
        // Crear nueva tabla con estructura correcta
        DB::statement('
            CREATE TABLE news_historics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR NOT NULL,
                slug VARCHAR NOT NULL,
                excerpt TEXT,
                content TEXT NOT NULL,
                summary TEXT,
                image VARCHAR,
                image_caption VARCHAR,
                category_id INTEGER NOT NULL,
                author_id INTEGER,
                views INTEGER NOT NULL DEFAULT 0,
                status VARCHAR NOT NULL,
                tags VARCHAR,
                featured BOOLEAN NOT NULL DEFAULT 0,
                source VARCHAR,
                source_url VARCHAR,
                published_at DATETIME,
                reading_time INTEGER,
                original_id INTEGER NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY(category_id) REFERENCES categories(id),
                FOREIGN KEY(author_id) REFERENCES users(id)
            )
        ');
        
        // Crear índice para original_id
        DB::statement('CREATE INDEX news_historics_original_id_index ON news_historics (original_id)');
        
        // Recrear vista
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
        DB::statement('DROP VIEW IF EXISTS all_news_view');
        Schema::dropIfExists('news_historics');
    }
};