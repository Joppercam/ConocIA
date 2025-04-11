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
        // create_trusted_sources_table.php
        Schema::create('trusted_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('type'); // 'government', 'academic', 'statistical', etc.
            $table->decimal('reliability_score', 4, 2); // 0-10 score
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trusted_sources');
    }
};
