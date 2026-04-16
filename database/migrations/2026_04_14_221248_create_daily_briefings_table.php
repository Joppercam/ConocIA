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
        Schema::create('daily_briefings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->text('script');
            $table->json('headlines')->nullable();
            $table->integer('duration_seconds')->default(120);
            $table->integer('news_count')->default(5);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_briefings');
    }
};
