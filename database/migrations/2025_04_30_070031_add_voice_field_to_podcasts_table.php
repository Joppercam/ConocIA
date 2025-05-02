<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoiceFieldToPodcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('podcasts', function (Blueprint $table) {
            // Verificar si el campo ya existe antes de añadirlo
            if (!Schema::hasColumn('podcasts', 'voice')) {
                // Agregar campo para la voz utilizada
                $table->string('voice')->default('alloy')->after('published_at');
            }
            
            // Los índices no causarán error si ya existen, pero añadimos
            // verificación para ser más claros
            
            // Índice para fechas de publicación
            if (!Schema::hasIndex('podcasts', 'podcasts_published_at_index')) {
                $table->index('published_at');
            }
            
            // Índice para conteo de reproducciones
            if (!Schema::hasIndex('podcasts', 'podcasts_play_count_index')) {
                $table->index('play_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('podcasts', function (Blueprint $table) {
            // Verificar si las columnas/índices existen antes de eliminarlos
            if (Schema::hasColumn('podcasts', 'voice')) {
                $table->dropColumn('voice');
            }
            
            if (Schema::hasIndex('podcasts', 'podcasts_published_at_index')) {
                $table->dropIndex(['published_at']);
            }
            
            if (Schema::hasIndex('podcasts', 'podcasts_play_count_index')) {
                $table->dropIndex(['play_count']);
            }
        });
    }
}