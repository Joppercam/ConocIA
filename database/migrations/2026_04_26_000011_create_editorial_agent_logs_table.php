<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_agent_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20)->default('info');
            $table->string('event', 80);
            $table->string('message');
            $table->foreignId('task_id')->nullable()->constrained('editorial_agent_tasks')->nullOnDelete();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('content_type', 60)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['event', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_agent_logs');
    }
};
