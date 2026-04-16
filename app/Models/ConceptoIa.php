<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConceptoIa extends Model
{
    protected $table = 'conceptos_ia';

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'definition',
        'category', 'related_concepts', 'key_players', 'further_reading',
        'image', 'featured', 'status', 'views', 'reading_time', 'published_at',
    ];

    protected $casts = [
        'related_concepts' => 'array',
        'key_players'      => 'array',
        'further_reading'  => 'array',
        'featured'         => 'boolean',
        'views'            => 'integer',
        'reading_time'     => 'integer',
        'published_at'     => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
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

    public function getRelatedConceptModels(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        $slugs = $this->related_concepts ?? [];
        if (empty($slugs)) {
            return static::published()
                ->where('id', '!=', $this->id)
                ->where('category', $this->category)
                ->take($limit)
                ->get();
        }
        return static::whereIn('slug', $slugs)->published()->take($limit)->get();
    }
}
