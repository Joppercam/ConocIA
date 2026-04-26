<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_agent_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('dedupe_key')->unique();
            $table->string('task_type', 60);
            $table->string('priority', 20)->default('medium');
            $table->string('status', 30)->default('pending');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('suggested_action')->nullable();
            $table->string('content_type', 60)->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('content_url')->nullable();
            $table->json('source_urls')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['task_type', 'status']);
            $table->index(['content_type', 'content_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_agent_tasks');
    }
};
