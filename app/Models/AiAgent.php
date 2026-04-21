<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgent extends Model
{
    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'logo', 'website_url', 'github_url',
        'category', 'type', 'framework', 'stars_github',
        'capabilities', 'use_cases', 'requires_api_key', 'has_free_tier',
        'pricing_model', 'source_url', 'featured', 'active', 'auto_generated', 'last_synced_at',
    ];

    protected $casts = [
        'capabilities'    => 'array',
        'use_cases'       => 'array',
        'requires_api_key'=> 'boolean',
        'has_free_tier'   => 'boolean',
        'featured'        => 'boolean',
        'active'          => 'boolean',
        'auto_generated'  => 'boolean',
        'last_synced_at'  => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? ucfirst($this->category ?? '');
    }

    public function getPricingLabelAttribute(): string
    {
        return match($this->pricing_model) {
            'free'        => 'Gratuito',
            'freemium'    => 'Freemium',
            'paid'        => 'De pago',
            'open-source' => 'Open Source',
            default       => ucfirst($this->pricing_model ?? ''),
        };
    }

    public function getPricingColorAttribute(): string
    {
        return match($this->pricing_model) {
            'free'        => '#00c896',
            'freemium'    => '#38b6ff',
            'paid'        => '#ffa502',
            'open-source' => '#9c27b0',
            default       => '#64748b',
        };
    }

    public function getFormattedStarsAttribute(): string
    {
        if (!$this->stars_github) return '—';
        return $this->stars_github >= 1000
            ? number_format($this->stars_github / 1000, 1) . 'k'
            : (string) $this->stars_github;
    }

    public static function categoryLabels(): array
    {
        return [
            'coding'           => 'Coding',
            'research'         => 'Investigación',
            'productivity'     => 'Productividad',
            'automation'       => 'Automatización',
            'data-analysis'    => 'Análisis de datos',
            'customer-service' => 'Atención al cliente',
            'creative'         => 'Creatividad',
            'general'          => 'General',
        ];
    }
}
