<?php

namespace App\Services;

use App\Models\DailyBriefing;
use App\Models\News;
use App\Models\ConocIaPaper;
use App\Models\ConceptoIa;
use App\Services\BriefingAudioService;
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
        $this->geminiKey   = (string) config('services.gemini.api_key', '');
        $this->geminiModel = (string) config('services.gemini.model', 'gemini-2.0-flash');
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

        // Intentar con IA solo si hay claves configuradas; siempre hay fallback con Google TTS.
        $script = '';

        if (app(ClaudeService::class)->isAvailable()) {
            $script = $this->callClaude($content);
        }

        if (empty($script) && !empty($this->geminiKey)) {
            $script = $this->callGemini($content);
        }

        if (empty($script) && app(OpenAIService::class)->isAvailable()) {
            $script = $this->callOpenAI($content);
        }

        // Fallback garantizado: construir guion desde titulares + summaries (Google TTS directo)
        if (empty($script)) {
            Log::info('DailyBriefing: usando fallback directo desde noticias (Google TTS).');
            $script = $this->buildFallbackScript($content);
        }

        if (empty($script)) {
            Log::error('DailyBriefing: sin contenido suficiente para generar briefing.');
            return null;
        }

        Log::info('DailyBriefing: script generado correctamente (' . str_word_count($script) . ' palabras).');

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

        $audio = app(BriefingAudioService::class);

        if ($existing) {
            $existing->update([
                'script'           => $script,
                'headlines'        => $headlines,
                'duration_seconds' => $durationSeconds,
                'news_count'       => $totalCount,
                'generated_at'     => now(),
                'audio_url'        => null,
            ]);
            $existing = $existing->fresh();
            $audioResult = $audio->generate($existing);
            if ($audioResult !== true) {
                Log::warning('BriefingAudio (update): ' . $audioResult);
            }
            return $existing;
        }

        $briefing = DailyBriefing::create([
            'date'             => today()->toDateString(),
            'script'           => $script,
            'headlines'        => $headlines,
            'duration_seconds' => $durationSeconds,
            'news_count'       => $totalCount,
            'generated_at'     => now(),
        ]);

        $audioResult = $audio->generate($briefing);
        if ($audioResult !== true) {
            Log::warning('BriefingAudio (create): ' . $audioResult);
        }

        return $briefing->fresh();
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

        return $openai->generateText($this->buildPrompt($content), 2500, 0.75);
    }

    /**
     * Fallback: construye el guion directamente desde el contenido de noticias,
     * sin necesitar ningún proveedor de IA externo. Mismo enfoque que PodcastService.
     */
    protected function buildFallbackScript(array $content): string
    {
        $today = Carbon::today()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $lines = [];

        $lines[] = "Bienvenidos al briefing de ConocIA del {$today}.";
        $lines[] = "Hoy les traigo las noticias más importantes del mundo de la inteligencia artificial.";
        $lines[] = '';

        foreach ($content['news'] as $i => $news) {
            $summary = strip_tags($news->excerpt ?? $news->summary ?? mb_substr($news->content ?? '', 0, 300));
            $summary = html_entity_decode(preg_replace('/\s+/', ' ', trim($summary)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($i === 0) {
                $lines[] = "La noticia principal de hoy: {$news->title}.";
                if ($summary) $lines[] = $summary;
            } else {
                $lines[] = "Además, {$news->title}.";
                if ($summary) $lines[] = mb_substr($summary, 0, 200) . '.';
            }
            $lines[] = '';
        }

        if ($content['papers']->isNotEmpty()) {
            $paper = $content['papers']->first();
            $paperSummary = strip_tags($paper->excerpt ?? mb_substr($paper->content ?? $paper->original_abstract ?? '', 0, 200));
            $lines[] = "En cuanto a investigación, destacamos el paper: {$paper->title}. {$paperSummary}";
            $lines[] = '';
        }

        if ($content['conceptos']->isNotEmpty()) {
            $concepto = $content['conceptos']->first();
            $def = strip_tags($concepto->definition ?? $concepto->excerpt ?? '');
            if ($def) {
                $lines[] = "Y el concepto del día: {$concepto->title}. {$def}";
                $lines[] = '';
            }
        }

        $lines[] = "Eso es todo por hoy. Seguí leyendo en ConocIA punto cl.";

        $script = implode(' ', array_filter($lines, fn($l) => $l !== ''));

        return mb_strlen($script) > 100 ? $script : '';
    }
}
