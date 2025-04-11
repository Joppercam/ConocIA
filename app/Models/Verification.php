<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'claim_id', 'verdict', 'explanation', 'summary', 'analysis', 
        'evidence', 'evidence_sources', 'views_count', 'confidence_score'
    ];
    
    protected $casts = [
        'evidence' => 'array',
        'evidence_sources' => 'array', // Si es un campo JSON/array
        'views_count' => 'integer',
        'confidence_score' => 'float'
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}