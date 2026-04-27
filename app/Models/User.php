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
        'google_id',
        'avatar',
        'username',
        'profile_photo',
        'bio',
        'website',
        'twitter',
        'linkedin',
        'github',
        'role_id',
        'is_active',
        'plan_actual',
        'is_trial',
        'trial_ends_at',
        'academic_title',
        'institution',
        'expertise_area',
        'linkedin_url',
        'is_featured_columnist',
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
        'email_verified_at'      => 'datetime',
        'password'               => 'hashed',
        'is_active'              => 'boolean',
        'is_trial'               => 'boolean',
        'trial_ends_at'          => 'datetime',
        'is_featured_columnist'  => 'boolean',
    ];

    public function scopeFeaturedColumnists($query)
    {
        return $query->where('is_featured_columnist', true)->where('is_active', true);
    }

    public function getFullTitleAttribute(): string
    {
        return trim(($this->academic_title ? $this->academic_title . ' ' : '') . $this->name);
    }

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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'trial'])
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            ->latestOfMany();
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function plan(): string
    {
        return $this->plan_actual ?: 'free';
    }

    public function isPro(): bool
    {
        return in_array($this->plan(), ['pro', 'business'], true);
    }

    public function isBusiness(): bool
    {
        return $this->plan() === 'business';
    }

    public function hasActiveTrial(): bool
    {
        return $this->is_trial && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function canAccessFeature(string $feature): bool
    {
        if ($this->isAdmin() || $this->isBusiness() || $this->hasActiveTrial()) {
            return true;
        }

        return match ($feature) {
            'insights', 'premium-content', 'alerts' => $this->isPro(),
            'business-insights', 'reports', 'trend-intelligence', 'priority-ai' => $this->isBusiness(),
            default => false,
        };
    }

    public function planLabel(): string
    {
        return match ($this->plan()) {
            'business' => 'BUSINESS',
            'pro' => 'PRO',
            default => 'FREE',
        };
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
        if ($this->relationLoaded('role') && $this->role) {
            return $this->role->slug === 'admin';
        }

        if ($this->role()->exists()) {
            return $this->role()->value('slug') === 'admin';
        }

        // Compatibilidad con datos legacy donde el administrador estaba fijo en role_id=1.
        return (int) $this->role_id === 1;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            if (str_starts_with($this->profile_photo, 'http')) {
                return $this->profile_photo;
            }
            $disk = config('filesystems.disks.r2.key') ? 'r2' : 'public';
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->profile_photo);
        }
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0a66c2&color=fff&size=128';
    }
}
