<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EcosystemActor extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'description', 'url',
        'location', 'region', 'focus_areas', 'logo',
    ];

    protected $casts = [
        'focus_areas' => 'array',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'universidad'         => 'Universidad',
            'centro_investigacion' => 'Centro de Investigación',
            'startup'             => 'Startup',
            'gobierno'            => 'Gobierno',
            'organizacion'        => 'Organización',
            default               => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'universidad'         => '#38b6ff',
            'centro_investigacion' => '#a78bfa',
            'startup'             => '#00c896',
            'gobierno'            => '#f59e0b',
            'organizacion'        => '#f472b6',
            default               => '#64748b',
        };
    }
}
