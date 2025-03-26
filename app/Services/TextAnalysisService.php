<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TextAnalysisService
{
    /**
     * Cliente HTTP
     * 
     * @var \GuzzleHttp\Client
     */
    protected $client;
    
    /**
     * API Key para el servicio de análisis
     * 
     * @var string
     */
    protected $apiKey;
    
    /**
     * URL base para la API
     * 
     * @var string
     */
    protected $apiUrl;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'http_errors' => false,
        ]);
        
        $this->apiKey = Config::get('services.text_analysis.api_key');
        $this->apiUrl = Config::get('services.text_analysis.api_url');
    }
    
    /**
     * Detectar si un texto es tóxico o inapropiado
     * 
     * @param string $text
     * @return array
     */
    public function detectToxicity(string $text): array
    {
        // Verificar si la configuración está habilitada
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            Log::warning('El servicio de análisis de texto no está configurado correctamente.');
            return [
                'success' => false,
                'is_toxic' => false,
                'reason' => 'Servicio no configurado',
                'score' => 0
            ];
        }
        
        try {
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'text' => $text,
                    'languages' => ['es'], // Análisis en español
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() !== 200 || !isset($data['results'])) {
                Log::error('Error en la API de análisis de texto: ' . $response->getBody());
                return [
                    'success' => false,
                    'is_toxic' => false,
                    'reason' => 'Error en el servicio',
                    'score' => 0
                ];
            }
            
            // Interpretar resultados (ajustar según la API específica que uses)
            $toxicity = $data['results']['toxicity'] ?? 0;
            $profanity = $data['results']['profanity'] ?? 0;
            $threat = $data['results']['threat'] ?? 0;
            $insult = $data['results']['insult'] ?? 0;
            
            $maxScore = max($toxicity, $profanity, $threat, $insult);
            $isToxic = $maxScore > 0.7; // Umbral de 70%
            
            $reason = '';
            if ($toxicity > 0.7) $reason = 'Contenido tóxico';
            elseif ($profanity > 0.7) $reason = 'Lenguaje obsceno';
            elseif ($threat > 0.7) $reason = 'Contenido amenazante';
            elseif ($insult > 0.7) $reason = 'Contenido insultante';
            
            return [
                'success' => true,
                'is_toxic' => $isToxic,
                'reason' => $reason,
                'score' => $maxScore
            ];
            
        } catch (\Exception $e) {
            Log::error('Excepción en el servicio de análisis de texto: ' . $e->getMessage());
            return [
                'success' => false,
                'is_toxic' => false,
                'reason' => 'Error en el servicio: ' . $e->getMessage(),
                'score' => 0
            ];
        }
    }
    
    /**
     * Detectar el idioma de un texto
     * 
     * @param string $text
     * @return string|null
     */
    public function detectLanguage(string $text): ?string
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return null;
        }
        
        try {
            $response = $this->client->post($this->apiUrl . '/language', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'text' => $text
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() !== 200 || !isset($data['language'])) {
                return null;
            }
            
            return $data['language'];
            
        } catch (\Exception $e) {
            Log::error('Error al detectar idioma: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Detectar si un texto es spam
     * 
     * @param string $text
     * @return bool
     */
    public function isSpam(string $text): bool
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return false;
        }
        
        try {
            $response = $this->client->post($this->apiUrl . '/spam', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'text' => $text
                ]
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() !== 200 || !isset($data['spam_score'])) {
                return false;
            }
            
            return $data['spam_score'] > 0.7; // Umbral de 70%
            
        } catch (\Exception $e) {
            Log::error('Error al detectar spam: ' . $e->getMessage());
            return false;
        }
    }
}