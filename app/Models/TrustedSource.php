<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedSource extends Model
{
    protected $fillable = [
        'name', 
        'url', 
        'type',
        'description', 
        'content_selector', 
        'active_for_monitoring',
        'reliability_score'
    ];
    
    protected $casts = [
        'reliability_score' => 'float',
    ];
}
