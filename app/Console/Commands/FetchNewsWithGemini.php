<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Category;
use App\Services\GeminiQuotaGuard;
use App\Services\SimpleImageDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Obtiene noticias de IA usando Gemini con Google Search Grounding.
 * No requiere NewsAPI — Gemini busca en Google en tiempo real.
 *
 * Uso:
 *   php artisan news:fetch-gemini --category=inteligencia-artificial --count=5
 *   php artisan news:fetch-gemini --all --count=3
 */
class FetchNewsWithGemini extends Command
{
    protected $signature = 'news:fetch-gemini
        {--category= : Slug de la categoría (omitir para ver lista)}
        {--all       : Recorre todas las categorías activas}
        {--count=5   : Noticias por categoría}
        {--days=3    : Buscar noticias de los últimos N días}';

    protected $description = 'Obtiene noticias de IA desde Google vía Gemini Search Grounding (sin NewsAPI)';

    protected array $categories = [
        'inteligencia-artificial' => 'Inteligencia Artificial',
        'machine-learning'        => 'Machine Learning',
        'ia-generativa'           => 'IA Generativa',
        'openai'                  => 'OpenAI',
        'google-ai'               => 'Google AI',
        'anthropic'               => 'Anthropic',
        'regulacion-de-ia'        => 'Regulación de IA',
        'ia-en-salud'             => 'IA en Salud',
        'robotica'                => 'Robótica',
        'etica-de-la-ia'          => 'Ética de la IA',
    ];

    protected array $categoryColors = [
        'inteligencia-artificial' => '#4285F4',
        'machine-learning'        => '#0F9D58',
        'ia-generativa'           => '#E91E63',
        'openai'                  => '#412991',
        'google-ai'               => '#4285F4',
        'anthropic'               => '#5A008E',
        'regulacion-de-ia'        => '#2196F3',
        'ia-en-salud'             => '#4CAF50',
        'robotica'                => '#795548',
        'etica-de-la-ia'          => '#FF5722',
    ];

    protected array $categoryIcons = [
        'inteligencia-artificial' => 'fa-brain',
        'machine-learning'        => 'fa-cogs',
        'ia-generativa'           => 'fa-paint-brush',
        'openai'                  => 'fa-cube',
        'google-ai'               => 'fa-google',
        'anthropic'               => 'fa-comment',
        'regulacion-de-ia'        => 'fa-gavel',
        'ia-en-salud'             => 'fa-heartbeat',
        'robotica'                => 'fa-robot',
        'etica-de-la-ia'          => 'fa-balance-scale',
    ];

    public function __construct(protected SimpleImageDownloader $imageDownloader)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $apiKey = config('services.gemini.api_key');
        if (empty($apiKey)) {
            $this->error('GEMINI_API_KEY no configurada en .env');
            return 1;
        }

        // Modo --all: iterar todas las categorías
        if ($this->option('all')) {
            $total = 0;
            foreach ($this->categories as $slug => $name) {
                $this->info("\n── Categoría: {$name} ──");
                $total += $this->fetchForCategory($slug, $name, $apiKey);
                sleep(2); // pausa entre categorías para no sobrecargar Gemini
            }
            $this->info("\nTotal noticias guardadas: {$total}");
            return 0;
        }

        // Modo categoría única
        $slug = $this->option('category');
        if (empty($slug)) {
            $this->info("Categorías disponibles:");
            foreach ($this->categories as $s => $n) {
                $this->line("  {$s}  →  {$n}");
            }
            return 0;
        }

        if (!array_key_exists($slug, $this->categories)) {
            $this->error("Categoría no válida: {$slug}");
            return 1;
        }

        $saved = $this->fetchForCategory($slug, $this->categories[$slug], $apiKey);
        $this->info("Noticias guardadas: {$saved}");
        return 0;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function fetchForCategory(string $slug, string $name, string $apiKey): int
    {
        $count = (int) $this->option('count');
        $days  = (int) $this->option('days');
        $guard = app(GeminiQuotaGuard::class);

        if (!$guard->canCall('high')) {
            $this->warn("Gemini quota insuficiente para {$name}. " . $guard->summary());
            return 0;
        }

        $this->info("Buscando {$count} noticias sobre \"{$name}\" (últimos {$days} días)...");

        // ── 1. Gemini busca y devuelve artículos estructurados ──────────────
        $articles = $this->searchWithGemini($slug, $name, $count, $days, $apiKey, $guard);

        if (empty($articles)) {
            $this->warn("No se obtuvieron artículos para {$name}.");
            return 0;
        }

        $this->info("Gemini devolvió " . count($articles) . " artículos.");

        // ── 2. Obtener o crear categoría ─────────────────────────────────────
        $category = Category::firstOrCreate(
            ['slug' => $slug],
            [
                'name'        => $name,
                'description' => "Noticias sobre {$name}",
                'color'       => ltrim($this->categoryColors[$slug] ?? '#2c3e50', '#'),
                'icon'        => $this->categoryIcons[$slug] ?? 'fa-tag',
            ]
        );

        // ── 3. Persistir artículos ───────────────────────────────────────────
        $saved          = 0;
        $imagesToUpdate = [];

        foreach ($articles as $article) {
            if (empty($article['title']) || empty($article['content'])) {
                continue;
            }

            $articleSlug = Str::slug($article['title']);

            if (News::where('slug', $articleSlug)->exists()) {
                $this->line("  · Ya existe: {$article['title']}");
                continue;
            }

            $readingTime = max(1, (int) ceil(str_word_count(strip_tags($article['content'])) / 200));

            $news = News::create([
                'title'       => $article['title'],
                'slug'        => $articleSlug,
                'content'     => $article['content'],
                'excerpt'     => $article['excerpt'] ?? Str::limit(strip_tags($article['content']), 200),
                'image'       => null, // se actualiza después si hay imagen
                'author'      => $article['source'] ?? 'ConocIA',
                'source'      => $article['source'] ?? 'ConocIA',
                'source_url'  => $article['source_url'] ?? '',
                'category_id' => $category->id,
                'featured'    => true,
                'status'      => 'published',
                'is_published'=> 1,
                'reading_time'=> $readingTime,
                'views'       => rand(30, 300),
                'published_at'=> now(),
            ]);

            $this->info("  ✓ Guardada: {$article['title']}");
            $saved++;

            // Buscar imagen en Pexels usando el título como query
            $imageUrl = $this->fetchPexelsImage($article['title'], $slug);
            if ($imageUrl) {
                $imagesToUpdate[$imageUrl] = $news->id;
            }
        }

        // ── 4. Descargar imágenes en lote ────────────────────────────────────
        if (!empty($imagesToUpdate)) {
            $this->downloadImages($imagesToUpdate, $slug);
        }

        return $saved;
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Llama a Gemini con Google Search Grounding para obtener noticias reales
     * y las convierte en artículos completos en una sola llamada.
     */
    private function searchWithGemini(
        string $slug, string $name, int $count, int $days,
        string $apiKey, GeminiQuotaGuard $guard
    ): array {
        $model = config('services.gemini.model', 'gemini-2.0-flash');

        $today     = now()->format('d/m/Y');
        $sinceDate = now()->subDays($days)->format('d/m/Y');

        // ── Paso 1: Search Grounding — buscar y resumir (sin artículo completo) ──
        $searchPrompt = <<<PROMPT
Usa Google Search para encontrar las {$count} noticias más recientes e importantes sobre "{$name}" publicadas entre el {$sinceDate} y el {$today}.

Devuelve SOLO un JSON con array "articles". Cada elemento debe tener:
- title: string (título en español, SEO-friendly, máx 100 chars)
- summary: string (resumen de los hechos principales, 3-4 párrafos, texto plano)
- excerpt: string (2 oraciones atractivas, máx 220 caracteres)
- source: string (nombre del medio original)
- source_url: string (URL del artículo si la tienes, sino "")
- image_url: string (URL de imagen si la tienes, sino "")

Si no encuentras {$count} noticias recientes, devuelve las que puedas. Responde SOLO con el JSON, sin texto adicional.
PROMPT;

        try {
            $r1 = Http::timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'tools'            => [['google_search' => (object)[]]], // Search Grounding
                    'contents'         => [['parts' => [['text' => $searchPrompt]]]],
                    'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 4096],
                ]
            );

            $guard->record();

            if ($r1->failed()) {
                $this->error("Gemini Search error: " . $r1->status() . " — " . $r1->body());
                return [];
            }

            $parts   = $r1->json()['candidates'][0]['content']['parts'] ?? [];
            $rawJson = implode('', array_column($parts, 'text'));
            $found   = $this->extractJson($rawJson);

            if (empty($found['articles'])) {
                $this->error("Gemini no devolvió artículos en paso 1.");
                Log::error('FetchNewsWithGemini paso1 JSON error', ['raw' => substr($rawJson, 0, 600)]);
                return [];
            }

            $this->line("  Encontradas " . count($found['articles']) . " noticias. Expandiendo artículos...");

            // ── Paso 2: Expandir cada artículo sin Grounding ──────────────────
            $articles = [];
            foreach ($found['articles'] as $stub) {
                $expandPrompt = <<<EXPAND
Eres un periodista editorial especializado en tecnología e inteligencia artificial.
Tienes este resumen de una noticia real:

TÍTULO: {$stub['title']}
RESUMEN: {$stub['summary']}
FUENTE: {$stub['source']}

Escribe el artículo completo en HTML con esta estructura:
- Párrafo de apertura gancho (sin <h2>)
- <h2>Los detalles</h2>: 2-3 párrafos con los hechos
- <h2>Por qué importa</h2>: contexto e implicaciones
- <blockquote>: la idea más reveladora
- <h2>Contexto técnico</h2>: explica 2 conceptos clave de forma accesible
- <h2>Para profundizar</h2>: lista <ul> con 3 ítems formato <strong>Tema</strong> — descripción

Mínimo 700 palabras. Todo en español. Responde SOLO con el HTML del artículo, sin JSON ni texto adicional.
EXPAND;

                try {
                    $r2 = Http::timeout(60)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        [
                            'contents'         => [['parts' => [['text' => $expandPrompt]]]],
                            'generationConfig' => ['temperature' => 0.6, 'maxOutputTokens' => 8192],
                        ]
                    );

                    $htmlContent = $r2->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!empty($htmlContent)) {
                        $stub['content'] = $this->stripMarkdownFences($htmlContent);
                    } else {
                        // Fallback: usar el resumen como contenido
                        $stub['content'] = '<p>' . implode('</p><p>', array_map('trim', explode("\n", $stub['summary']))) . '</p>';
                    }
                } catch (\Exception $e) {
                    Log::warning('FetchNewsWithGemini expand error: ' . $e->getMessage());
                    $stub['content'] = '<p>' . $stub['summary'] . '</p>';
                }

                unset($stub['summary']);
                $articles[] = $stub;
                sleep(1); // respetar rate limit
            }

            return $articles;

        } catch (\Exception $e) {
            $this->error("Excepción llamando a Gemini: " . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Extrae el primer objeto/array JSON válido de un texto libre.
     * Necesario porque Gemini 2.5 con Search Grounding mezcla texto y JSON.
     */
    private function extractJson(string $text): ?array
    {
        // 1. Intentar directo
        $decoded = json_decode(trim($text), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2. Extraer bloque ```json ... ```
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // 3. Extraer primer { ... } o [ ... ] balanceado
        foreach (['{', '['] as $open) {
            $pos = strpos($text, $open);
            if ($pos === false) continue;

            $close   = $open === '{' ? '}' : ']';
            $depth   = 0;
            $inStr   = false;
            $escaped = false;
            $end     = null;

            for ($i = $pos; $i < strlen($text); $i++) {
                $c = $text[$i];
                if ($escaped)       { $escaped = false; continue; }
                if ($c === '\\')    { $escaped = true;  continue; }
                if ($c === '"')     { $inStr = !$inStr; continue; }
                if ($inStr)         continue;
                if ($c === $open)   $depth++;
                if ($c === $close)  { $depth--; if ($depth === 0) { $end = $i; break; } }
            }

            if ($end !== null) {
                $candidate = substr($text, $pos, $end - $pos + 1);
                $decoded   = json_decode($candidate, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return null;
    }

    /**
     * Busca una imagen relevante en Pexels para el título dado.
     * Usa el título como query, con fallback a la categoría.
     */
    private function fetchPexelsImage(string $title, string $categorySlug): ?string
    {
        $apiKey = config('services.pexels.api_key');
        if (empty($apiKey)) {
            return null;
        }

        // Extraer palabras clave del título (primeras 4 palabras significativas)
        $words    = preg_split('/\s+/', $title);
        $query    = implode(' ', array_slice($words, 0, 4));
        $fallback = $this->categories[$categorySlug] ?? 'technology artificial intelligence';

        foreach ([$query, $fallback] as $searchQuery) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)
                    ->withHeaders(['Authorization' => $apiKey])
                    ->get('https://api.pexels.com/v1/search', [
                        'query'       => $searchQuery,
                        'per_page'    => 5,
                        'orientation' => 'landscape',
                    ]);

                if ($response->successful()) {
                    $photos = $response->json()['photos'] ?? [];
                    if (!empty($photos)) {
                        // Toma una foto aleatoria de los primeros 5 resultados
                        $photo = $photos[array_rand($photos)];
                        return $photo['src']['large2x'] ?? $photo['src']['large'] ?? null;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Pexels API error: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Elimina markdown code fences (```html ... ``` o ``` ... ```) del contenido
     * que Gemini a veces añade alrededor del HTML generado.
     */
    private function stripMarkdownFences(string $text): string
    {
        $text = trim($text);
        // Eliminar ```html o ``` al inicio
        if (preg_match('/^```(?:html)?\s*\n?([\s\S]*?)\n?```\s*$/i', $text, $m)) {
            $text = trim($m[1]);
        }
        // Convertir <h1> a <h2> — el template ya muestra el título real en h1
        $text = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', $text);
        $text = preg_replace('/<\/h1>/i', '</h2>', $text);
        return $text;
    }

    private function downloadImages(array $imagesToUpdate, string $categorySlug): void
    {
        $this->info("Descargando " . count($imagesToUpdate) . " imágenes...");

        // Preparar formato para SimpleImageDownloader
        $payload = [];
        foreach ($imagesToUpdate as $url => $newsId) {
            $payload[$url] = ['categorySlug' => $categorySlug, 'newsId' => $newsId];
        }

        $results = $this->imageDownloader->downloadMultiple($payload);

        foreach ($results as $url => $localPath) {
            $newsId = $imagesToUpdate[$url];
            if ($localPath) {
                News::where('id', $newsId)->update(['image' => $localPath]);
                $this->info("  ✓ Imagen guardada para noticia #{$newsId}");
            }
        }
    }
}
