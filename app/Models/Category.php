<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Obtiene las noticias que pertenecen a esta categoría.
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Obtiene las publicaciones de invitados que pertenecen a esta categoría.
     */
    public function guestPosts()
    {
        return $this->hasMany(GuestPost::class);
    }

    /**
     * Get all of the research for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function research()
    {
        return $this->hasMany(Research::class, 'category_id');
    }


    /**
     * Obtener las columnas pertenecientes a esta categoría
     */
    public function columns()
    {
        return $this->hasMany(Column::class);
    }

     /**
     * Los suscriptores que están interesados en esta categoría.
     */
    public function newsletters()
    {
        return $this->belongsToMany(Newsletter::class, 'newsletter_category', 'category_id', 'newsletter_id')
                    ->withTimestamps();
    }
}
