<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon'
    ];

    public function claims()
    {
        return $this->belongsToMany(Claim::class);
    }
}
