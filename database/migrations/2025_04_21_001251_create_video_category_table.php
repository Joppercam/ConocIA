<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Un video solo debe estar una vez en cada categoría
            $table->unique(['video_id', 'video_category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_category');
    }
};