<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insight extends Model
{
    protected $fillable = [
        'noticia_id',
        'resumen',
        'impacto',
        'relevancia',
        'insight_accionable',
        'tipo',
        'is_premium',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'relevancia' => 'integer',
    ];

    public function noticia(): BelongsTo
    {
        return $this->belongsTo(News::class, 'noticia_id');
    }
}
