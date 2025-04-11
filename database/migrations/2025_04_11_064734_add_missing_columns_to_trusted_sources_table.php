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
        Schema::table('trusted_sources', function (Blueprint $table) {
            // Añadir las columnas que faltan
            if (!Schema::hasColumn('trusted_sources', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('trusted_sources', 'content_selector')) {
                $table->string('content_selector')->nullable();
            }
            
            if (!Schema::hasColumn('trusted_sources', 'reliability_score')) {
                $table->integer('reliability_score')->default(80);
            }
            
            // Añadir cualquier otra columna que pueda faltar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trusted_sources', function (Blueprint $table) {
            // Eliminar las columnas si es necesario revertir
            $table->dropColumn(['description', 'content_selector', 'reliability_score']);
        });
    }
};