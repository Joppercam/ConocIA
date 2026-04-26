<?php

namespace App\Models;

use App\Http\ViewComposers\AdminLayoutComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class EditorialAgentTask extends Model
{
    protected $fillable = [
        'dedupe_key',
        'task_type',
        'priority',
        'status',
        'title',
        'summary',
        'suggested_action',
        'content_type',
        'content_id',
        'content_url',
        'source_urls',
        'payload',
        'scheduled_for',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'source_urls' => 'array',
        'payload' => 'array',
        'scheduled_for' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget(AdminLayoutComposer::CACHE_KEY));
        static::deleted(fn() => Cache::forget(AdminLayoutComposer::CACHE_KEY));
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getTaskTypeLabelAttribute(): string
    {
        return match ($this->task_type) {
            'content_push' => 'Impulsar contenido',
            'content_idea' => 'Idea editorial',
            'seo_opportunity' => 'Oportunidad SEO',
            'social_copy' => 'Copy redes',
            'research_candidate' => 'Paper candidato',
            default => ucfirst(str_replace('_', ' ', $this->task_type)),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'Alta',
            'low' => 'Baja',
            default => 'Media',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Aprobado',
            'rejected' => 'Descartado',
            'completed' => 'Ejecutado',
            default => 'Pendiente',
        };
    }
}
