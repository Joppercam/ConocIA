<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'type', 'start_date', 'end_date',
        'location', 'is_online', 'url', 'image', 'organizer',
        'price', 'is_free', 'featured', 'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_online'  => 'boolean',
        'is_free'    => 'boolean',
        'featured'   => 'boolean',
        'active'     => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', today());
    }

    public function scopePast($query)
    {
        return $query->where('start_date', '<', today());
    }

    public function getDaysUntilAttribute(): int
    {
        return today()->diffInDays($this->start_date, false);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date >= today();
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'conference' => 'Conferencia',
            'webinar'    => 'Webinar',
            'deadline'   => 'Deadline',
            'workshop'   => 'Workshop',
            'summit'     => 'Summit',
            default      => ucfirst($this->type),
        };
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            'conference' => '#38b6ff',
            'webinar'    => '#00c896',
            'deadline'   => '#ff4757',
            'workshop'   => '#ffa502',
            'summit'     => '#9c27b0',
            default      => '#64748b',
        ];
    }
}
