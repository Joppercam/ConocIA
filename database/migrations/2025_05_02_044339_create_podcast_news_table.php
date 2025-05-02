<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodcastNewsTable extends Migration
{
    public function up()
    {
        Schema::create('podcast_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Índice para mejorar el rendimiento
            $table->unique(['podcast_id', 'news_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('podcast_news');
    }
}