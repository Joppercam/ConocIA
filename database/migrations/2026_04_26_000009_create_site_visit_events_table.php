<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visit_events', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 60)->nullable()->index();
            $table->unsignedBigInteger('content_id')->nullable()->index();
            $table->string('title', 500)->nullable();
            $table->string('url', 1000);
            $table->string('route_name', 120)->nullable()->index();
            $table->string('referrer', 1000)->nullable();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('is_bot')->default(false)->index();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['viewed_at', 'content_type']);
            $table->index(['content_type', 'content_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visit_events');
    }
};
