<?php

namespace App\Console\Commands;

use App\Models\AnalisisFondo;
use App\Models\News;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateAnalisisFondo extends Command
{
    protected $signature = 'analisis:generate
                            {--topic= : Tema a analizar}
                            {--force : Ignorar la verificación de publicación reciente}';

    protected $description = 'Genera un análisis de fondo editorial de largo aliento sobre un tema de IA';

    /**
     * Temas rotativos — se toma el siguiente que no tenga análisis reciente.
     */
    protected array $topics = [
        ['slug' => 'carrera-frontier-models',      'name' => 'La carrera por los Frontier Models',         'category' => 'Modelos de Lenguaje',   'news_categories' => ['openai','anthropic','google-ai','microsoft-ai']],
        ['slug' => 'razonamiento-llms',             'name' => 'El problema del razonamiento en los LLMs',   'category' => 'Modelos de Lenguaje',   'news_categories' => ['nlp','inteligencia-artificial','deep-learning']],
        ['slug' => 'regulacion-ia-global',          'name' => 'Regulación de IA: el pulso global',          'category' => 'Regulación y Ética',    'news_categories' => ['regulacion-de-ia','etica-de-la-ia']],
        ['slug' => 'ia-agentes-autonomos',          'name' => 'Agentes autónomos: IA que actúa en el mundo','category' => 'Aplicaciones',          'news_categories' => ['inteligencia-artificial','automatizacion']],
        ['slug' => 'ia-generativa-industria',       'name' => 'IA generativa y su impacto en la industria', 'category' => 'Aplicaciones',          'news_categories' => ['ia-generativa','impacto-laboral','automatizacion']],
        ['slug' => 'open-source-vs-closed-ai',      'name' => 'Open source vs. IA propietaria',             'category' => 'Industria',             'news_categories' => ['inteligencia-artificial','startups-de-ia']],
        ['slug' => 'seguridad-alineamiento-ia',     'name' => 'Seguridad y alineamiento: el debate urgente','category' => 'Regulación y Ética',    'news_categories' => ['etica-de-la-ia','regulacion-de-ia','anthropic']],
        ['slug' => 'computacion-cuantica-ia',       'name' => 'Computación cuántica e IA: promesas y límites','category' => 'Tecnología',          'news_categories' => ['computacion-cuantica','inteligencia-artificial']],
        ['slug' => 'ia-salud-diagnostico',          'name' => 'IA en salud: del diagnóstico a la medicina personalizada', 'category' => 'Aplicaciones', 'news_categories' => ['ia-en-salud']],
        ['slug' => 'multimodalidad-ia',             'name' => 'La revolución multimodal: texto, imagen, audio y más', 'category' => 'Modelos de Lenguaje', 'news_categories' => ['ia-generativa','computer-vision','inteligencia-artificial']],
    ];

    public function handle(): int
    {
        $guard = app(GeminiQuotaGuard::class);

        if (!$guard->canCall('medium')) {
            $this->warn('Gemini quota insuficiente para análisis. ' . $guard->summary());
            return Command::FAILURE;
        }

        $topicConfig = $this->selectTopic();

        if (!$topicConfig) {
            $this->info('No hay temas pendientes de análisis en los próximos 14 días.');
            return Command::SUCCESS;
        }

        $this->info("Generando análisis: {$topicConfig['name']}...");

        // Obtener noticias recientes relacionadas como contexto
        $relatedNews = $this->fetchRelatedNews($topicConfig['news_categories']);

        if ($relatedNews->isEmpty()) {
            $this->warn('No hay noticias relacionadas para contextualizar el análisis. Generando sin contexto...');
        }

        $content = $this->generateAnalysis($topicConfig, $relatedNews, $guard);

        if (empty($content)) {
            $this->error('No se pudo generar el análisis.');
            return Command::FAILURE;
        }

        $slug = $this->uniqueSlug($topicConfig['slug']);

        AnalisisFondo::create([
            'title'        => $content['title'] ?? $topicConfig['name'],
            'slug'         => $slug,
            'excerpt'      => $content['excerpt'] ?? Str::limit(strip_tags($content['content']), 220),
            'content'      => $content['content'],
            'topic'        => $topicConfig['name'],
            'category'     => $topicConfig['category'],
            'key_players'  => $content['key_players'] ?? null,
            'reading_time' => $content['reading_time'] ?? max(1, (int) ceil(str_word_count(strip_tags($content['content'])) / 200)),
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $this->info("Análisis publicado: {$topicConfig['name']}");
        return Command::SUCCESS;
    }

    protected function selectTopic(): ?array
    {
        if ($topicName = $this->option('topic')) {
            return [
                'slug'            => Str::slug($topicName),
                'name'            => $topicName,
                'category'        => 'Inteligencia Artificial',
                'news_categories' => ['inteligencia-artificial'],
            ];
        }

        $force = $this->option('force');
        $cutoff = now()->subDays(14);

        foreach ($this->topics as $topic) {
            $recent = AnalisisFondo::where('topic', $topic['name'])
                ->where('published_at', '>=', $cutoff)
                ->exists();

            if (!$recent || $force) {
                return $topic;
            }
        }

        return null;
    }

    protected function fetchRelatedNews(array $categorySlugs)
    {
        return News::published()
            ->whereHas('category', fn($q) => $q->whereIn('slug', $categorySlugs))
            ->where('published_at', '>=', now()->subDays(30))
            ->latest('published_at')
            ->take(12)
            ->get();
    }

    protected function generateAnalysis(array $topicConfig, $relatedNews, GeminiQuotaGuard $guard): array
    {
        $newsContext = $relatedNews->isNotEmpty()
            ? $relatedNews->map(fn($n, $i) => ($i+1) . ". {$n->title}\n   {$n->excerpt}")->implode("\n\n")
            : '(Sin noticias recientes disponibles como contexto)';

        $prompt = <<<PROMPT
Eres un analista senior editorial especializado en inteligencia artificial, con el estilo de The Economist o MIT Technology Review en español. Tu audiencia son profesionales y entusiastas informados de la IA.

TEMA DEL ANÁLISIS: {$topicConfig['name']}
CATEGORÍA: {$topicConfig['category']}

NOTICIAS RECIENTES DE CONTEXTO (últimos 30 días):
{$newsContext}

Escribe un análisis de fondo profundo y riguroso. Este NO es un artículo de noticias — es una pieza editorial que examina el tema en toda su complejidad.

ESTRUCTURA OBLIGATORIA (HTML):

<h2>El contexto que importa</h2>
Historia y antecedentes: por qué este tema llegó donde está. No asumas que el lector ya lo sabe. 2-3 párrafos sólidos.

<h2>El estado actual</h2>
Qué está pasando ahora, con datos concretos y actores específicos. Los hechos recientes del contexto deben aparecer aquí. 3-4 párrafos.

<blockquote>La idea más reveladora del análisis — una síntesis provocadora.</blockquote>

<h2>Las tensiones del debate</h2>
Los puntos de vista enfrentados, los argumentos de cada lado. No tomes partido, presenta el debate con justicia intelectual. 3-4 párrafos.

<h2>Actores clave</h2>
¿Quiénes toman las decisiones que importan? Empresas, investigadores, reguladores, comunidades. Lista <ul> con descripciones breves.

<h2>Implicaciones para la industria y la sociedad</h2>
¿Qué significa esto para el trabajo, la economía, la privacidad, el poder? 2-3 párrafos con perspectiva amplia.

<h2>Perspectiva a futuro</h2>
¿Hacia dónde va esto? Escenarios posibles, señales a seguir, preguntas abiertas. 2 párrafos.

<h2>Para profundizar</h2>
Lista <ul> con 3 ítems. Cada uno: <strong>Recurso o ángulo</strong> — frase de por qué vale seguir explorando eso.

REQUISITOS:
- Extensión mínima: 2.000 palabras en el campo content.
- HTML válido: <p>, <h2>, <ul><li>, <blockquote>.
- No escribas "En conclusión" ni frases de cierre genéricas.
- Excerpt: 2 oraciones que generen ganas de leer, máx 220 caracteres.

Responde SOLO en JSON con claves: title, content, excerpt, key_players (array de {name, role}), reading_time (int).
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openaiKey   = env('OPENAI_API_KEY', '');

        try {
            if (!empty($geminiKey) && $guard->canCall('medium')) {
                $r = Http::timeout(90)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.72, 'maxOutputTokens' => 5000, 'responseMimeType' => 'application/json'],
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
            Log::warning('GenerateAnalisisFondo Gemini: ' . $e->getMessage());
        }

        try {
            if (!empty($openaiKey)) {
                $r = Http::timeout(90)->withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => env('OPENAI_MODEL_NAME', 'gpt-4-turbo'),
                    'temperature' => 0.72,
                    'max_tokens'  => 5000,
                    'messages'    => [
                        ['role' => 'system', 'content' => 'Responde siempre en JSON válido.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                ]);
                if ($r->successful()) {
                    $data = json_decode($r->json()['choices'][0]['message']['content'] ?? '{}', true);
                    if (!empty($data['content'])) return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('GenerateAnalisisFondo OpenAI: ' . $e->getMessage());
        }

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (AnalisisFondo::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
