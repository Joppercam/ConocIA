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
        Schema::table('podcasts', function (Blueprint $table) {
            // Cambiar news_id para que permita valores NULL
            $table->unsignedBigInteger('news_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            // Revertir a la restricción NOT NULL
            $table->unsignedBigInteger('news_id')->nullable(false)->change();
        });
    }
};