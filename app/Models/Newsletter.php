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
        'is_active',       // Cambio de 'active' a 'is_active'
        'verified_at',     // Cambio de 'unsubscribed_at' a 'verified_at'
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',   // Cambio de 'active' a 'is_active'
        'verified_at' => 'datetime', // Cambio de 'unsubscribed_at' a 'verified_at'
    ];
}