<?php

namespace App\Console\Commands;

use App\Models\RadarRegulatorio;
use App\Models\News;
use App\Services\GeminiQuotaGuard;
use App\Services\ClaudeService;
use App\Services\OpenAIService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateRadarRegulatorio extends Command
{
    protected $signature = 'radar:generate
                            {--force : Generar aunque ya haya entrada reciente}';

    protected $description = 'Genera entradas del Radar Regulatorio de IA en Chile a partir de noticias recientes';

    /**
     * Hitos regulatorios conocidos que deben aparecer si aún no existen.
     */
    protected array $seedItems = [
        [
            'slug'         => 'politica-nacional-ia-chile-2021',
            'title'        => 'Política Nacional de Inteligencia Artificial de Chile (2021)',
            'tipo'         => 'politica',
            'estado'       => 'vigente',
            'organismo'    => 'Ministerio de Ciencia, Tecnología, Conocimiento e Innovación',
            'fecha_evento' => '2021-11-01',
            'relevancia'   => 'alta',
        ],
        [
            'slug'         => 'plan-ia-chile-2030',
            'title'        => 'Plan de Acción IA Chile 2030',
            'tipo'         => 'politica',
            'estado'       => 'vigente',
            'organismo'    => 'Ministerio de Ciencia',
            'fecha_evento' => '2023-06-01',
            'relevancia'   => 'alta',
        ],
        [
            'slug'         => 'proyecto-ley-datos-personales-chile',
            'title'        => 'Proyecto de Ley de Protección de Datos Personales (en tramitación)',
            'tipo'         => 'proyecto_ley',
            'estado'       => 'en_tramite',
            'organismo'    => 'Congreso Nacional de Chile',
            'fecha_evento' => '2022-03-01',
            'relevancia'   => 'alta',
        ],
        [
            'slug'         => 'estrategia-transformacion-digital-chile',
            'title'        => 'Estrategia de Transformación Digital del Estado',
            'tipo'         => 'politica',
            'estado'       => 'vigente',
            'organismo'    => 'Ministerio Secretaría General de Gobierno / Gobierno Digital',
            'fecha_evento' => '2022-09-01',
            'relevancia'   => 'media',
        ],
        [
            'slug'         => 'guia-uso-ia-sector-publico-chile',
            'title'        => 'Guía de Uso Responsable de IA en el Sector Público Chileno',
            'tipo'         => 'informe',
            'estado'       => 'vigente',
            'organismo'    => 'División de Gobierno Digital',
            'fecha_evento' => '2024-01-01',
            'relevancia'   => 'media',
        ],
    ];

    public function handle(): int
    {
        $guard = app(GeminiQuotaGuard::class);
        $claude = app(ClaudeService::class);

        if (!$guard->canCall('medium') && !$claude->isAvailable()) {
            $this->warn('Sin cuota disponible. ' . $guard->summary());
            return Command::FAILURE;
        }

        // Primero: asegurar que los hitos semilla existen
        $this->ensureSeedItems($guard);

        // Segundo: generar nuevas entradas desde noticias recientes de Chile
        $this->generateFromNews($guard);

        return Command::SUCCESS;
    }

    protected function ensureSeedItems(GeminiQuotaGuard $guard): void
    {
        foreach ($this->seedItems as $seed) {
            if (RadarRegulatorio::where('slug', $seed['slug'])->exists()) {
                continue;
            }

            $this->info("Generando entrada semilla: {$seed['title']}...");
            $content = $this->generateContent($seed, [], $guard);

            if (empty($content)) {
                $this->warn("  Sin contenido para: {$seed['title']}");
                continue;
            }

            RadarRegulatorio::create([
                'title'        => $seed['title'],
                'slug'         => $seed['slug'],
                'excerpt'      => $content['excerpt'] ?? Str::limit(strip_tags($content['content'] ?? ''), 220),
                'content'      => $content['content'] ?? '',
                'tipo'         => $seed['tipo'],
                'estado'       => $seed['estado'],
                'organismo'    => $seed['organismo'],
                'fecha_evento' => $seed['fecha_evento'],
                'relevancia'   => $seed['relevancia'],
                'key_actors'   => $content['key_actors'] ?? null,
                'reading_time' => $content['reading_time'] ?? 3,
                'status'       => 'published',
                'published_at' => now(),
            ]);

            $this->info("  Creada: {$seed['title']}");
            sleep(2);
        }
    }

    protected function generateFromNews(GeminiQuotaGuard $guard): void
    {
        $recentChileNews = News::published()
            ->whereHas('category', fn($q) => $q->whereIn('slug', ['ia-en-chile', 'regulacion-de-ia']))
            ->where('published_at', '>=', now()->subDays(30))
            ->latest('published_at')
            ->take(10)
            ->get();

        if ($recentChileNews->isEmpty()) {
            $this->info('Sin noticias recientes de Chile/regulación para procesar.');
            return;
        }

        foreach ($recentChileNews->take(2) as $news) {
            $slug = 'radar-' . Str::limit($news->slug ?? Str::slug($news->title), 50);

            if (RadarRegulatorio::where('slug', 'like', $slug . '%')->exists()) {
                continue;
            }

            if (!$guard->canCall('medium')) {
                break;
            }

            $this->info("Analizando noticia para radar: {$news->title}...");

            $seed = [
                'title'        => $news->title,
                'tipo'         => 'anuncio',
                'estado'       => 'en_tramite',
                'organismo'    => '',
                'fecha_evento' => $news->published_at?->toDateString() ?? now()->toDateString(),
                'relevancia'   => 'media',
            ];

            $content = $this->generateContent($seed, [$news], $guard);

            if (empty($content) || empty($content['content'])) {
                continue;
            }

            // Solo publicar si el modelo detectó relevancia regulatoria real
            if (empty($content['is_regulatory'])) {
                $this->info("  Noticia sin relevancia regulatoria directa, omitida.");
                continue;
            }

            RadarRegulatorio::create([
                'title'        => $content['title'] ?? $seed['title'],
                'slug'         => $this->uniqueSlug($slug),
                'excerpt'      => $content['excerpt'] ?? Str::limit(strip_tags($content['content']), 220),
                'content'      => $content['content'],
                'tipo'         => $content['tipo'] ?? 'anuncio',
                'estado'       => $content['estado'] ?? 'en_tramite',
                'organismo'    => $content['organismo'] ?? '',
                'fecha_evento' => $content['fecha_evento'] ?? now()->toDateString(),
                'relevancia'   => $content['relevancia'] ?? 'media',
                'key_actors'   => $content['key_actors'] ?? null,
                'reading_time' => $content['reading_time'] ?? 3,
                'status'       => 'published',
                'published_at' => now(),
            ]);

            $this->info("  Entrada de radar creada desde noticia.");
            sleep(2);
        }
    }

    protected function generateContent(array $seed, array $newsContext, GeminiQuotaGuard $guard): array
    {
        $newsText = !empty($newsContext)
            ? collect($newsContext)->map(fn($n) => "- {$n->title}: {$n->excerpt}")->implode("\n")
            : '(Entrada basada en hito regulatorio conocido — sin noticias recientes de contexto)';

        $prompt = <<<PROMPT
Eres un analista de políticas públicas y regulación de IA en Chile, con expertise en el ecosistema regulatorio chileno y latinoamericano. Escribes para ConocIA, plataforma chilena de divulgación en IA.

HITO O EVENTO A ANALIZAR:
Título: {$seed['title']}
Tipo: {$seed['tipo']}
Estado: {$seed['estado']}
Organismo: {$seed['organismo']}
Fecha: {$seed['fecha_evento']}

NOTICIAS DE CONTEXTO:
{$newsText}

Genera un análisis completo para el Radar Regulatorio de IA en Chile. Este NO es un artículo noticioso — es un análisis de seguimiento regulatorio que explica el hito, su contexto político-institucional, y sus implicancias para ciudadanos, empresas e investigadores en Chile.

ESTRUCTURA (HTML):

<h2>¿De qué se trata?</h2>
Explica el hito regulatorio en lenguaje ciudadano. Sin asumir conocimiento legal previo. 2 párrafos.

<h2>Contexto y antecedentes</h2>
¿Por qué surge? ¿Qué problema intenta resolver? Marco institucional chileno relevante. 2 párrafos.

<h2>¿Qué cambia en la práctica?</h2>
Impacto concreto: para ciudadanos, empresas, organismos del Estado, investigadores. 2-3 párrafos.

<h2>Actores involucrados</h2>
Lista <ul> con ministerios, organismos, comisiones, grupos de interés que participan o son afectados.

<h2>Comparación regional</h2>
¿Cómo se compara con regulaciones similares en la región o en Europa (EU AI Act)? 1 párrafo.

<h2>Señales a seguir</h2>
Próximos pasos esperados, plazos, indicadores de avance. 1 párrafo.

REQUISITOS:
- Mínimo 600 palabras en content.
- HTML válido: <p>, <h2>, <ul><li>.
- Excerpt: 2 oraciones ciudadanas, máx 220 caracteres.

Responde en JSON con claves:
- title (string): título revisado o mejorado del hito
- content (string HTML)
- excerpt (string)
- tipo (uno de: proyecto_ley, decreto, politica, anuncio, informe, consulta)
- estado (uno de: en_tramite, aprobado, rechazado, promulgado, vigente, en_consulta, archivado)
- organismo (string)
- fecha_evento (string YYYY-MM-DD)
- relevancia (alta, media, o baja)
- key_actors (array de {name, role})
- reading_time (int)
- is_regulatory (bool: true si este evento tiene relevancia regulatoria real para IA en Chile, false si es solo noticias generales)
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openai      = app(OpenAIService::class);

        if ($openai->isAvailable()) {
            $data = $openai->generateJson($prompt, 4000, 0.6);
            if (!empty($data['content'])) {
                Log::info('GenerateRadarRegulatorio: generado con OpenAI.');
                return $data;
            }
        }

        try {
            if (!empty($geminiKey) && $guard->canCall('medium')) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 4000, 'responseMimeType' => 'application/json'],
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
            Log::warning('GenerateRadarRegulatorio Gemini: ' . $e->getMessage());
        }

        $claude = app(ClaudeService::class);
        if ($claude->isAvailable()) {
            $data = $claude->generateJson($prompt, 4000, 0.6);
            if (!empty($data['content'])) {
                Log::info('GenerateRadarRegulatorio: generado con Claude (fallback).');
                return $data;
            }
        }

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (RadarRegulatorio::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
