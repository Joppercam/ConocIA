<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_fondo', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->string('topic');                     // tema central del análisis
            $table->string('category')->nullable();      // área temática amplia
            $table->json('key_players')->nullable();     // [{name, role}]
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('views')->default(0);
            $table->integer('reading_time')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('topic');
            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_fondo');
    }
};
