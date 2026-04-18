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

class FetchNewsFromRss extends Command
{
    protected $signature = 'news:fetch-rss {--limit=3 : Máximo de artículos por feed} {--dry-run : Mostrar lo que se importaría sin guardar}';

    protected $description = 'Importa noticias de feeds RSS curados de alta calidad (Xataka, Hipertextual, VentureBeat, The Verge)';

    /**
     * Feeds RSS configurados con sus metadatos.
     * 'ai_only' => true: solo se importan si el título/descripción contiene keywords de IA.
     * 'ai_only' => false: el feed ya es 100% IA/tech (categoría explícita).
     */
    protected array $feeds = [
        // ── Blogs oficiales (100% IA, máxima calidad) ──────────────────────
        [
            'url'      => 'https://openai.com/news/rss.xml',
            'source'   => 'OpenAI',
            'language' => 'en',
            'ai_only'  => false,
            'category' => 'openai',         // forzar categoría sin auto-detectar
        ],
        [
            'url'      => 'https://deepmind.google/blog/rss.xml',
            'source'   => 'Google DeepMind',
            'language' => 'en',
            'ai_only'  => false,
            'category' => 'google-ai',
        ],
        [
            'url'      => 'https://blogs.microsoft.com/feed/',
            'source'   => 'Microsoft Blog',
            'language' => 'en',
            'ai_only'  => true,             // el blog cubre más temas, filtrar por IA
            'category' => 'microsoft-ai',
        ],
        [
            'url'      => 'https://blog.google/technology/ai/rss/',
            'source'   => 'Google AI Blog',
            'language' => 'en',
            'ai_only'  => false,
            'category' => 'google-ai',
        ],
        // ── Medios especializados en IA ─────────────────────────────────────
        [
            'url'      => 'https://venturebeat.com/category/ai/feed/',
            'source'   => 'VentureBeat AI',
            'language' => 'en',
            'ai_only'  => false,
        ],
        [
            'url'      => 'https://techcrunch.com/category/artificial-intelligence/feed/',
            'source'   => 'TechCrunch AI',
            'language' => 'en',
            'ai_only'  => false,
        ],
        [
            'url'      => 'https://www.theverge.com/ai-artificial-intelligence/rss/index.xml',
            'source'   => 'The Verge AI',
            'language' => 'en',
            'ai_only'  => false,
        ],
        // ── Medios en español (filtro IA activo) ───────────────────────────
        [
            'url'      => 'https://www.xataka.com/feed',
            'source'   => 'Xataka',
            'language' => 'es',
            'ai_only'  => true,
        ],
        [
            'url'      => 'https://hipertextual.com/feed',
            'source'   => 'Hipertextual',
            'language' => 'es',
            'ai_only'  => true,
        ],
    ];

    /**
     * Keywords para filtrar artículos relevantes en feeds generales.
     */
    protected array $aiKeywords = [
        'inteligencia artificial', 'artificial intelligence', 'machine learning',
        'deep learning', 'large language model', 'llm', 'chatgpt', 'gpt-',
        'gemini', 'claude', 'llama', 'openai', 'anthropic', 'deepmind',
        'generative ai', 'ia generativa', 'neural network', 'transformer',
        'ai model', 'modelo ia', 'copilot', 'diffusion', 'autonomous ai',
        'ai agent', 'agente ia', 'foundation model', 'ai regulation', 'ley ia',
    ];

    /**
     * Mapa de keywords → categoría slug para auto-categorización.
     */
    protected array $categoryMap = [
        'anthropic|claude\b'                                       => 'anthropic',  // primero: evita falsos positivos con otros patrones
        'openai|chatgpt|gpt-|sora|dall-e'                         => 'openai',
        'deepmind|gemini|google ai|google bard|notebooklm'        => 'google-ai',
        'microsoft|copilot|azure ai|phi-'                         => 'microsoft-ai',
        'meta ai|llama|meta\b'                                     => 'meta-ai',
        'amazon|bedrock|aws ai|sagemaker'                         => 'amazon-ai',
        'startup|funding|inversión|serie a|ronda'                  => 'startups-de-ia',
        'generative|generativa|text-to-|imagen|diffusion|sora'    => 'ia-generativa',
        'robot|robótica|humanoid'                                  => 'robotica',
        'quantum|cuántic'                                          => 'computacion-cuantica',
        'salud|health|medicina|diagnóstico|drug'                   => 'ia-en-salud',
        'finanz|finance|banking|trading|fintech'                   => 'ia-en-finanzas',
        'educac|education|aprendizaje|tutor'                       => 'ia-en-educacion',
        'ética|ethics|bias|sesgo|responsable|alignment'            => 'etica-de-la-ia',
        'regulac|regulation|ai act|legislac|govern'                => 'regulacion-de-ia',
        'empleo|trabajo|jobs|workforce|laboral'                    => 'impacto-laboral',
        'privacidad|privacy|deepfake|seguridad|security|jailbreak' => 'privacidad-y-seguridad',
        'nlp|lenguaje natural|language model|llm|bert|gpt'        => 'nlp',
        'visión|vision|imagen|image recognition|ocr'              => 'computer-vision',
        'machine learning|aprendizaje automático|ml\b'            => 'machine-learning',
        'deep learning|redes neuronales|neural'                    => 'deep-learning',
    ];

    protected SimpleImageDownloader $imageDownloader;

    public function __construct(SimpleImageDownloader $imageDownloader)
    {
        parent::__construct();
        $this->imageDownloader = $imageDownloader;
    }

    public function handle(): int
    {
        $limit   = (int) $this->option('limit');
        $dryRun  = $this->option('dry-run');
        $total   = 0;
        $skipped = 0;

        $this->info('Iniciando importación desde feeds RSS...');

        foreach ($this->feeds as $feed) {
            $this->line("\n<fg=cyan>Feed:</> {$feed['source']} ({$feed['url']})");

            $items = $this->parseFeed($feed['url']);
            if (empty($items)) {
                $this->warn("  Sin artículos o feed inaccesible.");
                continue;
            }

            $imported = 0;
            foreach ($items as $item) {
                if ($imported >= $limit) break;

                $title   = $this->cleanText($item['title'] ?? '');
                $desc    = $this->cleanText($item['description'] ?? '');
                $url     = trim($item['link'] ?? '');
                $image   = $item['image'] ?? null;
                $pubDate = $item['pubDate'] ?? null;

                if (empty($title) || empty($url)) continue;

                // Filtrar por relevancia IA (feeds mixtos Y validación final para todos)
                if ($feed['ai_only'] && !$this->isAiRelated($title . ' ' . $desc)) {
                    $skipped++;
                    $this->line("  <fg=yellow>Filtrado (no-IA):</> {$title}");
                    continue;
                }

                // Validación de calidad mínima para todos los feeds
                if (!$this->passesQualityFilter($title, $desc)) {
                    $skipped++;
                    $this->line("  <fg=yellow>Filtrado (calidad):</> {$title}");
                    continue;
                }

                // Evitar duplicados por URL de fuente
                if (News::where('source_url', $url)->exists()) {
                    $this->line("  <fg=yellow>Duplicado:</> {$title}");
                    continue;
                }

                // Usar categoría forzada del feed o auto-detectar
                $categorySlug = $feed['category'] ?? $this->detectCategory($title . ' ' . $desc);
                $category     = $this->getOrCreateCategory($categorySlug);

                if ($dryRun) {
                    $this->line("  <fg=green>[DRY]</> [{$categorySlug}] {$title}");
                    $imported++;
                    $total++;
                    continue;
                }

                // Mejorar contenido con IA
                $enhanced = $this->enhanceWithAI([
                    'title'      => $title,
                    'content'    => $desc,
                    'url'        => $url,
                    'language'   => $feed['language'],
                ], $category->name);

                if (!$enhanced) {
                    if (($feed['language'] ?? 'es') === 'en') {
                        $this->warn("  Sin IA disponible, omitiendo artículo en inglés: {$title}");
                        continue;
                    }
                    $enhanced = [
                        'title'   => $title,
                        'content' => "<p>{$desc}</p>",
                        'excerpt' => Str::limit($desc, 220),
                    ];
                }

                $slug = $this->uniqueSlug(Str::slug($enhanced['title']));

                $news = News::create([
                    'title'        => $enhanced['title'],
                    'slug'         => $slug,
                    'content'      => $enhanced['content'],
                    'excerpt'      => $enhanced['excerpt'],
                    'image'        => $this->defaultImage($categorySlug),
                    'author'       => $feed['source'],
                    'source'       => $feed['source'],
                    'source_url'   => $url,
                    'category_id'  => $category->id,
                    'featured'     => false,
                    'status'       => 'published',
                    'is_published' => 1,
                    'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($enhanced['content'])) / 200)),
                    'views'        => 0,
                    'published_at' => $pubDate ? now()->parse($pubDate) : now(),
                ]);

                // Descargar imagen si viene en el feed
                if ($image) {
                    $local = $this->imageDownloader->downloadMultiple(
                        [$image => ['categorySlug' => $categorySlug, 'newsId' => $news->id]]
                    );
                    if (!empty($local[$image])) {
                        $news->update(['image' => $local[$image]]);
                    }
                }

                $this->line("  <fg=green>✓</> [{$categorySlug}] {$news->title}");
                $imported++;
                $total++;

                sleep(1); // Respetar rate limit de Gemini
            }

            $this->line("  Importados: {$imported} artículos");
        }

        $this->newLine();
        $this->info("Total importado: {$total} noticias. Filtrados por no-IA: {$skipped}.");

        return Command::SUCCESS;
    }

    /**
     * Parsea un feed RSS/Atom y devuelve array de items.
     */
    protected function parseFeed(string $url): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'ConocIA RSS Reader/1.0'])
                ->get($url);

            if ($response->failed()) return [];

            $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$xml) return [];

            $items = [];

            // RSS 2.0
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    $items[] = $this->parseRssItem($item);
                }
            }
            // Atom
            elseif (isset($xml->entry)) {
                foreach ($xml->entry as $entry) {
                    $items[] = $this->parseAtomEntry($entry);
                }
            }

            return $items;
        } catch (\Exception $e) {
            Log::warning("FetchNewsFromRss: error parsing {$url}: " . $e->getMessage());
            return [];
        }
    }

    protected function parseRssItem(\SimpleXMLElement $item): array
    {
        // Imagen: media:content, enclosure o img en descripción
        $image = null;
        $media = $item->children('media', true);
        if (isset($media->content)) {
            $image = (string) $media->content->attributes()->url;
        }
        if (!$image && isset($item->enclosure)) {
            $type = (string) $item->enclosure->attributes()->type;
            if (str_starts_with($type, 'image/')) {
                $image = (string) $item->enclosure->attributes()->url;
            }
        }
        if (!$image) {
            preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', (string) $item->description, $m);
            $image = $m[1] ?? null;
        }

        return [
            'title'       => (string) $item->title,
            'description' => strip_tags((string) $item->description),
            'link'        => (string) $item->link,
            'pubDate'     => (string) $item->pubDate,
            'image'       => $image,
        ];
    }

    protected function parseAtomEntry(\SimpleXMLElement $entry): array
    {
        $link = '';
        foreach ($entry->link as $l) {
            if ((string) $l->attributes()->rel === 'alternate') {
                $link = (string) $l->attributes()->href;
                break;
            }
        }
        if (!$link && isset($entry->link)) {
            $link = (string) $entry->link->attributes()->href;
        }

        $content = isset($entry->content) ? strip_tags((string) $entry->content) : '';
        $summary = isset($entry->summary) ? strip_tags((string) $entry->summary) : '';

        return [
            'title'       => (string) $entry->title,
            'description' => $content ?: $summary,
            'link'        => $link,
            'pubDate'     => (string) ($entry->published ?? $entry->updated ?? ''),
            'image'       => null,
        ];
    }

    protected function isAiRelated(string $text): bool
    {
        $text = mb_strtolower($text);
        foreach ($this->aiKeywords as $kw) {
            if (str_contains($text, $kw)) return true;
        }
        return false;
    }

    protected function detectCategory(string $text): string
    {
        $text = mb_strtolower($text);
        foreach ($this->categoryMap as $pattern => $slug) {
            if (preg_match('/(' . $pattern . ')/i', $text)) {
                return $slug;
            }
        }
        return 'inteligencia-artificial'; // fallback
    }

    protected function getOrCreateCategory(string $slug): \App\Models\Category
    {
        static $cache = [];
        if (isset($cache[$slug])) return $cache[$slug];

        $names = [
            'openai' => 'OpenAI', 'google-ai' => 'Google AI', 'microsoft-ai' => 'Microsoft AI',
            'meta-ai' => 'Meta AI', 'amazon-ai' => 'Amazon AI', 'anthropic' => 'Anthropic',
            'startups-de-ia' => 'Startups de IA', 'ia-generativa' => 'IA Generativa',
            'robotica' => 'Robótica', 'computacion-cuantica' => 'Computación Cuántica',
            'ia-en-salud' => 'IA en Salud', 'ia-en-finanzas' => 'IA en Finanzas',
            'ia-en-educacion' => 'IA en Educación', 'etica-de-la-ia' => 'Ética de la IA',
            'regulacion-de-ia' => 'Regulación de IA', 'impacto-laboral' => 'Impacto Laboral',
            'privacidad-y-seguridad' => 'Privacidad y Seguridad', 'nlp' => 'NLP',
            'computer-vision' => 'Computer Vision', 'machine-learning' => 'Machine Learning',
            'deep-learning' => 'Deep Learning', 'inteligencia-artificial' => 'Inteligencia Artificial',
        ];

        $cat = Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $names[$slug] ?? Str::title(str_replace('-', ' ', $slug)), 'description' => '']
        );

        $cache[$slug] = $cat;
        return $cat;
    }

    protected function enhanceWithAI(array $item, string $categoryName): ?array
    {
        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openaiKey   = env('OPENAI_API_KEY', '');

        $isEnglish = ($item['language'] ?? 'es') === 'en';
        $langNote  = $isEnglish ? "El artículo original está en inglés. Tradúcelo al español.\n" : '';

        $prompt = <<<PROMPT
Eres un periodista senior especializado en inteligencia artificial y tecnología, con estilo editorial propio de publicaciones de referencia como MIT Technology Review o Wired en español.
{$langNote}
FUENTE ORIGINAL:
Título: {$item['title']}
Contenido: {$item['content']}

Tu misión es transformar este material en un artículo de largo aliento que enganche al lector desde la primera línea y lo incentive a profundizar en el tema.

ESTRUCTURA OBLIGATORIA (sigue este orden):

1. TÍTULO: Atractivo, preciso, en español. Puede usar pregunta retórica, dato sorprendente o contraste.

2. APERTURA (primer párrafo, sin <h2>): Comienza con un dato impactante, una pregunta que interpele al lector, o una situación concreta que ilustre la noticia. El lector no debe poder dejar de leer tras el primer párrafo.

3. DESARROLLO (3 a 4 secciones con <h2>): Cada sección debe tener 2-3 párrafos sólidos. Incluye datos concretos, nombres de actores clave, fechas, cifras o comparaciones cuando el contenido original las tenga. Añade contexto del campo de {$categoryName} cuando aporte valor.

4. CITA DESTACADA: Incluye al menos un <blockquote> con la idea más reveladora o impactante del artículo. Puede ser una paráfrasis de lo más significativo.

5. CONTEXTO CLAVE (sección <h2>Contexto clave</h2>): Explica de forma accesible 2-3 conceptos técnicos que el lector necesita para entender la noticia en su totalidad. Usa lenguaje claro sin perder precisión. Esta sección convierte lectores ocasionales en lectores informados.

6. PARA PROFUNDIZAR (cierre obligatorio, sección <h2>Para profundizar</h2>): Lista <ul> con 3 ítems. Cada ítem sugiere un ángulo relacionado, una pregunta abierta o un área de investigación que complementa la noticia. Formato: <strong>Tema</strong> — Frase de 1-2 oraciones que explica la conexión y despierta curiosidad. No incluyas URLs externas.

REQUISITOS TÉCNICOS:
- Extensión mínima: 1.200 palabras en el campo content.
- Todo el content debe ser HTML válido (párrafos con <p>, subtítulos con <h2>, listas con <ul><li>).
- Excerpt: 2 oraciones en español que capturen la esencia y generen curiosidad. Máximo 220 caracteres.
- No menciones que el artículo fue traducido o reescrito.
- No inventes hechos, pero sí añade contexto verificable del campo.

Responde SOLO en JSON con estas claves: title, content, excerpt.
PROMPT;

        $guard = app(GeminiQuotaGuard::class);

        // Intentar Gemini primero (solo si hay cuota para prioridad alta)
        try {
            if (!empty($geminiKey) && $guard->canCall('high')) {
                $r = Http::timeout(30)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 3500, 'responseMimeType' => 'application/json'],
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

        // Fallback OpenAI
        try {
            if (!empty($openaiKey)) {
                $r = Http::timeout(30)->withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => env('OPENAI_MODEL_NAME', 'gpt-4-turbo'),
                    'temperature' => 0.7,
                    'max_tokens'  => 3500,
                    'messages'    => [
                        ['role' => 'system', 'content' => 'Responde siempre en JSON válido con claves: title, content, excerpt.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
                if ($r->successful()) {
                    $data = json_decode($r->json()['choices'][0]['message']['content'] ?? '{}', true);
                    if (!empty($data['title']) && !empty($data['content'])) return $data;
                }
            }
        } catch (\Exception) {}

        return null;
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

        $colors = [
            'inteligencia-artificial' => '4285F4', 'machine-learning' => '0F9D58',
            'deep-learning' => 'DB4437', 'nlp' => '673AB7', 'openai' => '412991',
            'google-ai' => '4285F4', 'microsoft-ai' => '00A4EF', 'anthropic' => '5A008E',
            'ia-generativa' => 'E91E63', 'regulacion-de-ia' => '2196F3',
        ];
        $color = $colors[$categorySlug] ?? '2c3e50';
        $text  = urlencode(Str::title(str_replace('-', ' ', $categorySlug)));
        return "https://via.placeholder.com/1200x630/{$color}/FFFFFF?text={$text}";
    }

    protected function cleanText(string $text): string
    {
        return trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /**
     * Filtro de calidad: rechaza artículos que no son sobre IA/tech aunque vengan de feeds confiables.
     * Evita noticias de política, deportes, entretenimiento que se cuelen por keywords ambiguas.
     */
    protected function passesQualityFilter(string $title, string $description): bool
    {
        $text = mb_strtolower($title . ' ' . $description);

        // Palabras que indican contenido claramente off-topic
        $offTopicSignals = [
            'narcolancha', 'partido político', 'fútbol', 'baloncesto', 'nba', 'fifa',
            'actor ', 'actriz', 'película', 'serie netflix', 'música', 'cantante',
            'presidente de ', 'elecciones', 'congreso', 'senado', 'guerra en',
            'accidente de tráfico', 'terremoto', 'huracán', 'incendio forestal',
            'receta de', 'horóscopo', 'embarazada', 'boda de', 'divorcio de',
        ];

        foreach ($offTopicSignals as $signal) {
            if (str_contains($text, $signal)) return false;
        }

        // El título debe tener al menos 5 palabras (evita títulos vacíos o basura)
        if (str_word_count($title) < 5) return false;

        // Si viene de un feed ai_only=false (ya especializado), pasa sin más validación
        // Si viene con ai_only=true, ya fue filtrado por isAiRelated() antes de llegar aquí
        return true;
    }
}
