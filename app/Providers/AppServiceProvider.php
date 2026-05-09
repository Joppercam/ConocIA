<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
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
        // Limpiar caché de vistas una sola vez si hay vistas compiladas corruptas
        if (!Cache::get('views_cleared_2026_05_09')) {
            Artisan::call('view:clear');
            Cache::put('views_cleared_2026_05_09', true, now()->addDays(30));
        }

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
