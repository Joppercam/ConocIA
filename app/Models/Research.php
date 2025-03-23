<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
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
        'abstract',
        'image',
        'type',
        'research_type',
        'author',
        'user_id',
        'views',
        'comments_count',
        'citations',
        'featured',
        'is_published',
        'status',
        'category_id',
        'additional_authors',
        'institution',
        'references',
        'document_path',
        'published_at'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'featured' => 'boolean',
        'views' => 'integer',
        'comments_count' => 'integer',
        'citations' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Obtiene el usuario que creó la investigación.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the URL for the research.
     */
    public function getUrlAttribute()
    {
        return route('research.show', $this->slug ?? $this->id);
    }

    /**
     * Obtiene las etiquetas asociadas a la investigación.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Obtiene los comentarios de la investigación.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Ámbito para investigaciones publicadas.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Obtiene la fecha formateada para mostrar en la interfaz.
     */
    public function getFormattedDate()
    {
        return $this->published_at->format('d M, Y');
    }

    /**
     * Devuelve el array de autores.
     */
    public function getAuthorsArray()
    {
        return json_decode($this->authors, true) ?: [];
    }

    /**
     * Devuelve el array de instituciones.
     */
    public function getInstitutionsArray()
    {
        return json_decode($this->institutions, true) ?: [];
    }

    /**
     * Devuelve el array de palabras clave.
     */
    public function getKeywordsArray()
    {
        return json_decode($this->keywords, true) ?: [];
    }

    /**
     * Devuelve el array de referencias.
     */
    public function getReferencesArray()
    {
        return json_decode($this->references, true) ?: [];
    }

    // Añade este método al modelo Research
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }


    public function category()
        {
                return $this->belongsTo(Category::class, 'category_id');
        }

        /**
     * Incrementa el contador de vistas del artículo de investigación
     *
     * @return bool
     */
    public function incrementViews()
    {
        $this->views = $this->views + 1;
        return $this->save();
    }


}
