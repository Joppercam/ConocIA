<?php

namespace App\Console\Commands;

use App\Models\EstadoArte;
use App\Models\News;
use App\Services\GeminiQuotaGuard;
use App\Services\ClaudeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateEstadoArte extends Command
{
    protected $signature = 'digest:generate
                            {--subfield= : Slug del subcampo (ej: computer-vision)}
                            {--all : Generar para todos los subcampos configurados}
                            {--force : Sobreescribir el digest de esta semana si ya existe}';

    protected $description = 'Genera el digest semanal "Estado del Arte" por subcampo de IA';

    protected array $subfields = [
        'ia-generativa'    => [
            'label'           => 'IA Generativa',
            'news_categories' => ['ia-generativa', 'openai', 'anthropic', 'google-ai', 'microsoft-ai'],
        ],
        'machine-learning' => [
            'label'           => 'Machine Learning',
            'news_categories' => ['machine-learning', 'deep-learning', 'inteligencia-artificial'],
        ],
        'nlp'              => [
            'label'           => 'Procesamiento del Lenguaje Natural',
            'news_categories' => ['nlp', 'inteligencia-artificial'],
        ],
        'computer-vision'  => [
            'label'           => 'Computer Vision',
            'news_categories' => ['computer-vision', 'inteligencia-artificial'],
        ],
        'regulacion-ia'    => [
            'label'           => 'Regulación de IA',
            'news_categories' => ['regulacion-de-ia', 'etica-de-la-ia', 'privacidad-y-seguridad'],
        ],
        'impacto-laboral'  => [
            'label'           => 'IA y el Futuro del Trabajo',
            'news_categories' => ['impacto-laboral', 'automatizacion'],
        ],
    ];

    public function handle(): int
    {
        $guard = app(GeminiQuotaGuard::class);

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd   = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $subfieldSlug = $this->option('subfield');
        $generateAll  = $this->option('all');

        if (!$subfieldSlug && !$generateAll) {
            $this->error('Usa --subfield=<slug> o --all.');
            return Command::FAILURE;
        }

        $targets = $generateAll
            ? array_keys($this->subfields)
            : [$subfieldSlug];

        foreach ($targets as $slug) {
            if (!isset($this->subfields[$slug])) {
                $this->warn("Subcampo desconocido: {$slug}. Ignorando.");
                continue;
            }

            if (!$guard->canCall('medium')) {
                $this->warn('Gemini quota insuficiente. ' . $guard->summary());
                break;
            }

            $this->generateForSubfield($slug, $weekStart, $weekEnd, $guard);
        }

        return Command::SUCCESS;
    }

    protected function generateForSubfield(string $subfieldSlug, string $weekStart, string $weekEnd, GeminiQuotaGuard $guard): void
    {
        $config = $this->subfields[$subfieldSlug];
        $label  = $config['label'];

        // Verificar si ya existe este digest
        $existing = EstadoArte::where('subfield', $subfieldSlug)
            ->where('week_start', $weekStart)
            ->first();

        if ($existing && !$this->option('force')) {
            $this->line("  Ya existe digest de {$label} para esta semana. Usa --force para regenerar.");
            return;
        }

        $this->info("Generando Estado del Arte: {$label}...");

        // Obtener noticias de la semana para este subcampo
        $news = News::published()
            ->whereHas('category', fn($q) => $q->whereIn('slug', $config['news_categories']))
            ->where('published_at', '>=', $weekStart)
            ->where('published_at', '<=', $weekEnd . ' 23:59:59')
            ->latest('published_at')
            ->take(15)
            ->get();

        if ($news->count() < 3) {
            $this->warn("  Solo {$news->count()} noticias para {$label} esta semana. Mínimo 3 requeridas.");
            return;
        }

        $this->line("  Usando {$news->count()} noticias como fuente...");

        $content = $this->generateDigest($subfieldSlug, $label, $news, $weekStart, $weekEnd, $guard);

        if (empty($content)) {
            $this->error("  No se pudo generar el digest para {$label}.");
            return;
        }

        $weekStartCarbon = Carbon::parse($weekStart);
        $weekEndCarbon   = Carbon::parse($weekEnd);
        $periodLabel     = 'Semana del ' . $weekStartCarbon->locale('es')->isoFormat('D [de] MMMM')
                         . ' al ' . $weekEndCarbon->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

        $title = "{$label} — {$periodLabel}";
        $slug  = $this->uniqueSlug(Str::slug("{$subfieldSlug}-" . $weekStartCarbon->format('Y-m-d')));

        $attributes = [
            'title'             => $content['title'] ?? $title,
            'slug'              => $slug,
            'subfield'          => $subfieldSlug,
            'subfield_label'    => $label,
            'period_label'      => $periodLabel,
            'week_start'        => $weekStart,
            'week_end'          => $weekEnd,
            'excerpt'           => $content['excerpt'] ?? Str::limit(strip_tags($content['content']), 220),
            'content'           => $content['content'],
            'source_news_ids'   => $news->pluck('id')->toArray(),
            'key_developments'  => $content['key_developments'] ?? null,
            'reading_time'      => $content['reading_time'] ?? max(1, (int) ceil(str_word_count(strip_tags($content['content'])) / 200)),
            'status'            => 'published',
            'published_at'      => now(),
        ];

        if ($existing) {
            $existing->update($attributes);
        } else {
            EstadoArte::create($attributes);
        }

        $this->info("  Digest publicado: {$label}");
    }

    protected function generateDigest(string $subfieldSlug, string $label, $news, string $weekStart, string $weekEnd, GeminiQuotaGuard $guard): array
    {
        $newsBlock = $news->map(fn($n, $i) =>
            ($i+1) . ". **{$n->title}**\n   {$n->excerpt}"
        )->implode("\n\n");

        $startLabel = Carbon::parse($weekStart)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $endLabel   = Carbon::parse($weekEnd)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

        $prompt = <<<PROMPT
Eres el editor de "Estado del Arte", una sección semanal de ConocIA que sintetiza lo más relevante de un campo de la IA para lectores informados.

CAMPO: {$label}
PERÍODO: Semana del {$startLabel} al {$endLabel}

NOTICIAS DE LA SEMANA:
{$newsBlock}

Genera un digest editorial que sintetice lo que pasó en {$label} esta semana. No es un resumen de cada noticia — es una lectura editorial que encuentra los hilos conductores, las tensiones y las tendencias que emergen del conjunto.

ESTRUCTURA OBLIGATORIA (HTML):

<h2>Resumen de la semana</h2>
2-3 párrafos que capturen lo esencial: ¿qué movió el campo esta semana? ¿Hubo un tema dominante o varias historias paralelas? Escribe con perspectiva editorial, no como lista de eventos.

<h2>Desarrollos destacados</h2>
Lista <ul> de 4-6 ítems con los desarrollos más importantes. Cada ítem: <strong>Título corto</strong> — 1-2 oraciones explicando el desarrollo y su relevancia.

<h2>Tendencias emergentes</h2>
¿Qué patrones se observan? ¿Qué conversación está ganando fuerza? 2 párrafos con visión más amplia que los hechos individuales.

<blockquote>La observación más penetrante de la semana, como si fuera un tweet de un experto del campo.</blockquote>

<h2>Lo que viene</h2>
Señales a seguir, anuncios esperados, preguntas abiertas que la semana dejó sin responder. 1-2 párrafos.

<h2>Para explorar más</h2>
Lista <ul> de 2-3 ítems con ángulos o conceptos relacionados que el lector puede investigar. Formato: <strong>Tema</strong> — por qué es relevante ahora.

REQUISITOS:
- Extensión mínima: 700 palabras.
- HTML válido: <p>, <h2>, <ul><li>, <blockquote>.
- title: título editorial para el digest (ej: "IA Generativa: la semana en que todo se aceleró").
- excerpt: 2 oraciones que capturen el espíritu de la semana, máx 220 caracteres.
- key_developments: array de 4-6 strings cortos (titulares de los desarrollos destacados).
- reading_time: entero en minutos.

Responde SOLO en JSON con claves: title, content, excerpt, key_developments, reading_time.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        // ── Primario: Gemini 2.0 Flash ────────────────────────────────────────
        try {
            if (!empty($geminiKey) && $guard->canCall('medium')) {
                $r = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.72, 'maxOutputTokens' => 4000, 'responseMimeType' => 'application/json'],
                    ]
                );
                if ($r->successful()) {
                    $data = json_decode($r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}', true);
                    if (!empty($data['content'])) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('GenerateEstadoArte Gemini: ' . $e->getMessage());
        }

        // ── Fallback: Claude 3.5 Sonnet ───────────────────────────────────────
        $claude = app(ClaudeService::class);
        if ($claude->isAvailable()) {
            $data = $claude->generateJson($prompt, 4000, 0.72);
            if (!empty($data['content'])) {
                Log::info('GenerateEstadoArte: generado con Claude (fallback).');
                return $data;
            }
        }

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (EstadoArte::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
