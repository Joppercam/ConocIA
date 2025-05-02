<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'title',
        'audio_path',
        'duration',
        'play_count',
        'published_at',
        'voice'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'duration' => 'integer',
        'play_count' => 'integer',
    ];

    /**
     * Obtiene la noticia relacionada a este podcast
     */
    public function news()
    {
        return $this->belongsTo(News::class);
    }
    
    /**
     * Incrementa el contador de reproducciones
     */
    public function incrementPlayCount()
    {
        $this->increment('play_count');
    }
    
    /**
     * Obtiene la URL completa del archivo de audio
     */
    public function getAudioUrlAttribute()
    {
        return asset('storage/' . $this->audio_path);
    }
    
    /**
     * Obtiene la duración formateada (mm:ss)
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    
    /**
     * Scope para obtener los podcasts más reproducidos
     */
    public function scopePopular($query, $limit = 5)
    {
        return $query->orderBy('play_count', 'desc')->limit($limit);
    }
    
    /**
     * Scope para obtener los podcasts más recientes
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }
    
    /**
     * Determina si el podcast es reciente (menos de 3 días)
     */
    public function getIsRecentAttribute()
    {
        return $this->published_at->diffInDays(now()) < 3;
    }
}