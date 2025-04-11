<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear las nuevas tablas con estructura correcta
        Schema::create('tiktok_scripts_new2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_id'); // Cambiado de article_id a news_id
            $table->text('script_content');
            $table->text('visual_suggestions')->nullable();
            $table->string('hashtags')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'published'])->default('draft');
            $table->float('tiktok_score')->unsigned()->default(0);
            $table->text('ai_response_raw')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
        });
        
        Schema::create('tiktok_metrics_new2', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tiktok_script_id');
            $table->string('tiktok_video_id')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('clicks_to_portal')->default(0);
            $table->timestamps();
            
            $table->foreign('tiktok_script_id')->references('id')->on('tiktok_scripts_new2')->onDelete('cascade');
        });
        
        // Verificar si las tablas antiguas existen
        if (Schema::hasTable('tiktok_scripts') && Schema::hasTable('tiktok_metrics')) {
            // Si hay datos en las tablas antiguas, la estrategia sería migrarlos
            // Pero como estamos en desarrollo, simplemente eliminamos las antiguas
            Schema::dropIfExists('tiktok_metrics');
            Schema::dropIfExists('tiktok_scripts');
            
            // Renombrar las nuevas tablas
            Schema::rename('tiktok_scripts_new2', 'tiktok_scripts');
            Schema::rename('tiktok_metrics_new2', 'tiktok_metrics');
        } else {
            // Si las antiguas no existen, simplemente renombramos las nuevas
            Schema::rename('tiktok_scripts_new2', 'tiktok_scripts');
            Schema::rename('tiktok_metrics_new2', 'tiktok_metrics');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiktok_metrics');
        Schema::dropIfExists('tiktok_scripts');
    }
};