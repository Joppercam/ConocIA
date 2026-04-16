<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisFondo extends Model
{
    protected $table = 'analisis_fondo';

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'topic', 'category',
        'key_players', 'image', 'featured', 'status', 'views',
        'reading_time', 'author_id', 'published_at',
    ];

    protected $casts = [
        'key_players'  => 'array',
        'featured'     => 'boolean',
        'views'        => 'integer',
        'reading_time' => 'integer',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
