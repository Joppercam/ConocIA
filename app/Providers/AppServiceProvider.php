<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\NewsHistoric;
use App\Observers\NewsHistoricObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use App\Observers\NewsObserver;
use App\Models\News;
use App\Http\ViewComposers\AdminLayoutComposer;

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

        // Registrar observer para News
        News::observe(NewsObserver::class);

        // Configurar el locale predeterminado para Carbon
        Carbon::setLocale(config('app.locale', 'es'));

        NewsHistoric::observe(NewsHistoricObserver::class);

        Carbon::macro('formatSpanish', function ($format = 'D [de] MMMM, YYYY') {
            return $this->locale('es')->isoFormat($format);
        });

        // Registra el helper como una directiva Blade
        Blade::directive('newsImage', function ($expression) {
            return "<?php echo App\ImageHelper::getNewsImage($expression); ?>";
        });
        View::composer('admin.layouts.app', AdminLayoutComposer::class);
    }
}
