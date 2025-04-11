<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trusted_sources', function (Blueprint $table) {
            $table->boolean('active_for_monitoring')->default(true)->after('reliability_score');
        });
    }
    
    public function down(): void
    {
        Schema::table('trusted_sources', function (Blueprint $table) {
            $table->dropColumn('active_for_monitoring');
        });
    }
};
