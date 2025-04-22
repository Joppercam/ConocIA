<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->timestamps();
            
            // Una palabra clave solo debe aparecer una vez por video
            $table->unique(['video_id', 'keyword']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_keywords');
    }
};
