<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar directiva de caché
        Blade::directive('cache', function ($expression) {
            return "<?php if (! app('cache')->has($expression)) { \$__cache_key = $expression; ob_start(); ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php \$__cache_content = ob_get_clean(); app('cache')->put(\$__cache_key, \$__cache_content, now()->addSeconds(config('cache.ttl', 3600))); echo \$__cache_content; } else { echo app('cache')->get(\$__cache_key); } ?>";
        });
        
        // Caché temporal (1 minuto) para desarrollo
        Blade::directive('cachedev', function ($expression) {
            return "<?php if (! app('cache')->has($expression)) { \$__cache_key = $expression; ob_start(); ?>";
        });

        Blade::directive('endcachedev', function () {
            return "<?php \$__cache_content = ob_get_clean(); app('cache')->put(\$__cache_key, \$__cache_content, now()->addMinutes(1)); echo \$__cache_content; } else { echo app('cache')->get(\$__cache_key); } ?>";
        });

        // Directiva para caché condicional (sólo en producción)
        Blade::directive('cacheprod', function ($expression) {
            return "<?php if (app()->environment('production') && ! app('cache')->has($expression)) { \$__cache_key = $expression; ob_start(); ?>";
        });

        Blade::directive('endcacheprod', function () {
            return "<?php if (app()->environment('production')) { \$__cache_content = ob_get_clean(); app('cache')->put(\$__cache_key, \$__cache_content, now()->addHours(1)); echo \$__cache_content; } else { echo ob_get_clean(); } } else { echo app('cache')->get(\$__cache_key); } ?>";
        });
    }
}