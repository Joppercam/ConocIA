<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->integer('relevance_score')->default(0);
            $table->timestamps();
            
            // Un video solo debe estar una vez en cada noticia
            $table->unique(['news_id', 'video_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_video');
    }
};