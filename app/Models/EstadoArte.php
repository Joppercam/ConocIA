<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoArte extends Model
{
    protected $table = 'estado_arte';

    protected $fillable = [
        'title', 'slug', 'subfield', 'subfield_label', 'period_label',
        'week_start', 'week_end', 'excerpt', 'content',
        'source_news_ids', 'key_developments', 'image',
        'featured', 'status', 'views', 'reading_time', 'published_at',
    ];

    protected $casts = [
        'source_news_ids'  => 'array',
        'key_developments' => 'array',
        'week_start'       => 'date',
        'week_end'         => 'date',
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

    public function scopeBySubfield($query, string $subfield)
    {
        return $query->where('subfield', $subfield);
    }

    public function sourceNews(): \Illuminate\Support\Collection
    {
        $ids = $this->source_news_ids ?? [];
        if (empty($ids)) return collect();
        return News::whereIn('id', $ids)->get();
    }
}
