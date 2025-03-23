<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
// Elimina esta línea si existe:
// use Illuminate\Auth\Passwords\PasswordBroker;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Elimina cualquier código aquí que esté intentando usar macro() en PasswordBroker
        // Por ejemplo, si tienes algo como:
        // PasswordBroker::macro('someMethod', function() { ... });
        // Debes eliminarlo o comentarlo
    }
}