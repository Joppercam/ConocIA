<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Category;
use App\Services\GeminiQuotaGuard;
use App\Services\SimpleImageDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchNewsFromGuardian extends Command
{
    protected $signature = 'news:fetch-guardian {--limit=5 : Artículos por ejecución} {--dry-run : Mostrar sin guardar}';

    public function __construct(protected SimpleImageDownloader $imageDownloader)
    {
        parent::__construct();
    }

    protected $description = 'Importa noticias de IA desde The Guardian API (alta calidad editorial, gratis)';

    /**
     * Queries hacia The Guardian ordenadas por relevancia.
     * Cada query mapea a una categoría slug.
     */
    protected array $queries = [
        'artificial intelligence'            => 'inteligencia-artificial',
        'large language model'               => 'nlp',
        'generative AI'                      => 'ia-generativa',
        'OpenAI'                             => 'openai',
        'Google DeepMind'                    => 'google-ai',
        'Anthropic Claude'                   => 'anthropic',
        'AI regulation'                      => 'regulacion-de-ia',
        'AI jobs workforce'                  => 'impacto-laboral',
        'machine learning healthcare'        => 'ia-en-salud',
        'AI ethics bias'                     => 'etica-de-la-ia',
    ];

    public function handle(): int
    {
        $apiKey  = config('services.guardian.api_key', env('GUARDIAN_API_KEY', ''));
        $limit   = (int) $this->option('limit');
        $dryRun  = $this->option('dry-run');

        if (empty($apiKey)) {
            $this->error('GUARDIAN_API_KEY no configurada. Obtené una clave gratis en https://open-platform.theguardian.com/access/');
            return Command::FAILURE;
        }

        $total = 0;

        foreach ($this->queries as $query => $categorySlug) {
            if ($total >= $limit) break;

            $this->line("\n<fg=cyan>Query:</> \"{$query}\" → {$categorySlug}");

            $articles = $this->fetchFromGuardian($apiKey, $query, min(3, $limit - $total));

            foreach ($articles as $article) {
                if ($total >= $limit) break;

                // Evitar duplicados
                if (News::where('source_url', $article['webUrl'])->exists()) {
                    $this->line("  <fg=yellow>Duplicado:</> {$article['webTitle']}");
                    continue;
                }

                if ($dryRun) {
                    $this->line("  <fg=green>[DRY]</> {$article['webTitle']}");
                    $total++;
                    continue;
                }

                $category = $this->getOrCreateCategory($categorySlug);
                $bodyText = strip_tags($article['fields']['bodyText'] ?? $article['fields']['trailText'] ?? '');

                $enhanced = $this->enhanceWithAI([
                    'title'   => $article['webTitle'],
                    'content' => $bodyText,
                    'url'     => $article['webUrl'],
                ], $category->name);

                if (!$enhanced) {
                    $this->warn("  Sin IA disponible, omitiendo: {$article['webTitle']}");
                    continue;
                }

                $wordCount = str_word_count(strip_tags($enhanced['content']));
                if ($wordCount < 180) {
                    $this->warn("  Contenido demasiado corto ({$wordCount} palabras), omitiendo: {$article['webTitle']}");
                    continue;
                }

                $slug  = $this->uniqueSlug(Str::slug($enhanced['title']));

                $news = News::create([
                    'title'        => $enhanced['title'],
                    'slug'         => $slug,
                    'content'      => $enhanced['content'],
                    'excerpt'      => $enhanced['excerpt'] ?? Str::limit(strip_tags($enhanced['content']), 220),
                    'image'        => null,
                    'author'       => $article['fields']['byline'] ?? 'The Guardian',
                    'source'       => 'The Guardian',
                    'source_url'   => $article['webUrl'],
                    'category_id'  => $category->id,
                    'featured'     => false,
                    'status'       => 'published',
                    'is_published' => 1,
                    'reading_time' => max(1, (int) ceil($wordCount / 200)),
                    'views'        => 0,
                    'published_at' => isset($article['webPublicationDate'])
                        ? now()->parse($article['webPublicationDate'])
                        : now(),
                ]);

                // Imagen: descargar thumbnail de Guardian o buscar en Pexels
                $thumbnail    = $article['fields']['thumbnail'] ?? null;
                $imageStored  = $thumbnail ? $this->imageDownloader->download($thumbnail, $categorySlug) : null;
                if (!$imageStored) {
                    $imageStored = $this->imageDownloader->searchAndDownloadFromPexels(
                        $enhanced['title'], $categorySlug, $category->name
                    );
                }
                if ($imageStored) {
                    $news->update(['image' => $imageStored]);
                }

                $this->line("  <fg=green>✓</> {$enhanced['title']}" . ($imageStored ? ' 🖼' : ''));
                $total++;
                sleep(1);
            }
        }

        $this->newLine();
        $this->info("Total importado: {$total} artículos de The Guardian.");
        return Command::SUCCESS;
    }

    protected function fetchFromGuardian(string $apiKey, string $query, int $pageSize): array
    {
        try {
            $response = Http::timeout(15)->get('https://content.guardianapis.com/search', [
                'api-key'     => $apiKey,
                'q'           => $query,
                'section'     => 'technology',
                'order-by'    => 'newest',
                'page-size'   => $pageSize,
                'show-fields' => 'bodyText,trailText,thumbnail,byline',
            ]);

            if ($response->failed()) {
                Log::warning('FetchNewsFromGuardian: API error — ' . $response->body());
                return [];
            }

            return $response->json()['response']['results'] ?? [];
        } catch (\Exception $e) {
            Log::error('FetchNewsFromGuardian exception: ' . $e->getMessage());
            return [];
        }
    }

    protected function enhanceWithAI(array $item, string $categoryName): ?array
    {
        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openai      = app(\App\Services\OpenAIService::class);
        $guard       = app(GeminiQuotaGuard::class);

        $prompt = <<<PROMPT
Eres un periodista senior especializado en inteligencia artificial y tecnología, con estilo de referencia de MIT Technology Review o Wired en español. El artículo original está en inglés; tradúcelo y reescríbelo para una audiencia hispanohablante.

FUENTE ORIGINAL (The Guardian):
Título: {$item['title']}
Contenido: {$item['content']}

Tu misión es transformar este material en un artículo de largo aliento que enganche desde la primera línea, explique el contexto con profundidad y motive al lector a seguir explorando el tema.

ESTRUCTURA OBLIGATORIA (sigue este orden):

1. TÍTULO: En español, atractivo, no una traducción literal. Puede usar pregunta, dato impactante o contraste que genere intriga.

2. APERTURA (primer párrafo, sin <h2>): Un gancho poderoso — dato sorprendente, escenario concreto o pregunta que interpele al lector. No puede dejar de leer tras este párrafo.

3. DESARROLLO (3 a 4 secciones con <h2>): Cada sección con 2-3 párrafos. Usa los datos y nombres del original; añade contexto de {$categoryName} cuando aporte valor real.

4. CITA DESTACADA: Al menos un <blockquote> con la idea más significativa o reveladora del artículo.

5. CONTEXTO CLAVE (sección <h2>Contexto clave</h2>): Explica 2-3 conceptos técnicos que el lector necesita para entender plenamente la noticia. Lenguaje accesible sin perder rigor.

6. PARA PROFUNDIZAR (cierre obligatorio, sección <h2>Para profundizar</h2>): Lista <ul> con 3 ítems. Cada uno propone un ángulo relacionado, una pregunta abierta o un área de investigación que amplía la noticia. Formato: <strong>Tema</strong> — 1-2 oraciones que explican la conexión y despiertan curiosidad. Sin URLs externas.

REQUISITOS TÉCNICOS:
- Extensión mínima: 1.200 palabras en el campo content.
- HTML válido: <p> para párrafos, <h2> para subtítulos, <ul><li> para listas, <blockquote> para cita.
- Excerpt: 2 oraciones en español que capturan la esencia y generan curiosidad. Máximo 220 caracteres.
- No menciones que el artículo fue traducido ni reescrito.

        Responde SOLO en JSON con estas claves: title, content, excerpt.
PROMPT;

        try {
            if ($openai->isAvailable()) {
                $data = $openai->generateJson($prompt, 3500, 0.7);
                if (!empty($data['title']) && !empty($data['content'])) {
                    return $data;
                }
            }
        } catch (\Exception) {}

        try {
            if (!empty($geminiKey) && $guard->canCall('high')) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4096, 'responseMimeType' => 'application/json'],
                    ]
                );
                if ($r->successful()) {
                    $data = json_decode($r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}', true);
                    if (!empty($data['title']) && !empty($data['content'])) {
                        $guard->record();
                        return $data;
                    }
                }
            }
        } catch (\Exception) {}

        return null;
    }

    protected function getOrCreateCategory(string $slug): Category
    {
        static $cache = [];
        if (isset($cache[$slug])) return $cache[$slug];

        $names = [
            'inteligencia-artificial' => 'Inteligencia Artificial', 'nlp' => 'NLP',
            'ia-generativa' => 'IA Generativa', 'openai' => 'OpenAI',
            'google-ai' => 'Google AI', 'anthropic' => 'Anthropic',
            'regulacion-de-ia' => 'Regulación de IA', 'impacto-laboral' => 'Impacto Laboral',
            'ia-en-salud' => 'IA en Salud', 'etica-de-la-ia' => 'Ética de la IA',
        ];

        $cat = Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $names[$slug] ?? Str::title(str_replace('-', ' ', $slug)), 'description' => '']
        );

        $cache[$slug] = $cat;
        return $cat;
    }

    protected function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $i = 1;
        while (News::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    protected function defaultImage(string $categorySlug): ?string
    {
        return null;
    }
}
