<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConocIaPaper extends Model
{
    protected $table = 'conocia_papers';

    protected $fillable = [
        'arxiv_id', 'arxiv_url', 'original_title', 'original_abstract',
        'authors', 'arxiv_published_date', 'arxiv_category',
        'title', 'slug', 'excerpt', 'content',
        'key_contributions', 'practical_implications', 'difficulty_level',
        'image', 'featured', 'status', 'views', 'reading_time', 'published_at',
    ];

    protected $casts = [
        'authors'                => 'array',
        'key_contributions'      => 'array',
        'practical_implications' => 'array',
        'featured'               => 'boolean',
        'views'                  => 'integer',
        'reading_time'           => 'integer',
        'arxiv_published_date'   => 'date',
        'published_at'           => 'datetime',
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

    public function scopeByArxivCategory($query, string $cat)
    {
        return $query->where('arxiv_category', 'like', $cat . '%');
    }

    public function authorsFormatted(): string
    {
        $authors = $this->authors ?? [];
        if (count($authors) <= 3) {
            return implode(', ', $authors);
        }
        return implode(', ', array_slice($authors, 0, 3)) . ' et al.';
    }
}
