<?php

namespace App\Services;

use App\Models\DailyBriefing;
use App\Models\News;
use App\Models\ConocIaPaper;
use App\Models\ConceptoIa;
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

    public function generate(bool $force = false): ?DailyBriefing
    {
        $existing = DailyBriefing::today();
        if ($existing && !$force) {
            return $existing;
        }

        $content = $this->fetchContent();

        if ($content['news']->isEmpty()) {
            Log::warning('DailyBriefing: no news found to generate briefing.');
            return null;
        }

        $script = $this->callClaude($content);

        if (empty($script)) {
            Log::warning('DailyBriefing: Claude devolvió vacío, intentando Gemini.');
            $script = $this->callGemini($content);
        }

        if (empty($script)) {
            Log::info('DailyBriefing: Gemini failed, trying OpenAI fallback.');
            $script = $this->callOpenAI($content);
        }

        if (empty($script)) {
            return null;
        }

        $wordCount       = str_word_count($script);
        $durationSeconds = (int) round(($wordCount / 145) * 60);

        $headlines = collect()
            ->concat($content['news']->map(fn($n) => [
                'title'    => $n->title,
                'url'      => route('news.show', $n->slug),
                'category' => $n->category?->name,
                'color'    => $n->category?->color ?? '#38b6ff',
                'type'     => 'news',
            ]))
            ->concat($content['papers']->map(fn($p) => [
                'title'    => $p->title,
                'url'      => route('papers.show', $p->slug),
                'category' => 'Paper',
                'color'    => '#a78bfa',
                'type'     => 'paper',
            ]))
            ->concat($content['conceptos']->map(fn($c) => [
                'title'    => $c->title,
                'url'      => route('conceptos.show', $c->slug),
                'category' => 'Concepto',
                'color'    => '#00c896',
                'type'     => 'concepto',
            ]))
            ->values()
            ->toArray();

        $totalCount = $content['news']->count() + $content['papers']->count() + $content['conceptos']->count();

        if ($existing) {
            $existing->update([
                'script'           => $script,
                'headlines'        => $headlines,
                'duration_seconds' => $durationSeconds,
                'news_count'       => $totalCount,
                'generated_at'     => now(),
            ]);
            return $existing->fresh();
        }

        return DailyBriefing::create([
            'date'             => today()->toDateString(),
            'script'           => $script,
            'headlines'        => $headlines,
            'duration_seconds' => $durationSeconds,
            'news_count'       => $totalCount,
            'generated_at'     => now(),
        ]);
    }

    protected function fetchContent(): array
    {
        // Top 7 noticias por vistas (últimos 7 días)
        $news = News::with('category')
            ->published()
            ->where('published_at', '>=', now()->subDays(7))
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit(7)
            ->get();

        if ($news->isEmpty()) {
            $news = News::with('category')
                ->published()
                ->orderByDesc('published_at')
                ->limit(7)
                ->get();
        }

        // 2 papers más recientes
        $papers = ConocIaPaper::published()
            ->orderByDesc('published_at')
            ->limit(2)
            ->get();

        // 1 concepto destacado o más reciente
        $conceptos = ConceptoIa::published()
            ->where(fn($q) => $q->where('featured', true)->orWhereNotNull('definition'))
            ->orderByDesc('featured')
            ->orderByDesc('published_at')
            ->limit(1)
            ->get();

        return compact('news', 'papers', 'conceptos');
    }

    protected function buildPrompt(array $content): string
    {
        $today = Carbon::today()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');

        $newsBlock = $content['news']->map(fn($n, $i) => sprintf(
            "NOTICIA %d:\nTítulo: %s\nCategoría: %s\nResumen: %s",
            $i + 1,
            $n->title,
            $n->category?->name ?? 'General',
            strip_tags($n->excerpt ?? $n->summary ?? mb_substr($n->content ?? '', 0, 300))
        ))->implode("\n\n");

        $papersBlock = $content['papers']->isNotEmpty()
            ? $content['papers']->map(fn($p, $i) => sprintf(
                "PAPER %d:\nTítulo: %s\nResumen: %s",
                $i + 1,
                $p->title,
                strip_tags($p->excerpt ?? mb_substr($p->content ?? $p->original_abstract ?? '', 0, 250))
            ))->implode("\n\n")
            : null;

        $conceptoBlock = $content['conceptos']->isNotEmpty()
            ? sprintf(
                "CONCEPTO:\nNombre: %s\nDefinición: %s",
                $content['conceptos']->first()->title,
                strip_tags($content['conceptos']->first()->definition ?? $content['conceptos']->first()->excerpt ?? '')
            )
            : null;

        $sections = "=== NOTICIAS ===\n{$newsBlock}";

        if ($papersBlock) {
            $sections .= "\n\n=== PAPERS CIENTÍFICOS ===\n{$papersBlock}";
        }
        if ($conceptoBlock) {
            $sections .= "\n\n=== CONCEPTO DEL DÍA ===\n{$conceptoBlock}";
        }

        $papersInstructions = $papersBlock
            ? "- Tras las noticias, dedica 2-3 oraciones a cada paper: qué investigan y por qué importa para el campo."
            : "";
        $conceptoInstructions = $conceptoBlock
            ? "- Cierra con el Concepto del día: explícalo en 3-4 oraciones como si hablaras con alguien curioso pero sin conocimientos técnicos. Usa la frase \"El concepto de hoy es...\" para introducirlo."
            : "";

        return <<<PROMPT
Eres el locutor de "ConocIA Briefing", un podcast diario sobre inteligencia artificial en español.
Fecha de hoy: {$today}

Genera un guión en español, natural y fluido, para el episodio de hoy con el siguiente contenido:

{$sections}

INSTRUCCIONES:
- Abre con una bienvenida cálida mencionando la fecha y que hoy el briefing trae noticias, ciencia y un concepto.
- Cubre CADA noticia con 2-3 oraciones: qué ocurrió y por qué importa.
- Usa transiciones naturales entre noticias ("Por otro lado...", "En el mundo de...", "También hoy...").
{$papersInstructions}
{$conceptoInstructions}
- Cierra con una frase breve invitando a seguir leyendo en ConocIA.cl.
- Tono: profesional pero cercano, como un podcast de calidad.
- Extensión: entre 600 y 900 palabras.
- NO uses negritas, asteriscos, markdown, títulos ni numeraciones. Solo texto corrido natural.
PROMPT;
    }

    protected function callClaude(array $content): string
    {
        $claude = app(ClaudeService::class);

        if (!$claude->isAvailable()) {
            Log::info('DailyBriefing: ANTHROPIC_API_KEY no configurado, saltando Claude.');
            return '';
        }

        $script = $claude->generateText($this->buildPrompt($content), 2048, 0.75);

        if (empty($script)) {
            Log::warning('DailyBriefing: Claude devolvió respuesta vacía.');
        }

        return $script ?? '';
    }

    protected function callGemini(array $content): string
    {
        $guard = app(GeminiQuotaGuard::class);

        if (!$guard->canCall('critical')) {
            Log::warning('DailyBriefing: ' . $guard->summary());
            return '';
        }

        try {
            $response = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $this->buildPrompt($content)]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.75,
                        'maxOutputTokens' => 3000,
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

    protected function callOpenAI(array $content): string
    {
        $apiKey = config('services.openai.api_key', env('OPENAI_API_KEY', ''));
        $model  = env('OPENAI_MODEL_NAME', 'gpt-4-turbo');

        if (empty($apiKey)) {
            Log::warning('DailyBriefing: OpenAI API key not configured.');
            return '';
        }

        try {
            $response = Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $model,
                    'temperature' => 0.75,
                    'max_tokens'  => 1800,
                    'messages'    => [
                        ['role' => 'user', 'content' => $this->buildPrompt($content)],
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
