<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToResearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research', function (Blueprint $table) {
            // Añadir columnas faltantes
            $table->foreignId('user_id')->nullable()->after('id');
            $table->string('research_type')->nullable()->after('type');
            $table->string('status')->default('pending')->after('is_published');
            $table->string('document_path')->nullable()->after('image');
            $table->string('additional_authors')->nullable()->after('author');
            $table->string('institution')->nullable()->after('additional_authors');
            $table->text('references')->nullable()->after('content');
            $table->timestamp('published_at')->nullable()->after('is_published');
            
            // Crear índices para mejorar rendimiento en búsquedas
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('research_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('research', function (Blueprint $table) {
            // Eliminar columnas añadidas
            $table->dropColumn([
                'user_id',
                'research_type',
                'status',
                'document_path',
                'additional_authors',
                'institution',
                'references',
                'published_at'
            ]);
            
            // Eliminar índices
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['research_type']);
        });
    }
}