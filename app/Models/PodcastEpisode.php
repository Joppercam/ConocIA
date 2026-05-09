<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }
        $minutes = (int) floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    public function getItunesDuration(): string
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }
        $hours   = (int) floor($this->duration_seconds / 3600);
        $minutes = (int) floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
