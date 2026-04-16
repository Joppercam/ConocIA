<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        // Crear nueva tabla con estructura compatible con todos los motores
        Schema::create('news_historics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->string('image_caption')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('views')->default(0);
            $table->string('status');
            $table->string('tags')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->integer('reading_time')->nullable();
            $table->unsignedBigInteger('original_id')->index();
            $table->timestamps();
        });
        
        // Recrear vista (si el esquema actual lo permite)
        try {
            DB::statement(" 
                CREATE VIEW all_news_view AS
                SELECT id, title, slug, excerpt, content, summary, image, NULL as image_caption, 
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
        } catch (\Exception $e) {
            // En algunos entornos la vista legacy no es compatible; no bloquear migraciones.
        }
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS all_news_view');
        Schema::dropIfExists('news_historics');
    }
};