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
     * @var array
     */
    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'status',
        'parent_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     * Este método se ejecuta cuando el modelo se inicia.
     */
    protected static function booted()
    {
        // Cuando se crea un nuevo comentario
        static::created(function ($comment) {
            // Si el comentario está pendiente, notificar a los administradores
            if ($comment->status === 'pending') {
                // Encontrar a los administradores
                $admins = User::where('role', 'admin')->get();
                
                foreach ($admins as $admin) {
                    // Crear una notificación para cada administrador
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'comment',
                        'data' => [
                            'comment_id' => $comment->id,
                            'content' => \Str::limit($comment->content, 100),
                            'commentable_type' => $comment->commentable_type,
                            'commentable_id' => $comment->commentable_id,
                            // Añadir datos sobre el artículo comentado
                            'article_title' => $comment->commentable ? $comment->commentable->title : 'Artículo no disponible',
                            'article_slug' => $comment->commentable ? $comment->commentable->slug : '',
                            // Añadir datos sobre el autor del comentario
                            'author_name' => $comment->user_id ? $comment->user->name : $comment->guest_name,
                            'is_guest' => $comment->user_id ? false : true,
                        ]
                    ]);
                }
            }
        });
        
        // Cuando se actualiza un comentario (cambia de estado, por ejemplo)
        static::updated(function ($comment) {
            // Solo realizar acciones si el estado cambió
            if ($comment->wasChanged('status')) {
                // Si pasa a aprobado, podríamos notificar al autor (ejemplo)
                if ($comment->status === 'approved' && $comment->user_id) {
                    // Notificar al autor que su comentario fue aprobado
                    // Aquí podrías implementar otro tipo de notificación, por email, etc.
                }
            }
        });
    }

    /**
     * Obtener el modelo comentable (polimórfico).
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Obtener el usuario que creó el comentario (si está autenticado).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el comentario padre (si es una respuesta).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Obtener las respuestas a este comentario.
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scope para comentarios aprobados.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope para comentarios pendientes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para comentarios rechazados.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Verificar si un comentario está aprobado.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si un comentario está pendiente.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar si un comentario está rechazado.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}