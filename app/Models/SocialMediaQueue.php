<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaQueue extends Model
{
    use HasFactory;

    protected $table = 'social_media_queue';

    protected $fillable = [
        'network',
        'content',
        'media_paths',
        'news_id',
        'status',
        'manual_url',
        'post_id',
        'post_url',
        'error_message',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'media_paths' => 'array'
    ];

    /**
     * Obtiene la noticia asociada a esta publicación
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Marcar como publicado
     */
    public function markAsPublished($postId, $postUrl): void
    {
        $this->status = 'published';
        $this->post_id = $postId;
        $this->post_url = $postUrl;
        $this->published_at = now();
        $this->save();
    }

    /**
     * Marcar como fallido
     */
    public function markAsFailed($errorMessage): void
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->save();
    }
}