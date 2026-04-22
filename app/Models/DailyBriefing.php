<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBriefing extends Model
{
    protected $fillable = [
        'date',
        'script',
        'headlines',
        'duration_seconds',
        'news_count',
        'generated_at',
    ];

    protected $casts = [
        'date'         => 'date',
        'headlines'    => 'array',
        'generated_at' => 'datetime',
    ];

    public static function today(): ?self
    {
        return static::where('date', today()->toDateString())->first();
    }

    public static function latest(): ?self
    {
        return static::orderByDesc('date')->first();
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
    }

    public function getEstimatedMinutesAttribute(): string
    {
        $mins = (int) ceil($this->duration_seconds / 60);
        return $mins . ' min';
    }
}
