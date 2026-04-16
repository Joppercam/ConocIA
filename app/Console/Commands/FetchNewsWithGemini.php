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

            // Si Gemini devolvió imagen URL, descargarla
            if (!empty($article['image_url'])) {
                $imagesToUpdate[$article['image_url']] = $news->id;
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

        $prompt = <<<PROMPT
Eres un periodista senior de tecnología e inteligencia artificial. Usa Google Search para encontrar las {$count} noticias más recientes e importantes sobre "{$name}" publicadas entre el {$sinceDate} y el {$today}.

Para CADA noticia encontrada, genera un artículo periodístico completo siguiendo esta estructura HTML:
- Apertura: párrafo gancho sin <h2>
- Desarrollo: 3-4 secciones con <h2>, cada una con 2-3 párrafos
- <blockquote> con la idea más reveladora
- <h2>Contexto clave</h2>: explica 2-3 conceptos técnicos de forma accesible
- <h2>Para profundizar</h2>: lista <ul> con 3 ítems en formato <strong>Tema</strong> — descripción

Requisitos:
- Mínimo 900 palabras por artículo
- Todo en español con terminología técnica precisa
- Verifica que las noticias sean reales y recientes
- Si no encuentras {$count} noticias recientes sobre "{$name}", devuelve las que puedas

Devuelve un JSON con array "articles", cada elemento con:
- title: string (título en español, SEO-friendly)
- content: string (artículo HTML completo)
- excerpt: string (2 oraciones, max 220 caracteres)
- source: string (nombre del medio original)
- source_url: string (URL del artículo original si la tienes, sino "")
- image_url: string (URL de imagen representativa si la tienes, sino "")
PROMPT;

        try {
            $response = Http::timeout(90)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                [
                    'tools' => [
                        ['google_search' => (object)[]], // Search Grounding
                    ],
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.4,
                        'maxOutputTokens'  => 8192,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

            $guard->record();

            if ($response->failed()) {
                $this->error("Gemini error: " . $response->status() . " — " . $response->body());
                return [];
            }

            $raw = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($raw)) {
                $this->warn("Gemini devolvió respuesta vacía.");
                return [];
            }

            $decoded = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Error decodificando JSON de Gemini: " . json_last_error_msg());
                Log::error('FetchNewsWithGemini JSON error', ['raw' => substr($raw, 0, 500)]);
                return [];
            }

            return $decoded['articles'] ?? [];

        } catch (\Exception $e) {
            $this->error("Excepción llamando a Gemini: " . $e->getMessage());
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

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
