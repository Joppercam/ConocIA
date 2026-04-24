<?php

namespace App\Models;

use App\Support\TikTokCache;
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
        'published_at',
        'audio_path',
        'caption',
        'onscreen_text',
        'kit_generated_at',
    ];

    protected $casts = [
        'published_at'     => 'datetime',
        'kit_generated_at' => 'datetime',
        'tiktok_score'     => 'float',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            TikTokCache::clearAll();
        });

        static::deleted(function () {
            TikTokCache::clearAll();
        });
    }

    public function hasKit(): bool
    {
        return !is_null($this->kit_generated_at);
    }
    
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
