<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, añadimos la columna status como ENUM
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->after('guest_email')->nullable();
        });

        // Luego, migramos los datos de is_approved a status
        DB::statement("UPDATE comments SET status = CASE WHEN is_approved = 1 THEN 'approved' ELSE 'pending' END");

        // Después de migrar los datos, elimina la columna is_approved
        Schema::table('comments', function (Blueprint $table) {
            // Hacemos que status sea NOT NULL y establecemos un valor predeterminado
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->nullable(false)->change();
            
            // Eliminamos la columna antigua is_approved ya que ha sido migrada
            $table->dropColumn('is_approved');
            
            // Añadimos índices para mejorar el rendimiento de consultas frecuentes
            $table->index('status');
            $table->index(['commentable_type', 'commentable_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si necesitamos revertir, recreamos la columna is_approved
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('guest_email');
            
            // Migramos de vuelta los datos de status a is_approved
            DB::statement("UPDATE comments SET is_approved = CASE WHEN status = 'approved' THEN 1 ELSE 0 END");
            
            // Eliminamos la columna status y los índices añadidos
            $table->dropIndex(['status']);
            $table->dropIndex(['commentable_type', 'commentable_id', 'status']);
            $table->dropColumn('status');
        });
    }
};