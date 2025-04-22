<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('video_platforms');
            $table->string('external_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail_url');
            $table->string('embed_url');
            $table->string('original_url');
            $table->timestamp('published_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            // Un video debe ser único por plataforma y ID externo
            $table->unique(['platform_id', 'external_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('videos');
    }
};