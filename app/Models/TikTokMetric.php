<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TikTokMetric extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tiktok_script_id',
        'tiktok_video_id',
        'views',
        'likes',
        'comments',
        'shares',
        'clicks_to_portal'
    ];
    
    protected $casts = [
        'views' => 'integer',
        'likes' => 'integer',
        'comments' => 'integer',
        'shares' => 'integer',
        'clicks_to_portal' => 'integer',
    ];
    
    /**
     * Relación con el script de TikTok
     */
    public function script()
    {
        return $this->belongsTo(TikTokScript::class, 'tiktok_script_id');
    }
    
    /**
     * Obtener el engagement total
     */
    public function getEngagementAttribute()
    {
        return $this->likes + $this->comments + $this->shares;
    }
}