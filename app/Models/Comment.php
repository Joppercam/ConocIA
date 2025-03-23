<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'guest_name',
        'guest_email',
        'is_approved',
        'parent_id',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * Obtiene el modelo comentable (polimórfico).
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Obtiene el usuario que realizó el comentario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el comentario padre (para respuestas).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Obtiene las respuestas a este comentario.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Ámbito para comentarios aprobados.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Ámbito para comentarios principales (no respuestas).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
