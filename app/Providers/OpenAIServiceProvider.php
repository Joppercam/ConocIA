<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\KeywordExtractorService;

class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(KeywordExtractorService::class, function () {
            return new KeywordExtractorService();
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
