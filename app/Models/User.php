<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\AdminPasswordResetNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_photo',
        'bio',
        'website',
        'twitter',
        'linkedin',
        'github',
        'role_id',
        'is_active',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Obtiene el rol del usuario.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Obtiene las noticias creadas por el usuario.
     */
    public function news()
    {
        return $this->hasMany(News::class);
    }

    /**
     * Obtiene las investigaciones creadas por el usuario.
     */
    public function researches()
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Obtiene las publicaciones de invitados gestionadas por el usuario.
     */
    public function guestPosts()
    {
        return $this->hasMany(GuestPost::class);
    }

    /**
     * Obtiene los comentarios realizados por el usuario.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Verifica si el usuario es editor o superior.
     *
     * @return bool
     */
    public function isEditor()
    {
        return $this->role && ($this->role->slug === 'editor' || $this->isAdmin());
    }

    /**
     * Verifica si el usuario es autor o superior.
     *
     * @return bool
     */
    public function isAuthor()
    {
        return $this->role && ($this->role->slug === 'author' || $this->isEditor());
    }

    /**
     * Relación con las columnas que ha escrito este usuario
     */
    public function columns()
    {
        return $this->hasMany(Column::class, 'author_id');
    }

        /**
     * Enviar notificación de restablecimiento de contraseña
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminPasswordResetNotification($token));
    }


    public function isAdmin()
    {
        return $this->role_id == 1;
    }
}
