<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NewsHistoric extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 
        'slug', 
        'excerpt', 
        'content', 
        'summary',
        'image', 
        'image_caption',
        'category_id', 
        'author_id',
        'views', 
        'status',
        'tags', 
        'featured',
        'source',
        'source_url',
        'published_at',
        'reading_time',
        'original_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'views' => 'integer',
        'reading_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relaciones
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable', 'commentable_type', 'commentable_id', 'original_id');
    }

    // Métodos para acceder a relaciones originales
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag', 'news_id', 'tag_id')
                    ->where('news_id', $this->original_id);
    }

    public function socialPosts()
    {
        return $this->hasMany(SocialMediaPost::class, 'news_id', 'original_id');
    }
}