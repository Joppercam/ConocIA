<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ClaimCrawlerService;
use App\Services\OpenAIService;
use App\Models\Claim;
use App\Models\Verification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessVerificationsCommand extends Command
{
    protected $signature = 'verificador:process {--crawl-only : Solo ejecutar el crawling sin verificación} {--verify-only : Solo verificar afirmaciones pendientes sin crawling}';
    protected $description = 'Procesa el crawling de fuentes y verifica afirmaciones pendientes';
    
    protected $claimCrawlerService;
    protected $openAIService;
    
    public function __construct(ClaimCrawlerService $claimCrawlerService, OpenAIService $openAIService)
    {
        parent::__construct();
        $this->claimCrawlerService = $claimCrawlerService;
        $this->openAIService = $openAIService;
    }
    
    public function handle()
    {
        $this->info('Iniciando proceso del verificador autónomo...');
        
        $crawlOnly = $this->option('crawl-only');
        $verifyOnly = $this->option('verify-only');
        
        if (!$verifyOnly) {
            $this->runCrawling();
        }
        
        if (!$crawlOnly) {
            $this->runVerification();
        }
        
        $this->info('Proceso del verificador autónomo completado');
        
        return Command::SUCCESS;
    }
    
    /**
     * Ejecuta el proceso de crawling de fuentes
     */
    protected function runCrawling()
    {
        $this->info('Iniciando crawling de fuentes...');
        
        try {
            $stats = $this->claimCrawlerService->crawlAllSources();
            
            $this->info("Crawling completado. Procesadas {$stats['processed_sources']} fuentes, extraídas {$stats['extracted_claims']} afirmaciones.");
            
            if ($stats['errors'] > 0) {
                $this->warn("Se encontraron {$stats['errors']} errores durante el crawling.");
            }
        } catch (\Exception $e) {
            $this->error("Error al ejecutar el crawling: " . $e->getMessage());
            Log::error("Error en comando de crawling: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Ejecuta el proceso de verificación de afirmaciones pendientes
     */
    protected function runVerification()
    {
        $this->info('Iniciando verificación de afirmaciones pendientes...');
        
        try {
            // Obtener afirmaciones pendientes (limit para evitar sobrecargar la API)
            $pendingClaims = Claim::where('processed', false)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $this->info("Se encontraron {$pendingClaims->count()} afirmaciones pendientes de verificación");
            
            $processed = 0;
            $errors = 0;
            
            foreach ($pendingClaims as $claim) {
                $this->info("Verificando afirmación: " . Str::limit($claim->statement, 50));
                
                try {
                    // Verificar la afirmación
                    $verificationData = $this->openAIService->verifyClaim(
                        $claim->statement,
                        $claim->source,
                        $claim->context
                    );
                    
                // Crear verificación en la base de datos
                    Verification::create([
                        'claim_id' => $claim->id,
                        'verdict' => $verificationData['verdict'] ?? 'unverifiable',
                        'summary' => $verificationData['summary'] ?? null,
                        'analysis' => $verificationData['analysis'] ?? null,
                        'explanation' => $verificationData['explanation'] ?? $verificationData['summary'] ?? null,
                        'evidence' => $verificationData['evidence'] ?? [],
                        'evidence_sources' => $verificationData['evidence_sources'] ?? [], // Añadir este campo
                        'views_count' => 0,
                        'confidence_score' => $verificationData['confidence_score'] ?? 0.5 // Valor por defecto de confianza media
                    ]);
                    
                    // Marcar la afirmación como procesada
                    $claim->processed = true;
                    $claim->save();
                    
                    $processed++;
                    
                    // Esperar un poco para evitar sobrecargar la API
                    sleep(1);
                } catch (\Exception $e) {
                    $this->error("Error al verificar afirmación {$claim->id}: " . $e->getMessage());
                    Log::error("Error al verificar afirmación {$claim->id}: " . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString(),
                        'claim' => $claim->toArray()
                    ]);
                    $errors++;
                }
            }
            
            $this->info("Verificación completada. Procesadas {$processed} afirmaciones.");
            
            if ($errors > 0) {
                $this->warn("Se encontraron {$errors} errores durante la verificación.");
            }
        } catch (\Exception $e) {
            $this->error("Error al ejecutar la verificación: " . $e->getMessage());
            Log::error("Error en comando de verificación: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}