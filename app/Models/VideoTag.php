<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'video_tag', 'video_tag_id', 'video_id');
    }
}