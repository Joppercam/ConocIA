<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditorialAgentLog extends Model
{
    protected $fillable = [
        'level',
        'event',
        'message',
        'task_id',
        'content_id',
        'content_type',
        'context',
        'occurred_at',
    ];

    protected $casts = [
        'context' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(EditorialAgentTask::class, 'task_id');
    }
}
