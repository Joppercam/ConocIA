<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VerificadorCacheService;
use Illuminate\Support\Facades\Log;

class CleanVerificadorCacheCommand extends Command
{
    protected $signature = 'verificador:clean-cache';
    protected $description = 'Limpia el caché antiguo del verificador para optimizar el rendimiento';
    
    protected $cacheService;
    
    public function __construct(VerificadorCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }
    
    public function handle()
    {
        $this->info('Iniciando limpieza programada de caché del verificador...');
        
        try {
            $stats = $this->cacheService->runScheduledCacheCleaning();
            
            $this->info("Limpieza de caché completada con éxito:");
            $this->table(
                ['Tipo', 'Cantidad eliminada'],
                [
                    ['Verificaciones individuales', $stats['verificaciones_eliminadas']],
                    ['Listas de verificaciones', $stats['listas_eliminadas']],
                    ['Verificaciones destacadas', $stats['destacadas_eliminadas']],
                    ['Estadísticas', $stats['stats_eliminadas']],
                ]
            );
            
            // Registrar en logs
            Log::info('Limpieza de caché del verificador completada', $stats);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error al limpiar el caché: " . $e->getMessage());
            Log::error("Error en la limpieza de caché: " . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}