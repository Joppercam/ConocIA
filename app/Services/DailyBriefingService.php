<?php

namespace App\Services;

use App\Models\DailyBriefing;
use App\Models\News;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DailyBriefingService
{
    protected string $geminiKey;
    protected string $geminiModel;

    public function __construct()
    {
        $this->geminiKey   = config('services.gemini.api_key', '');
        $this->geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * Generate (or regenerate) today's briefing.
     */
    public function generate(bool $force = false): ?DailyBriefing
    {
        $existing = DailyBriefing::today();
        if ($existing && !$force) {
            return $existing;
        }

        $news = $this->fetchTopNews();
        if ($news->isEmpty()) {
            Log::warning('DailyBriefing: no news found to generate briefing.');
            return null;
        }

        $script = $this->callClaude($news);

        if (empty($script)) {
            Log::warning('DailyBriefing: Claude devolvió vacío, revisá logs de ClaudeService.');
            $script = $this->callGemini($news);
        }

        if (empty($script)) {
            Log::info('DailyBriefing: Gemini failed, trying OpenAI fallback.');
            $script = $this->callOpenAI($news);
        }

        if (empty($script)) {
            return null;
        }

        // Estimate ~145 words per minute for Spanish speech
        $wordCount       = str_word_count($script);
        $durationSeconds = (int) round(($wordCount / 145) * 60);

        $headlines = $news->map(fn($n) => [
            'title'    => $n->title,
            'url'      => route('news.show', $n->slug),
            'category' => $n->category?->name,
            'color'    => $n->category?->color ?? '#38b6ff',
        ])->values()->toArray();

        if ($existing) {
            $existing->update([
                'script'           => $script,
                'headlines'        => $headlines,
                'duration_seconds' => $durationSeconds,
                'news_count'       => $news->count(),
                'generated_at'     => now(),
            ]);
            return $existing->fresh();
        }

        return DailyBriefing::create([
            'date'             => today()->toDateString(),
            'script'           => $script,
            'headlines'        => $headlines,
            'duration_seconds' => $durationSeconds,
            'news_count'       => $news->count(),
            'generated_at'     => now(),
        ]);
    }

    /**
     * Fetch top 5 recent published news (last 48 hours, by views).
     */
    protected function fetchTopNews()
    {
        // Intentar primero noticias recientes (últimos 7 días)
        $news = News::with('category')
            ->published()
            ->where('published_at', '>=', now()->subDays(7))
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        // Fallback: cualquier noticia publicada reciente
        if ($news->isEmpty()) {
            $news = News::with('category')
                ->published()
                ->orderByDesc('published_at')
                ->limit(5)
                ->get();
        }

        return $news;
    }

    /**
     * Build the podcast script prompt (shared by Gemini and OpenAI).
     */
    protected function buildPrompt($news): string
    {
        $today     = Carbon::today()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $newsBlock = $news->map(fn($n, $i) => sprintf(
            "NOTICIA %d:\nTítulo: %s\nCategoría: %s\nResumen: %s",
            $i + 1,
            $n->title,
            $n->category?->name ?? 'General',
            strip_tags($n->excerpt ?? $n->summary ?? mb_substr($n->content ?? '', 0, 300))
        ))->implode("\n\n");

        return <<<PROMPT
Eres el locutor de "ConocIA Briefing", un podcast diario sobre inteligencia artificial y tecnología en español.
Fecha de hoy: {$today}

Genera un guión en español, natural y fluido, para el episodio de hoy con las siguientes noticias:

{$newsBlock}

INSTRUCCIONES:
- Abre con una bienvenida cálida mencionando la fecha.
- Cubre CADA noticia con 2-3 oraciones: lo que ocurrió, por qué importa.
- Usa transiciones naturales entre noticias ("Por otro lado...", "En el mundo de...", "También hoy...").
- Cierra con una frase de despedida breve invitando a seguir leyendo en ConocIA.
- Tono: profesional pero cercano, como un podcast de calidad.
- Extensión: entre 350 y 500 palabras.
- NO uses negritas, asteriscos, markdown ni títulos. Solo texto corrido natural.
PROMPT;
    }

    /**
     * Call Claude API (primary) to generate podcast-style script.
     */
    protected function callClaude($news): string
    {
        $claude = app(ClaudeService::class);

        if (!$claude->isAvailable()) {
            Log::info('DailyBriefing: ANTHROPIC_API_KEY no configurado, saltando Claude.');
            return '';
        }

        $prompt = $this->buildPrompt($news);

        $script = $claude->generateText($prompt, 1024, 0.75);

        if (empty($script)) {
            Log::warning('DailyBriefing: Claude devolvió respuesta vacía.');
            return '';
        }

        return $script;
    }

    /**
     * Call Gemini API to generate podcast-style script.
     */
    protected function callGemini($news): string
    {
        $guard = app(GeminiQuotaGuard::class);

        if (!$guard->canCall('critical')) {
            Log::warning('DailyBriefing: ' . $guard->summary());
            return '';
        }

        $prompt = $this->buildPrompt($news);

        try {
            $response = Http::timeout(45)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.75,
                        'maxOutputTokens' => 2048,
                    ],
                ]
            );

            if ($response->failed()) {
                Log::error('DailyBriefing Gemini error: ' . $response->body());
                return '';
            }

            $guard->record();
            return trim(
                $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? ''
            );
        } catch (\Exception $e) {
            Log::error('DailyBriefing Gemini exception: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Call OpenAI API as fallback when Gemini is unavailable.
     */
    protected function callOpenAI($news): string
    {
        $apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $model  = env('OPENAI_MODEL_NAME', 'gpt-4-turbo');

        if (empty($apiKey)) {
            Log::warning('DailyBriefing: OpenAI API key not configured.');
            return '';
        }

        $prompt = $this->buildPrompt($news);

        try {
            $response = Http::timeout(45)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $model,
                    'temperature' => 0.75,
                    'max_tokens'  => 800,
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('DailyBriefing OpenAI error: ' . $response->body());
                return '';
            }

            return trim(
                $response->json()['choices'][0]['message']['content'] ?? ''
            );
        } catch (\Exception $e) {
            Log::error('DailyBriefing OpenAI exception: ' . $e->getMessage());
            return '';
        }
    }
}
