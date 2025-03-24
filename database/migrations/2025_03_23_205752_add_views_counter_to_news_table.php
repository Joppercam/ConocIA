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
        // Verificar si la columna views no existe en la tabla news
        if (!Schema::hasColumn('news', 'views')) {
            Schema::table('news', function (Blueprint $table) {
                $table->unsignedInteger('views')->default(0)->after('reading_time');
                $table->index('views');
            });
        }
        
        // Crear tabla para estadísticas detalladas de vistas
        Schema::create('news_views_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->date('view_date');
            $table->unsignedInteger('views')->default(1);
            $table->timestamps();
            
            // Índices
            $table->unique(['news_id', 'view_date']);
            $table->index('view_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_views_stats');
        
        // Solo eliminar la columna views si existe
        if (Schema::hasColumn('news', 'views')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }
    }
};