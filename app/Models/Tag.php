<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
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
    ];

    /**
     * Obtiene las noticias que tienen esta etiqueta.
     */
    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    /**
     * Obtiene las investigaciones que tienen esta etiqueta.
     */
    public function researches()
    {
        return $this->belongsToMany(Research::class);
    }

    /**
     * Obtiene las publicaciones de invitados que tienen esta etiqueta.
     */
    public function guestPosts()
    {
        return $this->belongsToMany(GuestPost::class);
    }

    /**
     * Alias para el método researches().
     */
    public function research()
    {
        return $this->researches();
    }
}
