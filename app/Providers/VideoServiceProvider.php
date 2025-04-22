<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vimeo\Vimeo;

class VimeoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('vimeo', function ($app) {
            return new Vimeo(
                config('services.vimeo.client_id'),
                config('services.vimeo.client_secret'),
                config('services.vimeo.access_token')
            );
        });
    }

    public function provides()
    {
        return ['vimeo'];
    }
}