<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Startup extends Model
{
    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'logo', 'website_url',
        'founded_year', 'country', 'city', 'sector', 'stage',
        'total_funding_usd', 'last_funding_date', 'investors', 'products',
        'profile_content', 'key_quote', 'why_it_matters', 'founder_names',
        'featured_week', 'source_url', 'featured', 'active', 'auto_generated', 'last_synced_at',
    ];

    protected $casts = [
        'investors'         => 'array',
        'products'          => 'array',
        'founder_names'     => 'array',
        'featured'          => 'boolean',
        'active'            => 'boolean',
        'auto_generated'    => 'boolean',
        'last_funding_date' => 'date',
        'featured_week'     => 'date',
        'last_synced_at'    => 'datetime',
        'total_funding_usd' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getStageLabelAttribute(): string
    {
        return match($this->stage) {
            'pre-seed'  => 'Pre-seed',
            'seed'      => 'Seed',
            'series-a'  => 'Serie A',
            'series-b'  => 'Serie B',
            'series-c'  => 'Serie C+',
            'public'    => 'Pública',
            'acquired'  => 'Adquirida',
            'stealth'   => 'Stealth',
            default     => ucfirst($this->stage ?? ''),
        };
    }

    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'pre-seed'  => '#94a3b8',
            'seed'      => '#38b6ff',
            'series-a'  => '#00c896',
            'series-b'  => '#ffa502',
            'series-c'  => '#ff6b35',
            'public'    => '#9c27b0',
            'acquired'  => '#64748b',
            'stealth'   => '#1e293b',
            default     => '#64748b',
        };
    }

    public function getFundingLabelAttribute(): string
    {
        if (!$this->total_funding_usd) return 'No divulgado';
        $m = (float) $this->total_funding_usd;
        if ($m >= 1000) return '$' . number_format($m / 1000, 1) . 'B';
        return '$' . number_format($m, 0) . 'M';
    }

    public static function sectorLabels(): array
    {
        return [
            'nlp'              => 'NLP / Lenguaje',
            'computer-vision'  => 'Computer Vision',
            'robotics'         => 'Robótica',
            'infrastructure'   => 'Infraestructura IA',
            'healthcare'       => 'Salud',
            'education'        => 'Educación',
            'finance'          => 'Finanzas',
            'productivity'     => 'Productividad',
            'developer-tools'  => 'Dev Tools',
            'security'         => 'Seguridad',
            'autonomous'       => 'Vehículos Autónomos',
            'multimodal'       => 'Multimodal',
            'other'            => 'Otros',
        ];
    }
}
