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
        // Buscar 2 noticias por cada categoría cada 12 horas (a las 8:00 AM y 8:00 PM)
        $schedule->command('news:fetch-all --count=2')
            ->twiceDaily(8, 20)
            ->withoutOverlapping(60) // Evita que se ejecute si la ejecución anterior sigue en curso (timeout de 60 min)
            ->appendOutputTo(storage_path('logs/fetch-all-news.log'));
   
   
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


  
             
        // Publicación principal - 3 veces al día en días laborables
        $schedule->command('news:publish-twitter --limit=1')
        ->weekdays()
        ->at('09:00')
        ->appendOutputTo(storage_path('logs/social-media-twitter.log'));

        $schedule->command('news:publish-twitter --limit=1')
            ->weekdays()
            ->at('13:00')
            ->appendOutputTo(storage_path('logs/social-media-twitter.log'));

        $schedule->command('news:publish-twitter --limit=1')
            ->weekdays()
            ->at('18:00')
            ->appendOutputTo(storage_path('logs/social-media-twitter.log'));

        // Fines de semana - Una publicación por día
        $schedule->command('news:publish-twitter --limit=1')
            ->saturdays()
            ->at('12:00')
            ->appendOutputTo(storage_path('logs/social-media-twitter.log'));

        $schedule->command('news:publish-twitter --limit=1')
            ->sundays()
            ->at('17:00')
            ->appendOutputTo(storage_path('logs/social-media-twitter.log'));

        // Monitoreo diario - Para mantener registro del uso de la API
        $schedule->command('news:publish-twitter --dry-run')
            ->dailyAt('00:01')
            ->appendOutputTo(storage_path('logs/twitter-usage-stats.log'));

        $schedule->command('news:archive')->dailyAt('02:00');
   

                // Generar guiones de TikTok automáticamente dos veces al día
                $schedule->command('tiktok:generate-scripts --count=5')
                ->twiceDaily(9, 16)
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/tiktok-scripts.log'));
        
        // Enviar notificación a los administradores sobre nuevos guiones pendientes de revisión
        $schedule->command('tiktok:notify-pending-scripts')
                ->dailyAt('10:00')
                ->weekdays()
                ->when(function () {
                    // Solo enviar notificación si hay guiones pendientes
                    return \App\Models\TikTokScript::where('status', 'pending_review')->exists();
                });


    
        // Ejecutar crawling cada 1 hora
        $schedule->command('verificador:process')
        ->hourly()
        ->appendOutputTo(storage_path('logs/verificador.log'))
        ->emailOutputOnFailure(config('mail.admin_email'));
        
        // Limpiar el caché de verificaciones antiguas cada día a medianoche
        $schedule->command('verificador:clean-cache')
            ->dailyAt('00:00')
        ->appendOutputTo(storage_path('logs/verificador.log'));
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