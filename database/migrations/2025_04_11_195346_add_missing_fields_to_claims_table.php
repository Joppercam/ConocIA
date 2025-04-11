<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            // Añadir el campo context
            $table->text('context')->nullable()->after('statement');
            
            // Añadir el campo processed (para trackear si ya fue procesado)
            $table->boolean('processed')->default(false)->after('is_verified');
            
            // Nota: No añadimos claim_category_id porque ya tienes una relación many-to-many
            // Si quisieras una relación directa, se haría así:
            // $table->foreignId('claim_category_id')->nullable()->constrained()->after('source_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('context');
            $table->dropColumn('processed');
            // $table->dropColumn('claim_category_id');
        });
    }
}