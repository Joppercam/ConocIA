<?php

namespace App\Console\Commands;

use App\Services\CategoryScheduler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetchScheduledNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene noticias de las categorías programadas para la hora actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener la hora y fecha actuales
        $currentHour = (int) now()->format('G'); // Formato 24 horas (0-23)
        $currentDate = now()->format('Y-m-d');
        
        // Comprobar si estamos dentro del horario permitido (8 AM - 10 PM)
        if ($currentHour < 8 || $currentHour > 22) {
            $this->info('Fuera del horario operativo (8:00 - 22:00). No se procesarán noticias.');
            Log::info('News fetcher: Ejecución fuera del horario operativo (' . $currentHour . ':00).');
            return 0;
        }
        
        // Obtener las categorías a procesar en esta hora
        $categories = CategoryScheduler::getCategoriesToProcess($currentHour, $currentDate);
        
        if (empty($categories)) {
            $this->info('No hay categorías programadas para procesar a esta hora.');
            Log::info('News fetcher: No hay categorías programadas para esta hora (' . $currentHour . ':00).');
            return 0;
        }
        
        $this->info('Procesando ' . count($categories) . ' categorías a las ' . $currentHour . ':00: ' . implode(', ', $categories));
        Log::info('News fetcher: Iniciando proceso de ' . count($categories) . ' categorías: ' . implode(', ', $categories));
        
        // Número de noticias por categoría
        $newsPerCategory = 2;
        
        // Procesar cada categoría
        foreach ($categories as $category) {
            $this->info('Obteniendo noticias para: ' . $category);
            
            try {
                // Ejecutar el comando existente para esta categoría
                $exitCode = Artisan::call('news:fetch', [
                    '--category' => $category,
                    '--count' => $newsPerCategory,
                    '--language' => 'es',
                    '--generate-comments'=> 1,
                    '--min-comments'=>2,
                    '--max-comments'=>3
                ]);
                
                $commandOutput = Artisan::output();
                
                if ($exitCode === 0) {
                    $this->info('Noticias obtenidas exitosamente para ' . $category);
                    Log::info('News fetcher: Éxito para categoría ' . $category);
                } else {
                    $this->error('Error al obtener noticias para ' . $category . ' (código: ' . $exitCode . ')');
                    Log::error('News fetcher: Error para categoría ' . $category . ' (código: ' . $exitCode . ')');
                    Log::debug('Output del comando: ' . $commandOutput);
                }
            } catch (\Exception $e) {
                $this->error('Excepción al procesar categoría ' . $category . ': ' . $e->getMessage());
                Log::error('News fetcher: Excepción para categoría ' . $category . ': ' . $e->getMessage());
            }
            
            // Pequeña pausa entre categorías para evitar sobrecargar las APIs
            sleep(5);
        }
        
        $this->info('Proceso completado para todas las categorías programadas.');
        Log::info('News fetcher: Proceso completado para la hora ' . $currentHour . ':00');
        
        return 0;
    }
}