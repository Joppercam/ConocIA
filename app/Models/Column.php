<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Column extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'author_id',
        'category_id',
        'featured',
        'reading_time',
        'views',
        'published_at',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'featured' => 'boolean',
        'views' => 'integer',
        'reading_time' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($column) {
            // Generar slug si no existe
            if (empty($column->slug)) {
                $column->slug = Str::slug($column->title);
            }
            
            // Generar excerpt si no existe
            if (empty($column->excerpt)) {
                $column->excerpt = Str::limit(strip_tags($column->content), 150);
            }
            
            // Calcular tiempo de lectura si no existe
            if (empty($column->reading_time)) {
                $wordCount = str_word_count(strip_tags($column->content));
                $column->reading_time = max(1, ceil($wordCount / 200)); // 200 palabras por minuto
            }
            
            // Establecer fecha de publicación si no existe
            if (empty($column->published_at)) {
                $column->published_at = now();
            }
        });
    }

    /**
     * Relación con el autor
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Relación con la categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Scope para columnas publicadas
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }
    
    /**
     * Scope para columnas destacadas
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

        /**
     * Obtener los comentarios de la columna (si implementas esta funcionalidad)
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}