<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\ImageHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra el helper como una directiva Blade
        Blade::directive('newsImage', function ($expression) {
            return "<?php echo App\ImageHelper::getNewsImage($expression); ?>";
        });
    }
}
