<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('completed')->default(false);
            $table->integer('watch_seconds')->default(0);
            $table->timestamps();
            
            // Índice para consultas rápidas
            $table->index(['video_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_views');
    }
};