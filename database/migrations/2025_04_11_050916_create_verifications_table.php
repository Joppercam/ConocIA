<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create_verifications_table.php
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->enum('verdict', ['true', 'partially_true', 'false', 'unverifiable']);
            $table->text('explanation'); // Explicación generada por IA
            $table->decimal('confidence_score', 5, 2); // Nivel de confianza entre 0 y 100
            $table->json('evidence_sources'); // Fuentes utilizadas como evidencia
            $table->timestamps();
            $table->index('verdict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
