<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestPost extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category_id',
        'user_id',
        'status',
        'published_at',
        'author_bio',
        'author_website',
        'author_twitter',
        'author_linkedin',
        'allow_comments',
        'views',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'views' => 'integer',
    ];

    /**
     * Obtiene el usuario que gestiona la publicación.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene la categoría a la que pertenece la publicación.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Obtiene las etiquetas asociadas a la publicación.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Obtiene los comentarios de la publicación.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Ámbito para publicaciones publicadas.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Ámbito para publicaciones pendientes.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Obtiene la fecha formateada para mostrar en la interfaz.
     */
    public function getFormattedDate()
    {
        return $this->published_at->format('d M, Y');
    }

    /**
     * Calcula el tiempo de lectura aproximado del artículo.
     */
    public function getReadingTime()
    {
        $words = str_word_count(strip_tags($this->content));
        $minutes = ceil($words / 200);
        return $minutes . ' min lectura';
    }
}
