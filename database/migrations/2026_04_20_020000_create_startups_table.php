<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('country', 60)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('sector', 60)->nullable(); // nlp, computer-vision, robotics, infrastructure, healthcare, etc.
            $table->string('stage', 30)->nullable();  // pre-seed, seed, series-a, series-b, public, acquired, stealth
            $table->decimal('total_funding_usd', 12, 2)->nullable(); // en millones USD
            $table->date('last_funding_date')->nullable();
            $table->json('investors')->nullable();
            $table->json('products')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('auto_generated')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};
