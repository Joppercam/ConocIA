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
        // create_claims_table.php
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('statement', 512); // La afirmación a verificar
            $table->text('source_url')->nullable(); // URL de donde se extrajo
            $table->string('source_name'); // Nombre de la fuente (persona/organización)
            $table->string('source_type'); // Tipo: 'politician', 'media', 'social_media', etc.
            $table->timestamp('statement_date'); // Fecha de la afirmación
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->index(['is_verified', 'statement_date']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
