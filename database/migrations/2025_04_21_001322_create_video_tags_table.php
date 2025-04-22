<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        
        Schema::create('video_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_tag_id')->constrained('video_tags')->onDelete('cascade');
            $table->timestamps();
            
            // Un tag solo debe aparecer una vez por video
            $table->unique(['video_id', 'video_tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_tag');
        Schema::dropIfExists('video_tags');
    }
};