<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term');
            $table->string('slug')->unique();
            $table->string('letter', 1);
            $table->text('definition');
            $table->text('explanation')->nullable();
            $table->string('difficulty_level')->default('basico');
            $table->string('related_concept_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glossary_terms');
    }
};
