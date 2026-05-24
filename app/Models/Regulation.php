<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Regulation extends Model
{
    protected $fillable = [
        'title', 'slug', 'scope', 'status', 'summary',
        'content', 'source_url', 'institution',
        'date_introduced', 'date_updated',
    ];

    protected $casts = [
        'date_introduced' => 'date',
        'date_updated'    => 'date',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_tramitacion' => 'En tramitación',
            'aprobada'       => 'Aprobada',
            'vigente'        => 'Vigente',
            'rechazada'      => 'Rechazada',
            'propuesta'      => 'Propuesta',
            default          => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_tramitacion' => '#f59e0b',
            'aprobada'       => '#3b82f6',
            'vigente'        => '#10b981',
            'rechazada'      => '#ef4444',
            'propuesta'      => '#8b5cf6',
            default          => '#64748b',
        };
    }

    public function getScopeLabelAttribute(): string
    {
        return match($this->scope) {
            'chile'         => 'Chile',
            'internacional' => 'Internacional',
            default         => ucfirst($this->scope),
        };
    }
}
