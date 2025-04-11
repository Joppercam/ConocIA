<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TikTokScript extends Model
{
    use HasFactory;
    
    // Especificar el nombre de la tabla explícitamente
    protected $table = 'tiktok_scripts';
    
    protected $fillable = [
        'news_id',
        'script_content',
        'visual_suggestions',
        'hashtags',
        'status',
        'tiktok_score',
        'ai_response_raw',
        'published_at'
    ];
    
    protected $casts = [
        'published_at' => 'datetime',
        'tiktok_score' => 'float',
    ];
    
    /**
     * Relación con el modelo News
     */
    public function news()
    {
        return $this->belongsTo(News::class);
    }
    
    /**
     * Relación con las métricas de TikTok
     */
    public function metrics()
    {
        return $this->hasOne(TikTokMetric::class);
    }
}