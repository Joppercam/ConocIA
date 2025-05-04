<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'name',         // Nuevo campo para el nombre del suscriptor (opcional)
        'is_active',    
        'verified_at',  
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Las categorías a las que el suscriptor está suscrito.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'newsletter_category', 'newsletter_id', 'category_id')
                    ->withTimestamps();
    }
}