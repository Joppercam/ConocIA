<?php

namespace App\Providers;

use App\Services\CommentValidationService;
use App\Services\CommentValidationServiceEnhanced;
use App\Services\TextAnalysisService;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TextAnalysisService::class, function ($app) {
            return new TextAnalysisService();
        });
        
        $this->app->singleton(CommentValidationService::class, function ($app) {
            // Si el análisis avanzado está habilitado, usar la versión mejorada
            if (config('comments.enable_advanced_analysis', false)) {
                return new CommentValidationServiceEnhanced(
                    $app->make(TextAnalysisService::class)
                );
            }
            
            // De lo contrario, usar la versión básica
            return new CommentValidationService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publicar archivos de configuración
        $this->publishes([
            __DIR__.'/../config/comments.php' => config_path('comments.php'),
        ], 'config');
    }
}