<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class News extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 
        'slug', 
        'excerpt', 
        'content', 
        'image', 
        'category_id', 
        'author', 
        'views', 
        'tags', 
        'featured',
        'source',
        'source_url',
        'published_at',
        'reading_time',
    ];

      /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'views' => 'integer',
        'reading_time' => 'integer',
    ];
    
    // Mutador para las fechas
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value);
    }
    
    // Incrementa las vistas cuando se accede al artículo
    public function incrementViews()
    {
        $this->views++;
        return $this->save();
    }

    /**
     * Scope a query para obtener noticias publicadas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    // Añade este método al modelo News
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

        /**
     * Obtiene noticias relacionadas por categoría.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedNews($limit = 4)
    {
        return self::where('id', '!=', $this->id)
                ->where('category_id', $this->category_id)
                ->latest()
                ->take($limit)
                ->get();
    }


       /**
     * Scope a query para filtrar por categoría.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $categorySlug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    
    /**
     * Scope a query para ordenar por más leídas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }


        /**
     * Obtiene el autor de la noticia.
     */
    public function author()
    {
        // Si el autor es un usuario:
        return $this->belongsTo(User::class, 'author_id');
        
        // O si tienes un modelo Author separado:
        // return $this->belongsTo(Author::class);
    }


        /**
     * Obtener las etiquetas de la noticia.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

}