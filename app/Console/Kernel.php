<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

     /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ApproveCommentsCommand::class,
        Commands\PublishCommentsConfigCommand::class,
        Commands\TestCommentValidationCommand::class,
    ];

    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Gemini Search Grounding: 3 noticias por las 4 categorías más activas — 2x al día
        // No consume NewsAPI, Gemini busca en Google en tiempo real
        $schedule->command('news:fetch-gemini --all --count=3 --days=2')
            ->twiceDaily(7, 17)
            ->withoutOverlapping(90)
            ->appendOutputTo(storage_path('logs/fetch-gemini-news.log'));

        // NewsAPI: 1 noticia por categoría IA (sin categorías genéricas) — 1x al día
        $schedule->command('news:fetch-all --count=1')
            ->dailyAt('08:00')
            ->withoutOverlapping(60)
            ->appendOutputTo(storage_path('logs/fetch-all-news.log'));

        // RSS curado (Xataka, Hipertextual, VentureBeat, The Verge, TechCrunch) — 2x al día
        $schedule->command('news:fetch-rss --limit=3')
            ->twiceDaily(9, 18)
            ->withoutOverlapping(30)
            ->appendOutputTo(storage_path('logs/fetch-rss.log'));

        // The Guardian API — 1x al día (requiere GUARDIAN_API_KEY en .env)
        $schedule->command('news:fetch-guardian --limit=5')
            ->dailyAt('10:00')
            ->withoutOverlapping(20)
            ->appendOutputTo(storage_path('logs/fetch-guardian.log'));

        // Pexels: rellenar imágenes faltantes — 3x al día, 30 min después de los fetches
        $schedule->command('news:fetch-missing-images --limit=30')
            ->dailyAt('07:30')
            ->withoutOverlapping(15)
            ->appendOutputTo(storage_path('logs/fetch-missing-images.log'));
        $schedule->command('news:fetch-missing-images --limit=30')
            ->dailyAt('17:30')
            ->withoutOverlapping(15)
            ->appendOutputTo(storage_path('logs/fetch-missing-images.log'));
   
   
            $schedule->command('newsletter:send --news=5 --include-research --include-columns')
            ->weekly()
            ->mondays()
            ->at('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/newsletter-cron.log'));
   


       // Ejecutar el comando de aprobación automática de comentarios cada 3 minutos
       $schedule->command('comments:auto-approve')
            ->everyThreeMinutes()
            ->appendOutputTo(storage_path('logs/comments-auto-approve.log'));


  
             
        // Twitter desactivado temporalmente (sin credenciales configuradas)
        // Para reactivar: configurar TWITTER_* en .env y descomentar estas tareas
        // $schedule->command('news:publish-twitter --limit=1')->weekdays()->at('09:00')->appendOutputTo(storage_path('logs/social-media-twitter.log'));
        // $schedule->command('news:publish-twitter --limit=1')->weekdays()->at('13:00')->appendOutputTo(storage_path('logs/social-media-twitter.log'));
        // $schedule->command('news:publish-twitter --limit=1')->weekdays()->at('18:00')->appendOutputTo(storage_path('logs/social-media-twitter.log'));
        // $schedule->command('news:publish-twitter --limit=1')->saturdays()->at('12:00')->appendOutputTo(storage_path('logs/social-media-twitter.log'));
        // $schedule->command('news:publish-twitter --limit=1')->sundays()->at('17:00')->appendOutputTo(storage_path('logs/social-media-twitter.log'));
        // $schedule->command('news:publish-twitter --dry-run')->dailyAt('00:01')->appendOutputTo(storage_path('logs/twitter-usage-stats.log'));

        $schedule->command('news:archive')->dailyAt('02:00');

        // ── Sección "Profundiza" ──────────────────────────────────────────────

        // Conceptos IA: un concepto nuevo cada lunes a las 06:00 (quota baja)
        $schedule->command('conceptos:generate --count=1')
            ->weekly()->mondays()->at('06:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/conceptos-ia.log'));

        // Análisis de Fondo: un análisis editorial cada miércoles a las 14:00
        $schedule->command('analisis:generate')
            ->weekly()->wednesdays()->at('14:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/analisis-fondo.log'));

        // Papers arXiv: lunes y jueves a las 23:00 (off-peak, max 2 por categoría)
        $schedule->command('papers:fetch-arxiv --max-results=2')
            ->twiceWeekly(1, 4)->at('23:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/papers-arxiv.log'));

        // Estado del Arte: todos los domingos a las 20:00 (usa noticias acumuladas)
        $schedule->command('digest:generate --all')
            ->weekly()->sundays()->at('20:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/estado-arte.log'));

        // Importar videos de YouTube sobre IA (martes y viernes a las 10:00)
        $schedule->command('videos:fetch-youtube --per-query=3')
            ->twiceWeekly(2, 5)->at('10:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/youtube-fetch.log'));

        // Generar resúmenes IA para videos nuevos (una vez al día)
        $schedule->command('videos:generate-summaries --limit=5')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/video-summaries.log'));

        // Generar briefing diario con IA cada mañana (después de fetch-all y fetch-rss de las 08:00/09:00)
        $schedule->command('briefing:generate')
            ->dailyAt('08:30')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/briefing.log'));
   

                // TikTok desactivado temporalmente
                // $schedule->command('tiktok:generate-scripts --count=5')->twiceDaily(9, 16)->withoutOverlapping()->appendOutputTo(storage_path('logs/tiktok-scripts.log'));
                // $schedule->command('tiktok:notify-pending-scripts')->dailyAt('10:00')->weekdays();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}