<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadarRegulatorio extends Model
{
    use HasFactory;

    protected $table = 'radar_regulatorio';

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'tipo', 'estado', 'organismo', 'fecha_evento',
        'relevancia', 'fuente_url', 'key_actors',
        'reading_time', 'status', 'published_at',
    ];

    protected $casts = [
        'fecha_evento'  => 'date',
        'key_actors'    => 'array',
        'published_at'  => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'proyecto_ley' => 'Proyecto de Ley',
            'decreto'      => 'Decreto',
            'politica'     => 'Política Pública',
            'anuncio'      => 'Anuncio',
            'informe'      => 'Informe',
            'consulta'     => 'Consulta Pública',
            default        => ucfirst($this->tipo),
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'en_tramite'   => 'En trámite',
            'aprobado'     => 'Aprobado',
            'rechazado'    => 'Rechazado',
            'promulgado'   => 'Promulgado',
            'vigente'      => 'Vigente',
            'en_consulta'  => 'En consulta',
            'archivado'    => 'Archivado',
            default        => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'promulgado', 'vigente', 'aprobado' => '#16a34a',
            'en_tramite', 'en_consulta'          => '#d97706',
            'rechazado', 'archivado'             => '#dc2626',
            default                              => '#6b7280',
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'proyecto_ley' => '#7c3aed',
            'decreto'      => '#1d4ed8',
            'politica'     => '#0369a1',
            'informe'      => '#374151',
            'consulta'     => '#b45309',
            default        => '#38b6ff',
        };
    }
}
