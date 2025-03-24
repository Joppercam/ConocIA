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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Scope para obtener las noticias más leídas
     */
    public function scopeMostRead($query, $limit = 5)
    {
        return $query->published()
                    ->orderBy('views', 'desc')
                    ->limit($limit);
    }

        /**
     * Scope para obtener noticias populares del último periodo
     */
    public function scopePopularRecent($query, $days = 7, $limit = 5)
    {
        return $query->published()
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('views', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope a query para obtener noticias publicadas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

        /**
     * Estadísticas diarias de vistas
     */
    public function viewStats()
    {
        return $this->hasMany('App\Models\NewsViewsStat');
    }

    // Añade este método al modelo News
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

        /**
     * Obtener tiempo de lectura en formato legible
     *
     * @return string
     */
    public function getReadingMinutesAttribute()
    {
        $minutes = $this->reading_time ?: 1;
        return $minutes . ' min' . ($minutes > 1 ? 's' : '');
    }

        /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

        /**
     * Obtener un título corto (para mostrar en listados)
     *
     * @return string
     */
    public function getShortTitleAttribute()
    {
        return \Illuminate\Support\Str::limit($this->title, 60);
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