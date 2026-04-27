<?php

namespace App\Support;

use App\Models\MetricsEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MetricsTracker
{
    public static function track(string $eventType, array $metadata = []): void
    {
        try {
            if (!Schema::hasTable('metrics_events')) {
                return;
            }

            MetricsEvent::create([
                'user_id' => Auth::id(),
                'event_type' => $eventType,
                'metadata' => $metadata,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Metrics must never break user-facing flows.
        }
    }
}
