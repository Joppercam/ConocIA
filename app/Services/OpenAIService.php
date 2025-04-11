<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\ClaimCategory;
use App\Models\TrustedSource;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl;
    protected $modelName;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->modelName = config('services.openai.model_name', 'gpt-4-turbo');
    }
    
    /**
     * Extraer afirmaciones verificables de un texto
     *
     * @param string $text El texto del que extraer afirmaciones
     * @return array Lista de afirmaciones verificables
     */
    public function extractClaims($text)
    {
        $prompt = $this->getClaimExtractionPrompt($text);
        
        $cacheKey = 'claim_extraction_' . md5($text);
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($prompt) {
            try {
                $response = $this->sendRequest($prompt);
                
                // Parsear la respuesta JSON
                $data = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE || !isset($data['claims'])) {
                    Log::error('Error al parsear la respuesta de extracción de afirmaciones: ' . $response);
                    return [];
                }
                
                return $data['claims'];
            } catch (\Exception $e) {
                Log::error('Error al extraer afirmaciones: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * Categorizar una afirmación
     *
     * @param string $claim La afirmación a categorizar
     * @return int|null El ID de la categoría o null si no se puede categorizar
     */
    public function categorizeClaimById($claim)
    {
        // Obtener todas las categorías disponibles
        $categories = ClaimCategory::all();
        
        if ($categories->isEmpty()) {
            Log::error('No hay categorías disponibles para categorizar afirmaciones');
            return null;
        }
        
        $categoriesData = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description
            ];
        })->toArray();
        
        $prompt = $this->getCategorizationPrompt($claim, $categoriesData);
        
        $cacheKey = 'claim_categorization_' . md5($claim);
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($prompt, $categories) {
            try {
                $response = $this->sendRequest($prompt);
                
                // Parsear la respuesta JSON
                $data = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE || !isset($data['category_id'])) {
                    Log::error('Error al parsear la respuesta de categorización: ' . $response);
                    return $categories->first()->id; // Categoría por defecto
                }
                
                $categoryId = (int) $data['category_id'];
                
                // Verificar que la categoría existe
                if (!$categories->contains('id', $categoryId)) {
                    Log::error('La categoría devuelta no existe: ' . $categoryId);
                    return $categories->first()->id; // Categoría por defecto
                }
                
                return $categoryId;
            } catch (\Exception $e) {
                Log::error('Error al categorizar afirmación: ' . $e->getMessage());
                return $categories->first()->id; // Categoría por defecto
            }
        });
    }
    
    /**
     * Verificar una afirmación y evaluar su veracidad
     *
     * @param string $claimStatement La afirmación a verificar
     * @param string $source La fuente de la afirmación
     * @param string $context Contexto adicional (opcional)
     * @return array Resultado de la verificación
     */
    public function verifyClaim($claimStatement, $source, $context = null)
    {
        // Obtener fuentes confiables de la base de datos
        $trustedSources = TrustedSource::all()->pluck('url')->toArray();
        
        $prompt = $this->getVerificationPrompt($claimStatement, $source, $trustedSources, $context);
        
        $cacheKey = 'claim_verification_' . md5($claimStatement . $source . ($context ?? ''));
        
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($prompt) {
            try {
                $response = $this->sendRequest($prompt);
                
                // Parsear la respuesta JSON
                $data = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Error al parsear la respuesta de verificación: ' . $response);
                    return [
                        'verdict' => 'unverifiable',
                        'summary' => 'No se pudo verificar esta afirmación debido a un error técnico.',
                        'analysis' => 'Error al procesar la verificación.',
                        'evidence' => []
                    ];
                }
                
                // Validar campos obligatorios
                if (!isset($data['verdict']) || !isset($data['summary']) || !isset($data['analysis'])) {
                    Log::error('Faltan campos en la respuesta de verificación: ' . $response);
                    return [
                        'verdict' => 'unverifiable',
                        'summary' => 'No se pudo verificar esta afirmación debido a un error técnico.',
                        'analysis' => 'Error al procesar la verificación.',
                        'evidence' => []
                    ];
                }
                
                // Validar el veredicto
                if (!in_array($data['verdict'], ['true', 'partially_true', 'false', 'unverifiable'])) {
                    $data['verdict'] = 'unverifiable';
                }
                
                return $data;
            } catch (\Exception $e) {
                Log::error('Error al verificar afirmación: ' . $e->getMessage());
                return [
                    'verdict' => 'unverifiable',
                    'summary' => 'No se pudo verificar esta afirmación debido a un error técnico.',
                    'analysis' => 'Error en el proceso de verificación: ' . $e->getMessage(),
                    'evidence' => []
                ];
            }
        });
    }
    
    /**
     * Enviar una solicitud a la API de OpenAI
     *
     * @param string $prompt El prompt a enviar
     * @return string La respuesta de la API
     */
    protected function sendRequest($prompt)
    {
        $httpClient = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
        
        // Configurar SSL según entorno
        if (app()->environment('local', 'development')) {
            $httpClient = $httpClient->withoutVerifying();
        }
        
        $response = $httpClient->post($this->baseUrl . '/chat/completions', [
            'model' => $this->modelName,
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert fact-checker AI assistant that responds ONLY in JSON format.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.1, // Baja temperatura para respuestas más deterministas
            'max_tokens' => 2000,
        ]);
        
        if ($response->failed()) {
            Log::error('Error en la solicitud a OpenAI: ' . $response->body());
            throw new \Exception('Error en la solicitud a OpenAI: ' . $response->status());
        }
        
        return $response->json('choices.0.message.content');
    }
    
    /**
     * Generar prompt para extracción de afirmaciones
     */
    protected function getClaimExtractionPrompt($text)
    {
        return <<<EOT
Extrae de forma precisa las principales afirmaciones verificables del siguiente texto. 
Una afirmación verificable debe contener información factual que pueda ser contrastada con datos y fuentes.

TEXTO:
$text

Sigue estas instrucciones exactamente:
1. Identifica solo afirmaciones objetivas y verificables (datos, estadísticas, hechos históricos).
2. Ignora opiniones, juicios de valor o declaraciones subjetivas.
3. Extrae afirmaciones completas con todo su contexto para que tengan sentido de forma aislada.
4. Prioriza afirmaciones significativas sobre datos triviales.
5. Si hay afirmaciones idénticas o muy similares, selecciona solo la más completa.
6. Máximo 5 afirmaciones.

Responde SOLO en este formato JSON:
{
  "claims": [
    {
      "statement": "La afirmación completa y textual",
      "context": "Breve contexto sobre quién hizo la afirmación y cuándo (si está disponible)"
    }
  ]
}

Si no hay afirmaciones verificables, devuelve un array "claims" vacío.
EOT;
    }
    
    /**
     * Generar prompt para categorización de afirmaciones
     */
    protected function getCategorizationPrompt($claim, $categories)
    {
        $categoriesJson = json_encode($categories);
        
        return <<<EOT
Clasifica la siguiente afirmación en una de las categorías disponibles según su tema principal:

AFIRMACIÓN:
$claim

CATEGORÍAS DISPONIBLES:
$categoriesJson

Instrucciones:
1. Analiza cuidadosamente el tema principal de la afirmación.
2. Selecciona UNA SOLA categoría que mejor se ajuste al tema principal.
3. Si la afirmación podría clasificarse en varias categorías, elige la que represente el tema más destacado.
4. Si no hay una categoría claramente adecuada, elige la más cercana.

Responde ÚNICAMENTE en este formato JSON:
{
  "category_id": [ID numérico de la categoría],
  "explanation": "Breve explicación de por qué elegiste esta categoría (2-3 líneas)"
}
EOT;
    }
    
    /**
     * Generar prompt para verificación de afirmaciones
     */
    protected function getVerificationPrompt($claimStatement, $source, $trustedSources, $context = null)
    {
        $trustedSourcesJson = json_encode($trustedSources);
        $contextInfo = $context ? "CONTEXTO ADICIONAL:\n$context\n\n" : "";
        
        return <<<EOT
        Verifica la siguiente afirmación utilizando tu conocimiento y las fuentes confiables proporcionadas:
        
        AFIRMACIÓN:
        $claimStatement
        
        FUENTE DE LA AFIRMACIÓN:
        $source
        
        {$contextInfo}FUENTES CONFIABLES PARA VERIFICACIÓN:
        $trustedSourcesJson
        
        Instrucciones para verificación:
        1. Analiza cuidadosamente la afirmación en todos sus aspectos.
        2. Busca datos objetivos y verificables que confirmen o refuten la afirmación.
        3. Considera el contexto completo y posibles interpretaciones.
        4. Evalúa la precisión, actualidad y relevancia de los datos citados.
        5. Identifica posibles omisiones o distorsiones en la presentación de los hechos.
        6. Si la afirmación contiene múltiples partes, evalúa cada una por separado.
        7. Si no puedes verificar la afirmación con certeza, explica por qué.
        8. Asigna un nivel de confianza a tu veredicto entre 0.0 (mínima confianza) y 1.0 (máxima confianza).
        
        Niveles de verificación:
        - "true": Totalmente verdadera y respaldada por evidencia sólida.
        - "partially_true": Parcialmente verdadera, con algunas imprecisiones o simplificaciones.
        - "false": Falsa, contradice datos verificables.
        - "unverifiable": No se puede verificar con la información disponible.
        
        Responde ÚNICAMENTE en este formato JSON:
        {
          "verdict": "true/partially_true/false/unverifiable",
          "confidence_score": 0.X, // Un número entre 0.0 y 1.0 que indica tu nivel de confianza en el veredicto
          "summary": "Resumen conciso de la verificación (máximo 200 caracteres)",
          "analysis": "Análisis detallado y fundamentado que explique el veredicto. Incluye datos específicos, contexto relevante y análisis de la evidencia (500-800 palabras)",
          "explanation": "Explicación simplificada del resultado de la verificación (1-3 frases)",
          "evidence": [
            {
              "url": "URL de la fuente (si es aplicable)",
              "title": "Título descriptivo de la fuente",
              "description": "Breve descripción de cómo esta fuente contribuye a la verificación"
            }
          ]
        }
        EOT;
        }
        
}