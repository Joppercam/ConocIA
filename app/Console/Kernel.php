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