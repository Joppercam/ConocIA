<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishCommentsConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:publish-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish comments configuration files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Publishing comments configuration files...');
        
        // Publicar configuración
        Artisan::call('vendor:publish', [
            '--provider' => 'App\\Providers\\CommentServiceProvider',
            '--tag' => 'config'
        ]);
        
        $this->info('Configuration files published successfully!');
        
        // Comprobar si existe config/comments.php
        if (file_exists(config_path('comments.php'))) {
            $this->info('Comments configuration file exists at: ' . config_path('comments.php'));
        } else {
            $this->error('Comments configuration file was not published correctly.');
            return 1;
        }
        
        $this->info('Remember to add the following variables to your .env file:');
        $this->line('COMMENTS_ENABLE_ADVANCED_ANALYSIS=false');
        $this->line('TEXT_ANALYSIS_API_KEY=your_api_key_here');
        $this->line('TEXT_ANALYSIS_API_URL=https://api.example.com/v1/analyze');
        
        return 0;
    }
}