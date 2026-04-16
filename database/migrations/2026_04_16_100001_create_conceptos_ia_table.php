<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conceptos_ia', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->text('definition')->nullable();       // 1-2 oraciones para cards/sidebar
            $table->string('category')->nullable();       // e.g. "Machine Learning", "NLP"
            $table->json('related_concepts')->nullable(); // array de slugs
            $table->json('key_players')->nullable();      // [{name, role}]
            $table->json('further_reading')->nullable();  // [{title, description}]
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('views')->default(0);
            $table->integer('reading_time')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conceptos_ia');
    }
};
