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
        Schema::create('social_media_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->string('network', 50); // twitter, facebook, linkedin, etc.
            $table->string('post_id')->nullable(); // ID único del post en la red social
            $table->string('post_url')->nullable(); // URL directa al post
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('success'); // success, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas frecuentes
            $table->index('network');
            $table->index(['news_id', 'network']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_posts');
    }
};