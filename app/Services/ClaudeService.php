<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para la API de Anthropic (Claude).
 * Usado como fallback de alta calidad para generación de contenido editorial
 * cuando Gemini no está disponible o supera su cuota.
 *
 * Modelo recomendado: claude-3-5-sonnet-20241022
 * Pricing: $3/1M input · $15/1M output — óptimo para contenido largo tipo Profundiza.
 */
class ClaudeService
{
    private string $apiKey;
    private string $model;
    private string $apiVersion = '2023-06-01';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
        $this->model  = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Genera contenido estructurado y devuelve el array decodificado.
     * Espera que el modelo responda en JSON — el system prompt lo refuerza.
     *
     * @param  string $prompt     Instrucciones completas para el modelo
     * @param  int    $maxTokens  Máximo de tokens en la respuesta
     * @param  float  $temperature
     * @return array  Array decodificado del JSON generado, o [] en error
     */
    public function generateJson(string $prompt, int $maxTokens = 4096, float $temperature = 0.7): array
    {
        if (!$this->isAvailable()) {
            Log::warning('ClaudeService: ANTHROPIC_API_KEY no configurado.');
            return [];
        }

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'x-api-key'         => $this->apiKey,
                    'anthropic-version' => $this->apiVersion,
                    'content-type'      => 'application/json',
                ])
                ->post('https://api.anthropic.com/v1/messages', [
                    'model'       => $this->model,
                    'max_tokens'  => $maxTokens,
                    'temperature' => $temperature,
                    'system'      => 'Eres un redactor editorial especializado en inteligencia artificial. Responde SIEMPRE en JSON válido, sin texto adicional fuera del objeto JSON.',
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('ClaudeService HTTP error: ' . $response->status() . ' ' . $response->body());
                return [];
            }

            $text = $response->json()['content'][0]['text'] ?? '';

            if (empty($text)) {
                Log::warning('ClaudeService: respuesta vacía.');
                return [];
            }

            // Limpiar posibles bloques ```json ... ```
            $text = preg_replace('/^```json\s*/m', '', $text);
            $text = preg_replace('/^```\s*/m', '', $text);

            $data = json_decode(trim($text), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('ClaudeService: JSON inválido. Error: ' . json_last_error_msg());
                return [];
            }

            return $data ?? [];

        } catch (\Exception $e) {
            Log::warning('ClaudeService exception: ' . $e->getMessage());
            return [];
        }
    }
}
