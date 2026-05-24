<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GlossaryTerm extends Model
{
    protected $fillable = [
        'term', 'slug', 'letter', 'definition',
        'explanation', 'difficulty_level', 'related_concept_url',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->term);
            }
            if (empty($model->letter)) {
                $model->letter = strtoupper(mb_substr($model->term, 0, 1));
            }
        });
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty_level) {
            'basico'      => 'Básico',
            'intermedio'  => 'Intermedio',
            'avanzado'    => 'Avanzado',
            default       => ucfirst($this->difficulty_level),
        };
    }

    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty_level) {
            'basico'     => '#10b981',
            'intermedio' => '#f59e0b',
            'avanzado'   => '#ef4444',
            default      => '#64748b',
        };
    }
}
