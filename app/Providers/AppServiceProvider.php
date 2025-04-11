<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\NewsHistoric;
use App\Observers\NewsHistoricObserver;
use Carbon\Carbon;
use App\ImageHelper;
use App\Models\SocialMediaQueue;
use Illuminate\Support\Facades\View;
use App\Observers\NewsObserver;
use App\Models\News;
use App\Http\ViewComposers\TikTokComposer;
use App\Models\Verification;
use App\Observers\VerificationObserver;
use Illuminate\Support\Facades\Http;


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


        // Compartir datos de publicaciones pendientes con todas las vistas de admin
        View::composer('admin.*', function ($view) {
            // Solo calcula estos valores si el usuario está autenticado
            if (auth()->check() && auth()->user()->isAdmin()) {
                $pendingSocialCount = SocialMediaQueue::where('status', 'pending')->count();
                $pendingSocialPosts = SocialMediaQueue::where('status', 'pending')
                    ->with('news')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
                
                $view->with(compact('pendingSocialCount', 'pendingSocialPosts'));
            }
        });

         // Registrar el ViewComposer para TikTok
         View::composer('admin.layouts.app', TikTokComposer::class);
         View::composer('admin.partials.sidebar', TikTokComposer::class);


         // Registrar el observador de verificaciones
        Verification::observe(VerificationObserver::class);

        // Configurar la verificación SSL globalmente para entornos de desarrollo
        if (app()->environment('local', 'development')) {
            Http::globalOptions(['verify' => false]);
        }
    }
}
