<?php

namespace App\Traits;

use App\Models\Comment;

trait HasComments
{
    /**
     * Obtiene todos los comentarios para este modelo.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Obtiene solo los comentarios aprobados para este modelo.
     */
    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->approved();
    }

    /**
     * Obtiene comentarios pendientes para este modelo.
     */
    public function pendingComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->pending();
    }

    /**
     * Obtiene comentarios rechazados para este modelo.
     */
    public function rejectedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->rejected();
    }

    /**
     * Cuenta el número de comentarios para este modelo.
     */
    public function commentsCount()
    {
        return $this->morphMany(Comment::class, 'commentable')->count();
    }

    /**
     * Cuenta el número de comentarios aprobados para este modelo.
     */
    public function approvedCommentsCount()
    {
        return $this->morphMany(Comment::class, 'commentable')->approved()->count();
    }

    /**
     * Agrega un comentario a este modelo.
     */
    public function addComment(array $data)
    {
        return $this->comments()->create($data);
    }
}