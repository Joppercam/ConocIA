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
        Schema::create('social_media_queue', function (Blueprint $table) {
            $table->id();
            $table->string('network'); // twitter, facebook, linkedin, etc.
            $table->text('content');
            $table->text('media_paths')->nullable();
            $table->foreignId('news_id')->nullable()->constrained('news')->onDelete('set null');
            $table->string('status'); // pending, published, failed
            $table->string('manual_url')->nullable(); // URL para publicación manual
            $table->string('post_id')->nullable(); // ID del post en la red social
            $table->string('post_url')->nullable(); // URL del post publicado
            $table->text('error_message')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_queue');
    }
};