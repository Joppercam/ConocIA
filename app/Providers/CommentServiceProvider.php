<?php

namespace App\Providers;

use App\Services\CommentValidationService;
use App\Services\TextAnalysisService;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TextAnalysisService::class, fn() => new TextAnalysisService());

        $this->app->singleton(CommentValidationService::class, function ($app) {
            $textAnalysis = config('comments.enable_advanced_analysis', false)
                ? $app->make(TextAnalysisService::class)
                : null;

            return new CommentValidationService($textAnalysis);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/comments.php' => config_path('comments.php'),
        ], 'config');
    }
}
