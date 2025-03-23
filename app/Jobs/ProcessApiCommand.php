<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessApiCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category;
    protected $count;
    protected $language;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 1 hora

    /**
     * Create a new job instance.
     */
    public function __construct($category, $count, $language)
    {
        $this->category = $category;
        $this->count = $count;
        $this->language = $language;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando ejecución de comando de API de noticias en cola', [
                'category' => $this->category,
                'count' => $this->count
            ]);
            
            // Ejecutar el comando Artisan
            $exitCode = Artisan::call('news:fetch', [
                '--category' => $this->category,
                '--count' => $this->count,
                '--language' => $this->language
            ]);
            
            $output = Artisan::output();
            
            Log::info('Comando de API de noticias completado', [
                'exit_code' => $exitCode,
                'output' => substr($output, 0, 500) . (strlen($output) > 500 ? '...' : '')
            ]);
        } catch (\Exception $e) {
            Log::error('Error en job de API de noticias', [
                'category' => $this->category,
                'count' => $this->count,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}