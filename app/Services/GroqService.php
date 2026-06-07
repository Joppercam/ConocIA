<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para la API de Groq.
 * Groq ofrece llama3-70b gratis (14.400 req/día) sin tarjeta de crédito.
 * API compatible con el formato OpenAI: solo cambia la base URL y el modelo.
 *
 * Registro gratuito: https://console.groq.com
 */
class GroqService
{
    private const BASE_URL = 'https://api.groq.com/openai/v1/chat/completions';

    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model  = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateText(string $prompt, int $maxTokens = 2048, float $temperature = 0.7): string
    {
        if (!$this->isAvailable()) {
            return '';
        }

        try {
            $response = Http::timeout(90)
                ->withToken($this->apiKey)
                ->post(self::BASE_URL, [
                    'model'       => $this->model,
                    'temperature' => $temperature,
                    'max_tokens'  => $maxTokens,
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('GroqService generateText HTTP error: ' . $response->status() . ' ' . $response->body());
                return '';
            }

            return trim($response->json()['choices'][0]['message']['content'] ?? '');
        } catch (\Exception $e) {
            Log::warning('GroqService generateText exception: ' . $e->getMessage());
            return '';
        }
    }

    public function generateJson(string $prompt, int $maxTokens = 4096, float $temperature = 0.7): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        try {
            $response = Http::timeout(90)
                ->withToken($this->apiKey)
                ->post(self::BASE_URL, [
                    'model'           => $this->model,
                    'temperature'     => $temperature,
                    'max_tokens'      => $maxTokens,
                    'response_format' => ['type' => 'json_object'],
                    'messages'        => [
                        [
                            'role'    => 'system',
                            'content' => 'Responde SIEMPRE en JSON válido, sin texto adicional fuera del objeto JSON.',
                        ],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('GroqService generateJson HTTP error: ' . $response->status() . ' ' . $response->body());
                return [];
            }

            $text = $response->json()['choices'][0]['message']['content'] ?? '';

            if (empty($text)) {
                Log::warning('GroqService: respuesta vacía.');
                return [];
            }

            $data = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                Log::warning('GroqService: JSON inválido. Raw: ' . substr($text, 0, 200));
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::warning('GroqService exception: ' . $e->getMessage());
            return [];
        }
    }
}
