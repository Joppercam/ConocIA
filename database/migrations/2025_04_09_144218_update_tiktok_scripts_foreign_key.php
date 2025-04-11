<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo si la tabla existe y tiene la columna article_id
        if (Schema::hasTable('tiktok_scripts') && Schema::hasColumn('tiktok_scripts', 'article_id')) {
            Schema::table('tiktok_scripts', function (Blueprint $table) {
                // Eliminar la foreign key existente
                $table->dropForeign(['article_id']);
                
                // Renombrar la columna
                $table->renameColumn('article_id', 'news_id');
            });
            
            // Añadir la nueva foreign key (en un paso separado porque SQLite no permite 
            // añadir constraints en un ALTER TABLE con renameColumn)
            Schema::table('tiktok_scripts', function (Blueprint $table) {
                $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tiktok_scripts') && Schema::hasColumn('tiktok_scripts', 'news_id')) {
            Schema::table('tiktok_scripts', function (Blueprint $table) {
                // Eliminar la foreign key
                $table->dropForeign(['news_id']);
                
                // Renombrar de vuelta
                $table->renameColumn('news_id', 'article_id');
            });
            
            // Añadir de nuevo la foreign key anterior
            Schema::table('tiktok_scripts', function (Blueprint $table) {
                $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
            });
        }
    }
};