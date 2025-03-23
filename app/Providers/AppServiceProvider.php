<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
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

        // Configurar el locale predeterminado para Carbon
        Carbon::setLocale(config('app.locale', 'es'));

        Carbon::macro('formatSpanish', function ($format = 'D [de] MMMM, YYYY') {
            return $this->locale('es')->isoFormat($format);
        });

        // Registra el helper como una directiva Blade
        Blade::directive('newsImage', function ($expression) {
            return "<?php echo App\ImageHelper::getNewsImage($expression); ?>";
        });
    }
}
