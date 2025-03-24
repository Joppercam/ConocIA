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
        Schema::table('research', function (Blueprint $table) {
            // Verificar si la columna no existe antes de crearla
            if (!Schema::hasColumn('research', 'author_email')) {
                $table->string('author_email')->nullable()->after('author');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research', function (Blueprint $table) {
            // Solo eliminar si existe
            if (Schema::hasColumn('research', 'author_email')) {
                $table->dropColumn('author_email');
            }
        });
    }
};