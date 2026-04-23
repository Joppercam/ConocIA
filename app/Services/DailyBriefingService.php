<?php

namespace App\Services;

use App\Models\DailyBriefing;
use App\Models\News;
use App\Models\ConocIaPaper;
use App\Models\ConceptoIa;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use App\Services\OpenAIService;
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

        $script = $this->callOpenAI($content);

        if (empty($script)) {
            Log::warning('DailyBriefing: OpenAI devolvio vacio, intentando Gemini.');
            $script = $this->callGemini($content);
        }

        if (empty($script)) {
            Log::info('DailyBriefing: Gemini failed, trying Claude fallback.');
            $script = $this->callClaude($content);
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
        $today    = Carbon::today()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $mainNews = $content['news']->first();
        $restNews = $content['news']->skip(1);

        $mainBlock = $mainNews ? sprintf(
            "NOTICIA PRINCIPAL:\nTítulo: %s\nCategoría: %s\nResumen: %s",
            $mainNews->title,
            $mainNews->category?->name ?? 'General',
            strip_tags($mainNews->excerpt ?? $mainNews->summary ?? mb_substr($mainNews->content ?? '', 0, 400))
        ) : '';

        $restBlock = $restNews->map(fn($n, $i) => sprintf(
            "NOTICIA %d:\nTítulo: %s\nCategoría: %s\nResumen: %s",
            $i + 2,
            $n->title,
            $n->category?->name ?? 'General',
            strip_tags($n->excerpt ?? $n->summary ?? mb_substr($n->content ?? '', 0, 250))
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
                "CONCEPTO DEL DÍA:\nNombre: %s\nDefinición: %s",
                $content['conceptos']->first()->title,
                strip_tags($content['conceptos']->first()->definition ?? $content['conceptos']->first()->excerpt ?? '')
            )
            : null;

        $sections = $mainBlock;
        if ($restBlock) $sections .= "\n\n=== RESTO DE NOTICIAS ===\n{$restBlock}";
        if ($papersBlock) $sections .= "\n\n=== CIENCIA Y PAPERS ===\n{$papersBlock}";
        if ($conceptoBlock) $sections .= "\n\n=== CONCEPTO ===\n{$conceptoBlock}";

        $papersInstructions = $papersBlock
            ? "- CIENCIA: dedica un párrafo a los papers. Explica qué investigan y por qué podrían cambiar algo concreto. Nada de jerga — habla como si le contaras a un colega inteligente pero no especialista."
            : "";

        $conceptoInstructions = $conceptoBlock
            ? "- CONCEPTO DEL DÍA: cierra con este bloque. Introdúcelo con algo como \"Antes de cerrar, hoy quiero explicarte qué es...\". Explícalo en 3-4 oraciones usando una analogía del mundo real. Que quien nunca oyó el término lo entienda y lo recuerde."
            : "";

        return <<<PROMPT
Eres Alex, el analista de ConocIA Briefing — el resumen diario de inteligencia artificial de ConocIA.cl. Eres hombre.

Tu voz es directa, analítica y sin relleno. No eres un locutor de radio entusiasta ni un académico distante. Eres alguien que lee todo lo que pasa en IA, lo filtra con criterio y le cuenta a tu audiencia lo que realmente importa — y por qué. Hablas con convicción, usas ironía cuando corresponde, y nunca dices "es un placer estar aquí" ni frases de relleno. Cuando uses adjetivos o participios que refieran a ti mismo, usa género masculino.

Fecha: {$today}

CONTENIDO DEL DÍA:
{$sections}

ESTRUCTURA DEL GUIÓN:

1. APERTURA — No empieces con la fecha ni con bienvenidas genéricas. Arranca directamente con el dato, la pregunta o la tensión más interesante del día. Una o dos oraciones que hagan que el oyente quiera escuchar el resto. Algo como: "Hoy Google movió ficha..." o "Hay una pregunta que recorre la industria esta semana..." o "Si creías que los modelos de lenguaje ya habían tocado techo, hoy hay razones para dudar de eso."

2. NOTICIA PRINCIPAL — Desarróllala con profundidad: qué ocurrió, quiénes son los actores clave, qué implica para la industria o para los usuarios. 4-5 oraciones. No resumas — analiza.

3. RESTO DE NOTICIAS — Cubre cada una con 2-3 oraciones. Prioriza lo que tiene consecuencias reales sobre lo que es solo anuncio. Usa transiciones que conecten temáticamente cuando sea posible ("Relacionado con esto...", "En la misma semana en que...", "Mientras tanto, en el frente de..."). Evita transiciones mecánicas como "También hoy..." o "Por otro lado..." repetidas.

{$papersInstructions}
{$conceptoInstructions}

4. CIERRE — Una o dos oraciones que dejen al oyente con algo en qué pensar, no una despedida corporativa. Puede ser una pregunta abierta, una tensión sin resolver, o una predicción. Termina con: "Seguí leyendo en ConocIA.cl."

REGLAS:
- Texto corrido, sin títulos, sin asteriscos, sin markdown, sin numeraciones.
- Entre 700 y 950 palabras. Ni más ni menos — el briefing tiene que caber en menos de 7 minutos.
- Nunca menciones que eres una IA ni que generaste este texto.
- No repitas el título de las noticias textualmente — parafraséalas con criterio editorial.
PROMPT;
    }

    protected function callClaude(array $content): string
    {
        $claude = app(ClaudeService::class);

        if (!$claude->isAvailable()) {
            Log::info('DailyBriefing: ANTHROPIC_API_KEY no configurado, saltando Claude.');
            return '';
        }

        $script = $claude->generateText($this->buildPrompt($content), 3500, 0.78);

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
                        'maxOutputTokens' => 4000,
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
        $openai = app(OpenAIService::class);

        if (!$openai->isAvailable()) {
            Log::warning('DailyBriefing: OpenAI API key not configured.');
            return '';
        }

        return $openai->generateText($this->buildPrompt($content), 1800, 0.75);
    }
}
