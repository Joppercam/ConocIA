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
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->string('network');
            $table->text('content');
            $table->json('media_paths')->nullable();
            $table->enum('status', ['pending', 'published', 'failed'])->default('pending');
            $table->string('manual_url')->nullable();
            $table->string('post_id')->nullable();
            $table->string('post_url')->nullable();
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
