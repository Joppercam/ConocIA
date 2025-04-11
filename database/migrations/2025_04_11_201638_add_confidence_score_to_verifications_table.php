<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddConfidenceScoreToVerificationsTable extends Migration
{
    public function up()
    {
        Schema::table('verifications', function (Blueprint $table) {
            // Verificar si el campo ya existe
            if (!Schema::hasColumn('verifications', 'confidence_score')) {
                // Si no existe, lo creamos y lo hacemos nullable primero para evitar problemas con registros existentes
                $table->float('confidence_score')->nullable();
            }
        });
        
        // Ahora actualizamos los registros existentes con un valor por defecto
        DB::table('verifications')->whereNull('confidence_score')->update(['confidence_score' => 0.5]);
        
        // Finalmente, si queremos hacer que el campo sea NOT NULL después de poblar los datos
        Schema::table('verifications', function (Blueprint $table) {
            $table->float('confidence_score')->nullable(false)->default(0.5)->change();
        });
    }

    public function down()
    {
        Schema::table('verifications', function (Blueprint $table) {
            if (Schema::hasColumn('verifications', 'confidence_score')) {
                $table->dropColumn('confidence_score');
            }
        });
    }
}