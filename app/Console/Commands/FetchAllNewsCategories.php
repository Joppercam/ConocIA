<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FetchAllNewsCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch-all {--count=2} {--language=es}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene 2 noticias por cada categoría disponible';

    /**
     * Lista de categorías a consultar
     * 
     * @var array
     */
    protected $categories = [
        // Categorías técnicas
        'inteligencia-artificial',
        'machine-learning',
        'deep-learning',
        'nlp',
        'computer-vision',
        'robotica',
        'computacion-cuantica',
        
        // Categorías empresariales
        'openai',
        'google-ai',
        'microsoft-ai',
        'meta-ai',
        'amazon-ai',
        'anthropic',
        'startups-de-ia',
        
        // Categorías de aplicación
        'ia-generativa',
        'automatizacion',
        'ia-en-salud',
        'ia-en-finanzas',
        'ia-en-educacion',
        
        // Categorías sociales/impacto
        'etica-de-la-ia',
        'regulacion-de-ia',
        'impacto-laboral',
        'privacidad-y-seguridad',
        
        // Categorías generales
        'tecnologia',
        'investigacion',
        'ciberseguridad',
        'innovacion',
        'etica'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->option('count');
        $language = $this->option('language');

        $this->info("Iniciando búsqueda de {$count} noticias por cada categoría...");
        $this->info("Total de categorías a procesar: " . count($this->categories));

        $bar = $this->output->createProgressBar(count($this->categories));
        $bar->start();
        
        $successCount = 0;
        $failCount = 0;

        foreach ($this->categories as $category) {
            $this->info("\nProcesando categoría: {$category}");
            
            try {
                // Ejecutar el comando de búsqueda para cada categoría
                $exitCode = Artisan::call('news:fetch', [
                    '--category' => $category,
                    '--count' => $count,
                    '--language' => $language,
                ]);
                
                // Obtener la salida del comando
                $output = Artisan::output();
                
                if ($exitCode === 0) {
                    $this->info("Categoría {$category} procesada correctamente");
                    $successCount++;
                } else {
                    $this->error("Error al procesar categoría {$category}");
                    $this->error("Salida: " . $output);
                    $failCount++;
                }
            } catch (\Exception $e) {
                $this->error("Excepción al procesar categoría {$category}: " . $e->getMessage());
                $failCount++;
            }
            
            // Avanzar la barra de progreso
            $bar->advance();
            
            // Pequeña pausa para evitar sobrecargar las APIs
            sleep(2);
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("Proceso completado. Categorías procesadas con éxito: {$successCount}. Fallidas: {$failCount}");

        return 0;
    }
}