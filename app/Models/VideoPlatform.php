<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'embed_pattern',
        'api_key',
        'api_secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class, 'platform_id');
    }
}