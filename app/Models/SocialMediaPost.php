<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'network',
        'post_id',
        'post_url',
        'published_at',
        'status',
        'error_message'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Obtiene la noticia asociada a esta publicación
     */
    public function news()
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Alcance de consulta para filtrar por red social
     */
    public function scopeByNetwork($query, $network)
    {
        return $query->where('network', $network);
    }

    /**
     * Alcance de consulta para obtener solo publicaciones exitosas
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Alcance de consulta para obtener publicaciones fallidas
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}