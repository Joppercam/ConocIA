<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('category', 60)->nullable(); // coding, research, productivity, automation, data-analysis, customer-service, creative, general
            $table->string('type', 30)->default('open-source'); // open-source, closed, api
            $table->string('framework', 60)->nullable(); // langchain, autogen, crewai, langgraph, custom, none
            $table->unsignedInteger('stars_github')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('use_cases')->nullable();
            $table->boolean('requires_api_key')->default(true);
            $table->boolean('has_free_tier')->default(false);
            $table->string('pricing_model', 30)->default('free'); // free, freemium, paid, open-source
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
        Schema::dropIfExists('ai_agents');
    }
};
