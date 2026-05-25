<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regulations', function (Blueprint $table) {
            $table->text('impact_laboral')->nullable()->after('content');
            $table->text('impact_economico')->nullable()->after('impact_laboral');
            $table->text('impact_social')->nullable()->after('impact_economico');
        });
    }

    public function down(): void
    {
        Schema::table('regulations', function (Blueprint $table) {
            $table->dropColumn(['impact_laboral', 'impact_economico', 'impact_social']);
        });
    }
};
