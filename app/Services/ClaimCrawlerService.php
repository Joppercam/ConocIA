<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\TrustedSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class ClaimCrawlerService
{
    protected $openAIService;
    
    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    
    /**
     * Inicia el proceso de crawling en todas las fuentes de confianza
     * 
     * @return array Estadísticas del crawling
     */
    public function crawlAllSources()
    {
        $stats = [
            'processed_sources' => 0,
            'extracted_claims' => 0,
            'errors' => 0
        ];
        
        // Obtener fuentes activas para monitoreo
        $sources = TrustedSource::where('active_for_monitoring', true)->get();
        
        foreach ($sources as $source) {
            try {
                Log::info("Iniciando crawling en: {$source->name} ({$source->url})");
                
                $contentExtracted = $this->extractContent($source);
                
                if (empty($contentExtracted)) {
                    Log::warning("No se pudo extraer contenido de: {$source->url}");
                    $stats['errors']++;
                    continue;
                }
                
                // Extraer afirmaciones usando OpenAI
                $claims = $this->openAIService->extractClaims($contentExtracted);
                
                if (empty($claims)) {
                    Log::info("No se encontraron afirmaciones verificables en: {$source->url}");
                    $stats['processed_sources']++;
                    continue;
                }
                
                // Procesar cada afirmación extraída
                foreach ($claims as $claimData) {
                    $statement = $claimData['statement'] ?? null;
                    $context = $claimData['context'] ?? null;
                    
                    if (!$statement) {
                        continue;
                    }
                    
                    // Evitar duplicados recientes (últimos 30 días)
                    $existingClaim = Claim::where('statement', $statement)
                        ->where('created_at', '>=', now()->subDays(30))
                        ->first();
                    
                    if ($existingClaim) {
                        Log::info("Afirmación ya existente: {$statement}");
                        continue;
                    }
                    
                    // Categorizar la afirmación
                    $categoryId = $this->openAIService->categorizeClaimById($statement);
                    
                    if (!$categoryId) {
                        Log::error("No se pudo categorizar la afirmación: {$statement}");
                        continue;
                    }
                    
                    // En el método crawlAllSources de ClaimCrawlerService.php
                    $claim = Claim::create([
                        'statement' => $statement,
                        'context' => $context,
                        'source_name' => $source->name,
                        'source_url' => $source->url,
                        'source_type' => 'article', // O determinar dinámicamente según la fuente
                        'statement_date' => Carbon::now(),
                        'is_verified' => false,
                        'processed' => false
                    ]);

                    // Ahora asocia la categoría usando la relación many-to-many
                    if ($categoryId) {
                        $claim->categories()->attach($categoryId);
                    }
                    
                    $stats['extracted_claims']++;
                    Log::info("Nueva afirmación guardada: {$statement}");
                }
                
                $stats['processed_sources']++;
            } catch (\Exception $e) {
                Log::error("Error al procesar la fuente {$source->url}: " . $e->getMessage());
                $stats['errors']++;
            }
        }
        
        Log::info("Crawling finalizado. Estadísticas: " . json_encode($stats));
        return $stats;
    }
    
    /**
     * Extrae el contenido principal de una URL
     * 
     * @param TrustedSource $source La fuente de la que extraer contenido
     * @return string|null El contenido extraído o null si hay un error
     */
    protected function extractContent(TrustedSource $source)
    {
        try {
                    // Obtener el cliente HTTP (ahora ya configurado globalmente)
            $httpClient = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; FactCheckerBot/1.0; +https://tudominio.com/info/bot)'
            ]);
    
            // Realizar la petición
            $response = $httpClient->get($source->url);
            
            // Manejo específico para 401/403 (Reuters)
            if ($response->status() === 401 || $response->status() === 403) {
                Log::warning("La fuente {$source->url} requiere autenticación (Error {$response->status()})");
                return null;
            }
            
            if ($response->failed()) {
                Log::error("Error al obtener contenido de {$source->url}: " . $response->status());
                return null;
            }
            
            $html = $response->body();
            
            // Usar Symfony Crawler para extraer el contenido principal
            $crawler = new Crawler($html);
            
            // Intentar obtener el contenido según el selector CSS personalizado
            if (!empty($source->content_selector)) {
                $contentNodes = $crawler->filter($source->content_selector);
                
                if ($contentNodes->count() > 0) {
                    return $contentNodes->text();
                }
            }
            
            // Si no hay selector personalizado o falló, intentar con selectores comunes
            $selectors = [
                'article', 'main', '.content', '.post-content', '.entry-content',
                '#content', '.article-body', '.story-body'
            ];
            
            foreach ($selectors as $selector) {
                $contentNodes = $crawler->filter($selector);
                
                if ($contentNodes->count() > 0) {
                    return $contentNodes->text();
                }
            }
            
            // Si todo lo anterior falla, tomar el body completo
            $bodyContent = $crawler->filter('body')->text();
            
            // Limpiar el contenido (eliminar espacios excesivos, etc.)
            return trim(preg_replace('/\s+/', ' ', $bodyContent));
        } catch (\Exception $e) {
            Log::error("Error al extraer contenido de {$source->url}: " . $e->getMessage());
            return null;
        }
    }
}