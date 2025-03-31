<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageHelper;

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
        'summary',
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
        'is_published' => 'boolean',
    ];

    /**
     * Atributos calculados que se añaden automáticamente a las representaciones de array y JSON.
     *
     * @var array
     */
    protected $appends = [
        'url',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el usuario que creó la investigación.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Obtiene la categoría asociada a la investigación.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relación con el autor (basada en user_id).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para investigaciones publicadas.
     */
    public function scopePublished($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'published')
              ->orWhere('status', 'active')
              ->orWhere('is_published', true);
        });
    }

    /**
     * Scope para investigaciones destacadas.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope para investigaciones populares (más vistas).
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    /**
     * Scope para investigaciones más citadas.
     */
    public function scopeCited($query)
    {
        return $query->orderBy('citations', 'desc');
    }

    /**
     * Scope para investigaciones con imágenes válidas.
     */
    public function scopeHasValidImage($query)
    {
        return $query->whereNotNull('image')
            ->where('image', '!=', '')
            ->where('image', '!=', 'null')
            ->whereRaw("image NOT LIKE '%default%'")
            ->whereRaw("image NOT LIKE '%placeholder%'");
    }

    /**
     * Scope para investigaciones más comentadas.
     */
    public function scopeMostCommented($query)
    {
        return $query->orderBy('comments_count', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene la URL para la investigación.
     */
    public function getUrlAttribute()
    {
        return route('research.show', $this->slug ?? $this->id);
    }

    /**
     * Accessor para obtener el resumen.
     */
    public function getSummaryAttribute($value)
    {
        // Si el valor de summary ya existe, úsalo
        if (!empty($value)) {
            return $value;
        }
        
        // Si no, intenta usar excerpt
        if (isset($this->attributes['excerpt']) && !empty($this->attributes['excerpt'])) {
            return $this->attributes['excerpt'];
        }
        
        // Si no, intenta usar abstract
        if (isset($this->attributes['abstract']) && !empty($this->attributes['abstract'])) {
            return $this->attributes['abstract'];
        }
        
        // Si ninguno existe, devuelve un string vacío
        return '';
    }

    /**
     * Mutator para establecer el resumen.
     */
    public function setSummaryAttribute($value)
    {
        $this->attributes['summary'] = $value;
        
        // También actualiza excerpt para mantener consistencia
        $this->attributes['excerpt'] = $value;
    }

    /**
     * Accessor para obtener el nombre del autor.
     */
    public function getAuthorAttribute()
    {
        // Si ya existe una relación author definida, usa esa
        if ($this->relationLoaded('author') && $this->relations['author'] !== null) {
            return $this->relations['author']->name;
        }

        // Si tienes un campo author_name, úsalo
        if (isset($this->attributes['author_name'])) {
            return $this->attributes['author_name'];
        }

        // Si solo tienes author_id pero no la relación cargada, devuelve valor por defecto
        return 'Autor';
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS
    |--------------------------------------------------------------------------
    */

    /**
     * Incrementa el contador de vistas del artículo de investigación.
     *
     * @return bool
     */
    public function incrementViews()
    {
        $this->views = $this->views + 1;
        return $this->save();
    }

    /**
     * Obtiene la fecha formateada para mostrar en la interfaz.
     *
     * @param string $format Formato de fecha (opcional)
     * @return string
     */
    public function getFormattedDate($format = 'd M, Y')
    {
        return $this->published_at ? $this->published_at->format($format) : '';
    }

    /**
     * Devuelve el array de autores.
     *
     * @return array
     */
    public function getAuthorsArray()
    {
        return json_decode($this->additional_authors ?? $this->authors, true) ?: [];
    }

    /**
     * Devuelve el array de instituciones.
     *
     * @return array
     */
    public function getInstitutionsArray()
    {
        return json_decode($this->institutions, true) ?: [];
    }

    /**
     * Devuelve el array de palabras clave.
     *
     * @return array
     */
    public function getKeywordsArray()
    {
        return json_decode($this->keywords, true) ?: [];
    }

    /**
     * Devuelve el array de referencias.
     *
     * @return array
     */
    public function getReferencesArray()
    {
        return json_decode($this->references, true) ?: [];
    }

    /**
     * Obtiene una versión truncada del extracto para mostrar en tarjetas.
     *
     * @param int $length Longitud máxima
     * @return string
     */
    public function getShortExcerpt($length = 120)
    {
        $text = $this->excerpt ?? $this->abstract ?? $this->summary ?? '';
        
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }

    /**
     * Obtiene la URL de la imagen principal.
     *
     * @param string $size Tamaño de la imagen (small, medium, large)
     * @return string
     */
    public function getImageUrl($size = 'medium')
    {
        // Utiliza el helper de imágenes si está disponible
        if (class_exists(ImageHelper::class)) {
            return ImageHelper::getImageUrl($this->image, 'research', $size);
        }
        
        // Fallback a la función global si el helper no está disponible
        if (function_exists('getImageUrl')) {
            return getImageUrl($this->image, 'research', $size);
        }
        
        // Último recurso: devolver la imagen tal cual (asumiendo ruta completa)
        return $this->image ?? '';
    }

    /**
     * Genera HTML de imagen optimizado para SEO.
     *
     * @param string $alt Texto alternativo para SEO
     * @param string $size Tamaño de la imagen (small, medium, large)
     * @param array $attributes Atributos HTML adicionales
     * @return string HTML de la imagen
     */
    public function getImageHtml($alt = '', $size = 'medium', $attributes = [])
    {
        // Utiliza el texto alternativo proporcionado o el título del artículo
        $altText = $alt ?: $this->title;
        
        // Utiliza el helper de imágenes si está disponible
        if (class_exists(ImageHelper::class)) {
            return ImageHelper::getSeoImage($this->image, $altText, 'research', $size, $attributes);
        }
        
        // Fallback: generar HTML manualmente
        $url = $this->getImageUrl($size);
        $safeAlt = htmlspecialchars($altText, ENT_QUOTES, 'UTF-8');
        
        // Preparar atributos
        $attributesStr = '';
        foreach ($attributes as $key => $value) {
            $attributesStr .= " {$key}=\"{$value}\"";
        }
        
        return "<img src=\"{$url}\" alt=\"{$safeAlt}\" loading=\"lazy\"{$attributesStr}>";
    }

    /**
     * Obtener etiquetas relacionadas como string separado por comas.
     *
     * @return string
     */
    public function getTagsString()
    {
        if ($this->relationLoaded('tags')) {
            return $this->tags->pluck('name')->implode(', ');
        }
        
        return '';
    }
}