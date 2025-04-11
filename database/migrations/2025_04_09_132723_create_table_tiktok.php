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
        // Tabla para manejar guiones de TikTok
        Schema::create('tiktok_scripts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->text('script_content'); // El guión generado
            $table->text('visual_suggestions')->nullable(); // Sugerencias para elementos visuales
            $table->string('hashtags')->nullable(); // Hashtags sugeridos
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'published'])->default('draft');
            $table->float('tiktok_score')->unsigned()->default(0); // Puntaje para priorización (corregido)
            $table->text('ai_response_raw')->nullable(); // Respuesta cruda de la IA para debugging
            $table->timestamp('published_at')->nullable(); // Cuando se publicó en TikTok
            $table->timestamps();
            
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
        
        // Tabla para métricas de rendimiento de los videos
        Schema::create('tiktok_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tiktok_script_id');
            $table->string('tiktok_video_id')->nullable(); // ID del video en TikTok
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('clicks_to_portal')->default(0); // Tráfico generado hacia el portal
            $table->timestamps();
            
            $table->foreign('tiktok_script_id')->references('id')->on('tiktok_scripts')->onDelete('cascade');
        });
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