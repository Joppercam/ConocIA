<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'statement', 
        'context',       // Nuevo campo
        'source_url', 
        'source_name', 
        'source_type', 
        'statement_date', 
        'is_verified',
        'processed'      // Nuevo campo
    ];

    protected $casts = [
        'statement_date' => 'datetime',
        'is_verified' => 'boolean',
        'processed' => 'boolean',  // Añadir cast para el nuevo campo
    ];

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    public function categories()
    {
        return $this->belongsToMany(ClaimCategory::class);
    }
}