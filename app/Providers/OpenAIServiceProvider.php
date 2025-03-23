<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;
use GuzzleHttp\Client;

class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Esto solo se hace en entorno de desarrollo
        if (app()->environment('local')) {
            $this->app->resolving(\OpenAI\Client::class, function ($client) {
                $httpClient = new Client([
                    'verify' => false, // Desactivar verificación SSL solo en entorno local
                ]);
                
                return $client->withHttpClient($httpClient);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}