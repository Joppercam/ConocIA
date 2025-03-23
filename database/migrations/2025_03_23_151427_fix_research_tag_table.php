<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixResearchTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero comprobamos si la tabla existe
        if (Schema::hasTable('research_tag')) {
            // Eliminar la tabla para recrearla correctamente
            Schema::dropIfExists('research_tag');
        }

        // Crear la tabla con las claves foráneas correctas
        Schema::create('research_tag', function (Blueprint $table) {
            $table->id();
            
            // Importante: asegúrate de que estos nombres coincidan con las columnas reales en tus tablas
            // Usualmente, el Laravel por defecto usa el nombre en singular_id
            $table->unsignedBigInteger('research_id');
            $table->unsignedBigInteger('tag_id');
            
            // Crear índices para un mejor rendimiento
            $table->index(['research_id', 'tag_id']);
            
            // Definir claves foráneas - asegúrate de que los nombres de tabla sean correctos
            // Si tu tabla de investigaciones se llama 'research' y no 'researches', ajusta esto
            $table->foreign('research_id')
                  ->references('id')
                  ->on('research') // Cambia a 'researches' si ese es el nombre real de la tabla
                  ->onDelete('cascade');
                  
            $table->foreign('tag_id')
                  ->references('id')
                  ->on('tags')
                  ->onDelete('cascade');
                  
            // Evitar duplicados
            $table->unique(['research_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('research_tag');
    }
}