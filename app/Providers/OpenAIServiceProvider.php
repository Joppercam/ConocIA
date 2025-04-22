<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;
use App\Services\KeywordExtractorService;
use GuzzleHttp\Client as GuzzleClient;

class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar el cliente de OpenAI usando la forma actual de la API
        $this->app->singleton(OpenAI\Client::class, function ($app) {
            return OpenAI::client(
                config('services.openai.api_key'),
                [
                    'timeout' => 30.0,
                    'connect_timeout' => 30.0
                ]
            );
        });

        // Registrar el servicio de extracción de palabras clave
        $this->app->singleton(KeywordExtractorService::class, function ($app) {
            return new KeywordExtractorService(
                $app->make(OpenAI\Client::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}