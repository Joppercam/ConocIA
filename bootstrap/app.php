<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\App\Http\Middleware\RedirectWww::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Gemini Search Grounding — 1 noticia por hora (evita timeout)
        $schedule->command('news:fetch-gemini --all --count=1 --days=1')
            ->hourly()
            ->withoutOverlapping(55)
            ->before(fn() => Log::info('[SCHEDULER] ▶ Iniciando news:fetch-gemini', ['at' => now()->toDateTimeString()]))
            ->after(fn() => Log::info('[SCHEDULER] ✓ Completado news:fetch-gemini', ['at' => now()->toDateTimeString()]))
            ->appendOutputTo(storage_path('logs/fetch-gemini-news.log'));

        // NewsAPI — 1x al día
        $schedule->command('news:fetch-all --count=1')
            ->dailyAt('08:00')
            ->withoutOverlapping(60)
            ->before(fn() => Log::info('[SCHEDULER] ▶ Iniciando news:fetch-all', ['at' => now()->toDateTimeString()]))
            ->after(fn() => Log::info('[SCHEDULER] ✓ Completado news:fetch-all', ['at' => now()->toDateTimeString()]))
            ->appendOutputTo(storage_path('logs/fetch-all-news.log'));

        // RSS curado — 1 noticia cada 30 minutos (evita timeout por reescritura IA)
        $schedule->command('news:fetch-rss --limit=1')
            ->everyThirtyMinutes()
            ->withoutOverlapping(25)
            ->before(fn() => Log::info('[SCHEDULER] ▶ Iniciando news:fetch-rss', ['at' => now()->toDateTimeString()]))
            ->after(fn() => Log::info('[SCHEDULER] ✓ Completado news:fetch-rss', ['at' => now()->toDateTimeString()]))
            ->appendOutputTo(storage_path('logs/fetch-rss.log'));

        // The Guardian API — 1x al día
        $schedule->command('news:fetch-guardian --limit=5')
            ->dailyAt('10:00')
            ->withoutOverlapping(20)
            ->before(fn() => Log::info('[SCHEDULER] ▶ Iniciando news:fetch-guardian', ['at' => now()->toDateTimeString()]))
            ->after(fn() => Log::info('[SCHEDULER] ✓ Completado news:fetch-guardian', ['at' => now()->toDateTimeString()]))
            ->appendOutputTo(storage_path('logs/fetch-guardian.log'));

        // Pexels: imágenes faltantes
        $schedule->command('news:fetch-missing-images --limit=30')
            ->dailyAt('07:30')->withoutOverlapping(15)
            ->appendOutputTo(storage_path('logs/fetch-missing-images.log'));
        $schedule->command('news:fetch-missing-images --limit=30')
            ->dailyAt('17:30')->withoutOverlapping(15)
            ->appendOutputTo(storage_path('logs/fetch-missing-images.log'));

        // Newsletter semanal — lunes 08:00
        $schedule->command('newsletter:send --news=5 --include-research --include-columns')
            ->weekly()->mondays()->at('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/newsletter-cron.log'));

        // Auto-aprobación de comentarios — cada 3 minutos
        $schedule->command('comments:auto-approve')
            ->everyThreeMinutes()
            ->appendOutputTo(storage_path('logs/comments-auto-approve.log'));

        // Archivo de noticias antiguas — 02:00
        $schedule->command('news:archive')->dailyAt('02:00');

        // Conceptos IA — lunes 06:00
        $schedule->command('conceptos:generate --count=1')
            ->weekly()->mondays()->at('06:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/conceptos-ia.log'));

        // Análisis de fondo — miércoles 14:00
        $schedule->command('analisis:generate')
            ->weekly()->wednesdays()->at('14:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/analisis-fondo.log'));

        // Papers arXiv — lunes y jueves 23:00
        $schedule->command('papers:fetch-arxiv --max-results=2')
            ->days([1, 4])->at('23:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/papers-arxiv.log'));

        // Estado del Arte — domingos 20:00
        $schedule->command('digest:generate --all')
            ->weekly()->sundays()->at('20:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/estado-arte.log'));

        // Videos YouTube — martes y viernes 10:00
        $schedule->command('videos:fetch-youtube --per-query=3')
            ->days([2, 5])->at('10:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/youtube-fetch.log'));

        // Resúmenes de videos — diario 08:00
        $schedule->command('videos:generate-summaries --limit=5')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/video-summaries.log'));

        // Briefing diario — 08:30
        $schedule->command('briefing:generate')
            ->dailyAt('08:30')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/briefing.log'));
    })
    ->create();
