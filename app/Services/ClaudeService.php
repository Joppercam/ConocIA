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

            // Extraer JSON del texto (Claude suele envolver en ```json ... ```)
            $data = $this->extractJson($text);

            if ($data === null) {
                Log::warning('ClaudeService: JSON inválido. Error: ' . json_last_error_msg() . '. Raw: ' . substr($text, 0, 300));
                return [];
            }

            return $data;

        } catch (\Exception $e) {
            Log::warning('ClaudeService exception: ' . $e->getMessage());
            return [];
        }
    }

    private function extractJson(string $text): ?array
    {
        // 1. Bloque ```json ... ```
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $m)) {
            $candidate = trim($m[1]);
            $decoded = json_decode($candidate, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // Retry with sanitized control chars
            $decoded = json_decode($this->sanitizeControlChars($candidate), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // 2. Texto directo
        $trimmed = trim($text);
        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        $decoded = json_decode($this->sanitizeControlChars($trimmed), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 3. Primer { } o [ ] balanceado
        foreach (['{', '['] as $open) {
            $pos = strpos($text, $open);
            if ($pos === false) continue;
            $close = $open === '{' ? '}' : ']';
            $depth = 0; $inStr = false; $escaped = false; $end = null;
            for ($i = $pos; $i < strlen($text); $i++) {
                $c = $text[$i];
                if ($escaped)     { $escaped = false; continue; }
                if ($c === '\\')  { $escaped = true;  continue; }
                if ($c === '"')   { $inStr = !$inStr; continue; }
                if ($inStr)       continue;
                if ($c === $open) $depth++;
                if ($c === $close && --$depth === 0) { $end = $i; break; }
            }
            if ($end !== null) {
                $candidate = substr($text, $pos, $end - $pos + 1);
                $decoded = json_decode($candidate, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                // Retry with sanitized control chars
                $decoded = json_decode($this->sanitizeControlChars($candidate), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Escapa caracteres de control (0x00–0x1F) que aparezcan dentro de strings JSON.
     * Claude a veces genera HTML multilínea con newlines literales sin escapar.
     */
    private function sanitizeControlChars(string $json): string
    {
        $result   = '';
        $inString = false;
        $escaped  = false;
        $len      = strlen($json);

        for ($i = 0; $i < $len; $i++) {
            $c = $json[$i];

            if ($escaped) {
                $result  .= $c;
                $escaped  = false;
                continue;
            }

            if ($c === '\\') {
                $result  .= $c;
                $escaped  = true;
                continue;
            }

            if ($c === '"') {
                $inString = !$inString;
                $result  .= $c;
                continue;
            }

            if ($inString) {
                $ord = ord($c);
                if ($ord < 0x20) {
                    switch ($c) {
                        case "\n": $result .= '\\n';  break;
                        case "\r": $result .= '\\r';  break;
                        case "\t": $result .= '\\t';  break;
                        default:   $result .= sprintf('\\u%04x', $ord); break;
                    }
                    continue;
                }
            }

            $result .= $c;
        }

        return $result;
    }
}
