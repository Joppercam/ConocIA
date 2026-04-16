<?php

namespace App\Services;

use App\Models\Video;
use App\Services\GeminiQuotaGuard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoSummaryService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * Generate AI summary + keywords for a single video.
     */
    public function generate(Video $video, bool $force = false): bool
    {
        if (!$force && $video->hasAiSummary()) {
            return true; // already done
        }

        $result = $this->callGemini($video);
        if (!$result) {
            return false;
        }

        $video->update([
            'ai_summary'  => $result['summary'],
            'ai_keywords' => $result['keywords'],
        ]);

        return true;
    }

    protected function callGemini(Video $video): ?array
    {
        $prompt = <<<PROMPT
Analiza el siguiente video sobre inteligencia artificial y tecnología:

TÍTULO: {$video->title}
DESCRIPCIÓN: {$video->description}

Responde EXACTAMENTE en este formato JSON (sin markdown, sin explicaciones):
{
  "summary": ["punto 1 en español (máx 15 palabras)", "punto 2 en español (máx 15 palabras)", "punto 3 en español (máx 15 palabras)"],
  "keywords": ["keyword1", "keyword2", "keyword3", "keyword4", "keyword5"]
}

Los puntos del summary deben ser los 3 temas principales del video, claros y concisos.
Los keywords deben ser los 5 temas o tecnologías más relevantes mencionados.
PROMPT;

        try {
            $guard = app(GeminiQuotaGuard::class);

            if (!$guard->canCall('medium')) {
                Log::info('VideoSummaryService: ' . $guard->summary());
                return null;
            }

            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature'     => 0.3,
                        'maxOutputTokens' => 300,
                    ],
                ]
            );

            if ($response->failed()) {
                Log::error("VideoSummary Gemini error for video {$video->id}: " . $response->body());
                return null;
            }

            $guard->record();
            $text = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');

            // Strip markdown code fences if present
            $text = preg_replace('/^```json\s*/i', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);

            $data = json_decode($text, true);

            if (!isset($data['summary'], $data['keywords'])) {
                Log::warning("VideoSummary: unexpected Gemini response for video {$video->id}: {$text}");
                return null;
            }

            return [
                'summary'  => implode('|||', array_slice((array) $data['summary'], 0, 3)),
                'keywords' => array_slice((array) $data['keywords'], 0, 5),
            ];
        } catch (\Exception $e) {
            Log::error("VideoSummary exception for video {$video->id}: " . $e->getMessage());
            return null;
        }
    }
}
