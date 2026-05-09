<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PodcastEpisode extends Model
{
    protected $fillable = [
        'news_id',
        'audio_path',
        'audio_url',
        'duration_seconds',
        'file_size',
        'voice',
        'status',
        'error_message',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'duration_seconds' => 'integer',
        'file_size' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function getDurationFormatted(): string
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }

        $minutes = intdiv($this->duration_seconds, 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getItunesDuration(): string
    {
        if (!$this->duration_seconds) {
            return '00:00:00';
        }

        $hours = intdiv($this->duration_seconds, 3600);
        $minutes = intdiv($this->duration_seconds % 3600, 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
