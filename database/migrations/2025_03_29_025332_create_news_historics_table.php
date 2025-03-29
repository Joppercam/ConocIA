<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_historics', function (Blueprint $table) {
            $table->id();
            // Estructura idéntica a la tabla news
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->string('image')->nullable();
            $table->string('image_caption')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('author_id')->constrained('users');
            $table->integer('views')->default(0);
            $table->string('status');
            $table->string('tags')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('reading_time')->nullable();
            
            // Campo adicional para mantener referencia
            $table->unsignedBigInteger('original_id');
            $table->index('original_id');
            
            // Índices para búsquedas comunes
            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status']);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_historics');
    }
};