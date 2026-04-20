<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // GPT-4o
            $table->string('slug')->unique();                // gpt-4o
            $table->string('company');                       // OpenAI
            $table->string('company_slug');                  // openai
            $table->string('logo')->nullable();              // URL logo
            $table->string('type')->default('llm');          // llm, image, audio, multimodal
            $table->enum('access', ['closed', 'open', 'api-only'])->default('closed');
            $table->string('release_date')->nullable();      // "Mayo 2024"

            // Capacidades (boolean)
            $table->boolean('cap_text')->default(true);
            $table->boolean('cap_image_input')->default(false);
            $table->boolean('cap_image_output')->default(false);
            $table->boolean('cap_code')->default(false);
            $table->boolean('cap_voice')->default(false);
            $table->boolean('cap_web_search')->default(false);
            $table->boolean('cap_files')->default(false);
            $table->boolean('cap_reasoning')->default(false);

            // Especificaciones
            $table->unsignedInteger('context_window')->nullable();  // tokens
            $table->unsignedBigInteger('parameters')->nullable();   // billones
            $table->string('context_window_label')->nullable();     // "128K"

            // Precios (por millón de tokens, USD)
            $table->decimal('price_input', 8, 4)->nullable();
            $table->decimal('price_output', 8, 4)->nullable();
            $table->boolean('has_free_tier')->default(false);

            // Benchmarks
            $table->decimal('score_mmlu', 5, 2)->nullable();
            $table->decimal('score_humaneval', 5, 2)->nullable();
            $table->decimal('score_math', 5, 2)->nullable();

            // Editorial
            $table->text('description')->nullable();
            $table->string('official_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
