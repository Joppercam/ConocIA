<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $model;
    private string $organization;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey       = (string) (config('services.openai.api_key') ?? config('openai.api_key') ?? '');
        $this->model        = (string) (config('services.openai.model') ?? env('OPENAI_MODEL_NAME', 'gpt-4.1'));
        $this->organization = (string) (config('services.openai.organization') ?? config('openai.organization') ?? '');
        $this->timeout      = (int) config('services.openai.request_timeout', config('openai.request_timeout', 60));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateText(
        string $prompt,
        int $maxTokens = 2048,
        float $temperature = 0.7,
        ?string $systemPrompt = null
    ): string {
        if (!$this->isAvailable()) {
            Log::warning('OpenAIService: OPENAI_API_KEY no configurado.');
            return '';
        }

        try {
            $payload = [
                'model'       => $this->model,
                'temperature' => $temperature,
                'max_tokens'  => $maxTokens,
                'messages'    => array_values(array_filter([
                    $systemPrompt ? ['role' => 'system', 'content' => $systemPrompt] : null,
                    ['role' => 'user', 'content' => $prompt],
                ])),
            ];

            $response = $this->client()->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->failed()) {
                Log::error('OpenAIService generateText HTTP error: ' . $response->status() . ' ' . $response->body());
                return '';
            }

            return trim($response->json()['choices'][0]['message']['content'] ?? '');
        } catch (\Exception $e) {
            Log::error('OpenAIService generateText exception: ' . $e->getMessage());
            return '';
        }
    }

    public function generateJson(
        string $prompt,
        int $maxTokens = 4096,
        float $temperature = 0.7,
        ?string $systemPrompt = null
    ): array {
        if (!$this->isAvailable()) {
            Log::warning('OpenAIService: OPENAI_API_KEY no configurado.');
            return [];
        }

        try {
            $payload = [
                'model'           => $this->model,
                'temperature'     => $temperature,
                'max_tokens'      => $maxTokens,
                'response_format' => ['type' => 'json_object'],
                'messages'        => array_values(array_filter([
                    [
                        'role'    => 'system',
                        'content' => $systemPrompt ?: 'Responde SIEMPRE en JSON válido, sin texto fuera del JSON.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ])),
            ];

            $response = $this->client()->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->failed()) {
                Log::warning('OpenAIService generateJson HTTP error: ' . $response->status() . ' ' . $response->body());
                return [];
            }

            $text = $response->json()['choices'][0]['message']['content'] ?? '';

            if (empty($text)) {
                Log::warning('OpenAIService: respuesta vacia.');
                return [];
            }

            $data = $this->extractJson($text);

            if ($data === null) {
                Log::warning('OpenAIService: JSON invalido. Error: ' . json_last_error_msg() . '. Raw: ' . substr($text, 0, 300));
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::warning('OpenAIService generateJson exception: ' . $e->getMessage());
            return [];
        }
    }

    protected function client()
    {
        $client = Http::timeout($this->timeout)->withToken($this->apiKey);

        if (!empty($this->organization)) {
            $client = $client->withHeaders([
                'OpenAI-Organization' => $this->organization,
            ]);
        }

        return $client;
    }

    private function extractJson(string $text): ?array
    {
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $m)) {
            $candidate = trim($m[1]);
            $decoded = json_decode($candidate, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            $decoded = json_decode($this->sanitizeControlChars($candidate), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        $trimmed = trim($text);
        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        $decoded = json_decode($this->sanitizeControlChars($trimmed), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        foreach (['{', '['] as $open) {
            $pos = strpos($text, $open);
            if ($pos === false) {
                continue;
            }

            $close = $open === '{' ? '}' : ']';
            $depth = 0;
            $inStr = false;
            $escaped = false;
            $end = null;

            for ($i = $pos; $i < strlen($text); $i++) {
                $c = $text[$i];
                if ($escaped) {
                    $escaped = false;
                    continue;
                }
                if ($c === '\\') {
                    $escaped = true;
                    continue;
                }
                if ($c === '"') {
                    $inStr = !$inStr;
                    continue;
                }
                if ($inStr) {
                    continue;
                }
                if ($c === $open) {
                    $depth++;
                }
                if ($c === $close && --$depth === 0) {
                    $end = $i;
                    break;
                }
            }

            if ($end !== null) {
                $candidate = substr($text, $pos, $end - $pos + 1);
                $decoded = json_decode($candidate, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                $decoded = json_decode($this->sanitizeControlChars($candidate), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    private function sanitizeControlChars(string $json): string
    {
        $result = '';
        $inString = false;
        $escaped = false;
        $len = strlen($json);

        for ($i = 0; $i < $len; $i++) {
            $c = $json[$i];

            if ($escaped) {
                $result .= $c;
                $escaped = false;
                continue;
            }

            if ($c === '\\') {
                $result .= $c;
                $escaped = true;
                continue;
            }

            if ($c === '"') {
                $inString = !$inString;
                $result .= $c;
                continue;
            }

            if ($inString) {
                $ord = ord($c);
                if ($ord < 0x20) {
                    switch ($c) {
                        case "\n": $result .= '\\n'; break;
                        case "\r": $result .= '\\r'; break;
                        case "\t": $result .= '\\t'; break;
                        default: $result .= sprintf('\\u%04x', $ord); break;
                    }
                    continue;
                }
            }

            $result .= $c;
        }

        return $result;
    }
}
