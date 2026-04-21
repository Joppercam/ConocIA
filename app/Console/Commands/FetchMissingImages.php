<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Services\SimpleImageDownloader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchMissingImages extends Command
{
    protected $signature = 'news:fetch-missing-images
                            {--limit=50 : Número máximo de artículos a procesar}
                            {--dry-run  : Muestra qué haría sin descargar nada}';

    protected $description = 'Busca y descarga imágenes desde Pexels para noticias que no tienen imagen real';

    // Queries Pexels por slug de categoría — van de más específico a más general
    protected array $categoryQueries = [
        'inteligencia-artificial' => ['artificial intelligence technology', 'AI robot future', 'machine learning data'],
        'machine-learning'        => ['machine learning algorithm', 'neural network data', 'deep learning computer'],
        'deep-learning'           => ['deep learning neural network', 'artificial intelligence brain', 'data science'],
        'nlp'                     => ['natural language processing', 'text analysis computer', 'chatbot AI'],
        'computer-vision'         => ['computer vision camera', 'image recognition technology', 'optical AI'],
        'robotica'                => ['robot technology', 'robotics automation', 'industrial robot'],
        'computacion-cuantica'    => ['quantum computer technology', 'quantum computing', 'futuristic processor'],
        'openai'                  => ['openai chatgpt technology', 'AI language model', 'chatbot conversation'],
        'google-ai'               => ['google AI technology', 'artificial intelligence lab', 'tech innovation'],
        'microsoft-ai'            => ['microsoft technology AI', 'cloud computing AI', 'tech innovation'],
        'meta-ai'                 => ['social media AI', 'artificial intelligence technology', 'tech innovation'],
        'amazon-ai'               => ['amazon cloud AI', 'AWS technology', 'cloud computing'],
        'anthropic'               => ['AI safety research', 'artificial intelligence ethics', 'AI lab technology'],
        'startups-de-ia'          => ['technology startup innovation', 'AI startup team', 'tech entrepreneur'],
        'ia-generativa'           => ['generative AI art', 'AI image creation', 'creative technology AI'],
        'automatizacion'          => ['automation technology robot', 'factory automation', 'industrial technology'],
        'ia-en-salud'             => ['medical technology AI', 'healthcare innovation', 'digital health'],
        'ia-en-finanzas'          => ['fintech technology', 'financial AI data', 'stock market technology'],
        'ia-en-educacion'         => ['education technology laptop', 'online learning digital', 'student technology'],
        'etica-de-la-ia'          => ['ethics technology balance', 'AI responsibility', 'technology society'],
        'regulacion-de-ia'        => ['law technology regulation', 'government technology policy', 'digital regulation'],
        'impacto-laboral'         => ['work technology future', 'workplace automation', 'technology employment'],
        'privacidad-y-seguridad'  => ['cybersecurity technology', 'data privacy security', 'digital security lock'],
        'investigacion'           => ['research laboratory science', 'scientist data analysis', 'academic research'],
        'tecnologia'              => ['technology innovation digital', 'futuristic technology', 'tech device'],
        'ciberseguridad'          => ['cybersecurity hacker protection', 'network security', 'digital security'],
        'innovacion'              => ['innovation technology startup', 'creative technology', 'future innovation'],
        'educacion'               => ['education classroom technology', 'learning digital', 'student university'],
    ];

    // Fallback genérico si la categoría no tiene mapeo
    protected array $genericQueries = [
        'artificial intelligence technology',
        'technology innovation future',
        'digital transformation data',
    ];

    public function __construct(protected SimpleImageDownloader $imageDownloader)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $apiKey = config('services.pexels.api_key');
        if (empty($apiKey)) {
            $this->error('PEXELS_API_KEY no está configurada en .env');
            return 1;
        }

        $limit   = (int) $this->option('limit');
        $dryRun  = $this->option('dry-run');

        // Artículos publicados sin imagen real
        $articles = News::with('category')
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('image')
                  ->orWhere('image', '')
                  ->orWhere('image', 'null')
                  ->orWhereRaw("image LIKE '%default%'")
                  ->orWhereRaw("image LIKE '%placeholder%'");
            })
            ->latest('published_at')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('✓ Todas las noticias publicadas ya tienen imagen.');
            return 0;
        }

        $this->info("Encontrados {$articles->count()} artículos sin imagen.");

        if ($dryRun) {
            foreach ($articles as $a) {
                $this->line("  [{$a->id}] {$a->title}");
            }
            return 0;
        }

        $ok   = 0;
        $fail = 0;

        foreach ($articles as $article) {
            $catSlug = $article->category?->slug ?? 'tecnologia';
            $url     = $this->searchPexels($article->title, $catSlug, $apiKey);

            if (!$url) {
                $this->warn("  ✗ Sin imagen para: {$article->title}");
                $fail++;
                continue;
            }

            $article->update(['image' => $url]);
            $this->info("  ✓ [{$article->id}] " . Str::limit($article->title, 60));
            $ok++;

            // Respetar rate-limit de Pexels (200 req/hora en plan free)
            usleep(300_000); // 300ms entre requests
        }

        $this->info("\nResultado: {$ok} imágenes descargadas, {$fail} fallos.");
        return 0;
    }

    /**
     * Busca en Pexels con el título y hasta 2 queries de fallback de la categoría.
     */
    protected function searchPexels(string $title, string $catSlug, string $apiKey): ?string
    {
        // Query 1: palabras clave del título (ignorar artículos/preposiciones)
        $stopWords = ['de', 'la', 'el', 'los', 'las', 'un', 'una', 'en', 'con', 'por', 'para', 'del', 'al', 'se', 'que', 'es', 'su', 'sus', 'y', 'o', 'e', 'a'];
        $words = array_filter(
            preg_split('/\s+/', Str::ascii(strtolower($title))),
            fn($w) => strlen($w) > 3 && !in_array($w, $stopWords)
        );
        $titleQuery = implode(' ', array_slice(array_values($words), 0, 4));

        // Queries de categoría
        $catQueries = $this->categoryQueries[$catSlug] ?? $this->genericQueries;

        // Intentar cada query en orden hasta obtener una foto
        foreach (array_merge([$titleQuery], $catQueries) as $query) {
            if (empty(trim($query))) continue;

            $photo = $this->pexelsSearch($query, $apiKey);
            if ($photo) return $photo;
        }

        return null;
    }

    /**
     * Llama a la API de Pexels y retorna la URL de la foto más relevante.
     */
    protected function pexelsSearch(string $query, string $apiKey): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $apiKey])
                ->get('https://api.pexels.com/v1/search', [
                    'query'       => $query,
                    'per_page'    => 10,
                    'orientation' => 'landscape',
                ]);

            if ($response->successful()) {
                $photos = $response->json('photos') ?? [];
                if (!empty($photos)) {
                    // Foto aleatoria entre los primeros resultados para variedad
                    $photo = $photos[array_rand($photos)];
                    return $photo['src']['large2x'] ?? $photo['src']['large'] ?? null;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Pexels search failed for '{$query}': " . $e->getMessage());
        }

        return null;
    }
}
