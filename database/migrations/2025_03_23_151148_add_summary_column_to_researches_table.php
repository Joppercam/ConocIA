<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSummaryColumnToResearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research', function (Blueprint $table) {
            // Comprobar si la columna no existe antes de añadirla
            if (!Schema::hasColumn('research', 'summary')) {
                $table->text('summary')->nullable()->after('content');
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
        Schema::table('research', function (Blueprint $table) {
            // Comprobar si la columna existe antes de eliminarla
            if (Schema::hasColumn('research', 'summary')) {
                $table->dropColumn('summary');
            }
        });
    }
}