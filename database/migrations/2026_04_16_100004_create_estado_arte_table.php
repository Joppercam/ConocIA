<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_arte', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subfield');           // slug del campo: "computer-vision"
            $table->string('subfield_label');     // "Computer Vision"
            $table->string('period_label');       // "Semana del 14 al 20 de abril de 2026"
            $table->date('week_start');           // lunes de la semana cubierta
            $table->date('week_end');             // domingo de la semana cubierta
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->json('source_news_ids')->nullable();  // IDs de News usados como fuente
            $table->json('key_developments')->nullable(); // array de strings para preview
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->integer('views')->default(0);
            $table->integer('reading_time')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['subfield', 'week_start']);
            $table->unique(['subfield', 'week_start']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_arte');
    }
};
