<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['news', 'researches', 'columns', 'conceptos_ia'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'difficulty_level')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->enum('difficulty_level', ['basico', 'intermedio', 'avanzado'])
                      ->default('intermedio')
                      ->after('content');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['news', 'researches', 'columns', 'conceptos_ia'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'difficulty_level')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('difficulty_level');
                });
            }
        }
    }
};
