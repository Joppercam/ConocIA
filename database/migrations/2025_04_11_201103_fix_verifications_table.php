<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixVerificationsTable extends Migration
{
    public function up()
    {
        // Si la tabla verifications existe pero le faltan columnas, las añadimos
        if (Schema::hasTable('verifications')) {
            Schema::table('verifications', function (Blueprint $table) {
                if (!Schema::hasColumn('verifications', 'summary')) {
                    $table->text('summary')->nullable();
                }
                
                if (!Schema::hasColumn('verifications', 'analysis')) {
                    $table->text('analysis')->nullable();
                }
                
                if (!Schema::hasColumn('verifications', 'evidence')) {
                    $table->json('evidence')->nullable();
                }
                
                if (!Schema::hasColumn('verifications', 'views_count')) {
                    $table->integer('views_count')->default(0);
                }
            });
        } 
        // Si la tabla no existe, la creamos completa
        else {
            Schema::create('verifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('claim_id')->constrained()->onDelete('cascade');
                $table->string('verdict');
                $table->text('explanation');
                $table->text('summary')->nullable();
                $table->text('analysis')->nullable();
                $table->json('evidence')->nullable();
                $table->integer('views_count')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // No hacemos nada en down() para evitar pérdida de datos
    }
}