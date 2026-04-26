<?php

namespace App\Support;

use App\Models\EditorialAgentLog;
use App\Models\EditorialAgentTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class EditorialAgentLogger
{
    public static function info(string $event, string $message, array $context = []): void
    {
        self::write('info', $event, $message, $context);
    }

    public static function warning(string $event, string $message, array $context = []): void
    {
        self::write('warning', $event, $message, $context);
    }

    public static function error(string $event, string $message, array $context = []): void
    {
        self::write('error', $event, $message, $context);
    }

    private static function write(string $level, string $event, string $message, array $context = []): void
    {
        try {
            if (!Schema::hasTable('editorial_agent_logs')) {
                Log::channel(config('logging.default'))->log($level, "[EditorialAgent] {$event}: {$message}", $context);

                return;
            }

            $task = $context['task'] ?? null;
            $taskId = $context['task_id'] ?? ($task instanceof EditorialAgentTask ? $task->id : null);

            EditorialAgentLog::create([
                'level' => $level,
                'event' => $event,
                'message' => $message,
                'task_id' => $taskId,
                'content_id' => $context['content_id'] ?? null,
                'content_type' => $context['content_type'] ?? null,
                'context' => collect($context)->except(['task'])->all(),
                'occurred_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('EditorialAgentLogger failed: ' . $e->getMessage());
        }
    }
}
