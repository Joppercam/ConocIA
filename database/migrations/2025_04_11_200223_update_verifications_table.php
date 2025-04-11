<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVerificationsTable extends Migration
{
    public function up()
    {
        Schema::table('verifications', function (Blueprint $table) {
            // Comprobar si la columna existe primero para evitar errores
            if (Schema::hasColumn('verifications', 'explanation')) {
                $table->text('explanation')->nullable()->change();
            } else {
                $table->text('explanation')->nullable()->after('analysis');
            }
            
            // Asegurarse de que summary y analysis también sean nullable
            if (Schema::hasColumn('verifications', 'summary')) {
                $table->text('summary')->nullable()->change();
            }
            
            if (Schema::hasColumn('verifications', 'analysis')) {
                $table->text('analysis')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('verifications', function (Blueprint $table) {
            if (Schema::hasColumn('verifications', 'explanation')) {
                $table->text('explanation')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('verifications', 'summary')) {
                $table->text('summary')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('verifications', 'analysis')) {
                $table->text('analysis')->nullable(false)->change();
            }
        });
    }
}