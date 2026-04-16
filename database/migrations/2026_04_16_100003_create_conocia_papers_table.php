<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conocia_papers', function (Blueprint $table) {
            $table->id();
            // Metadata original de arXiv
            $table->string('arxiv_id')->unique();
            $table->string('arxiv_url');
            $table->string('original_title');
            $table->longText('original_abstract');
            $table->json('authors');
            $table->date('arxiv_published_date');
            $table->string('arxiv_category')->nullable();   // e.g. "cs.AI", "cs.LG"
            // Contenido editorial generado por IA
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();        // resumen en español HTML
            $table->json('key_contributions')->nullable();  // array de strings
            $table->json('practical_implications')->nullable();
            $table->string('difficulty_level')->nullable(); // básico / intermedio / avanzado
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('views')->default(0);
            $table->integer('reading_time')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'arxiv_published_date']);
            $table->index('arxiv_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conocia_papers');
    }
};
