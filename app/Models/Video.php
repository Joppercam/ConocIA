<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'external_id',
        'title',
        'description',
        'thumbnail_url',
        'embed_url',
        'original_url',
        'published_at',
        'duration_seconds',
        'view_count',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function platform()
    {
        return $this->belongsTo(VideoPlatform::class, 'platform_id');
    }

    public function categories()
    {
        return $this->belongsToMany(VideoCategory::class, 'video_category', 'video_id', 'video_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(VideoTag::class, 'video_tag', 'video_id', 'video_tag_id');
    }

    public function keywords()
    {
        return $this->hasMany(VideoKeyword::class);
    }

    public function news()
    {
        return $this->belongsToMany(News::class, 'news_video', 'video_id', 'news_id')
            ->withPivot('relevance_score');
    }

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }

    // Formatear la duración a minutos:segundos
    public function getDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return '0:00';
        }
        
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        
        return $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    // Obtener videos relacionados basados en tags y categorías
    public function getRelatedVideosAttribute($limit = 4)
    {
        $tagIds = $this->tags->pluck('id');
        $categoryIds = $this->categories->pluck('id');

        return self::where('id', '!=', $this->id)
            ->where(function ($query) use ($tagIds, $categoryIds) {
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('video_tags.id', $tagIds);
                })->orWhereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('video_categories.id', $categoryIds);
                });
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // Incrementar vistas
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}