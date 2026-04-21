<?php

namespace App\Console\Commands;

use App\Models\Startup;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchStartups extends Command
{
    protected $signature = 'startups:fetch
                            {--limit=10 : Máximo de startups a importar por ejecución}
                            {--dry-run  : Mostrar sin guardar}';

    protected $description = 'Detecta startups de IA relevantes desde noticias recientes y fuentes curadas';

    protected array $rssSources = [
        'https://techcrunch.com/tag/artificial-intelligence/feed/',
        'https://venturebeat.com/ai/feed/',
        'https://www.theinformation.com/feed',
    ];

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $guard  = app(GeminiQuotaGuard::class);

        $this->info('Buscando startups de IA en noticias recientes...');

        $articles = $this->fetchRssArticles();
        $this->info('Artículos RSS encontrados: ' . count($articles));

        $startups = [];

        if (!empty($articles)) {
            $startups = $this->extractStartupsFromArticles($articles, $guard);
        }

        if (empty($startups)) {
            $this->warn('RSS sin resultados claros, usando IA como fuente curada...');
            $startups = $this->fetchCuratedFromAI($guard);
        }

        if (empty($startups)) {
            $this->warn('Sin startups encontradas.');
            return Command::SUCCESS;
        }

        $imported = 0;
        foreach (array_slice($startups, 0, $limit) as $s) {
            if (empty($s['name'])) continue;

            if (Startup::where('name', $s['name'])->exists()) {
                $this->line("  DUPL: {$s['name']}");
                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] {$s['name']} ({$s['sector']} / {$s['stage']})");
                $imported++;
                continue;
            }

            $slug = $this->uniqueSlug(Str::slug($s['name']));

            Startup::create([
                'name'              => $s['name'],
                'slug'              => $slug,
                'tagline'           => $s['tagline'] ?? null,
                'description'       => $s['description'] ?? null,
                'website_url'       => $s['website_url'] ?? null,
                'founded_year'      => $s['founded_year'] ?? null,
                'country'           => $s['country'] ?? null,
                'city'              => $s['city'] ?? null,
                'sector'            => $s['sector'] ?? 'other',
                'stage'             => $s['stage'] ?? null,
                'total_funding_usd' => $s['total_funding_usd'] ?? null,
                'last_funding_date' => $s['last_funding_date'] ?? null,
                'investors'         => $s['investors'] ?? null,
                'products'          => $s['products'] ?? null,
                'source_url'        => $s['source_url'] ?? null,
                'featured'          => false,
                'active'            => true,
                'auto_generated'    => true,
                'last_synced_at'    => now(),
            ]);

            $this->info("  OK: {$s['name']}");
            $imported++;
        }

        $this->info("Total startups importadas: {$imported}");
        return Command::SUCCESS;
    }

    protected function fetchRssArticles(): array
    {
        $articles = [];

        foreach ($this->rssSources as $url) {
            try {
                $r = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'ConocIA StartupBot/1.0'])
                    ->get($url);

                if ($r->failed()) continue;

                $xml = @simplexml_load_string($r->body());
                if (!$xml) continue;

                $items = $xml->channel->item ?? $xml->entry ?? [];
                $count = 0;

                foreach ($items as $item) {
                    $title   = (string) ($item->title ?? '');
                    $desc    = strip_tags((string) ($item->description ?? $item->summary ?? ''));
                    $link    = (string) ($item->link ?? $item->id ?? '');
                    $pubDate = (string) ($item->pubDate ?? $item->updated ?? '');

                    if (empty($title)) continue;

                    // Solo artículos con keywords de startup/funding
                    $haystack = strtolower($title . ' ' . $desc);
                    $keywords = ['startup', 'raises', 'funding', 'series', 'million', 'seed', 'round', 'valuation', 'launches'];
                    $relevant = false;
                    foreach ($keywords as $kw) {
                        if (str_contains($haystack, $kw)) { $relevant = true; break; }
                    }
                    if (!$relevant) continue;

                    $articles[] = [
                        'title'   => $title,
                        'excerpt' => Str::limit($desc, 400),
                        'url'     => $link,
                        'date'    => $pubDate,
                    ];

                    if (++$count >= 8) break;
                }
            } catch (\Exception $e) {
                Log::warning("FetchStartups RSS error ({$url}): " . $e->getMessage());
            }
        }

        return array_slice($articles, 0, 20);
    }

    protected function extractStartupsFromArticles(array $articles, GeminiQuotaGuard $guard): array
    {
        if (!$guard->canCall('high')) return [];

        $articleText = '';
        foreach ($articles as $a) {
            $articleText .= "TÍTULO: {$a['title']}\nRESUMEN: {$a['excerpt']}\nURL: {$a['url']}\n\n";
        }

        $prompt = <<<PROMPT
Analiza estas noticias recientes sobre IA y extrae las startups de IA mencionadas con sus datos más relevantes.

NOTICIAS:
{$articleText}

Para cada startup identificada, devuelve un objeto JSON con:
- name: nombre de la startup
- tagline: descripción de 1 línea en español (qué hace)
- description: 2-3 oraciones en español describiendo el producto/servicio
- website_url: URL del sitio web si se menciona, sino null
- founded_year: año de fundación si se menciona, sino null
- country: país de origen si se menciona, sino null
- city: ciudad si se menciona, sino null
- sector: uno de nlp|computer-vision|robotics|infrastructure|healthcare|education|finance|productivity|developer-tools|security|autonomous|multimodal|other
- stage: uno de pre-seed|seed|series-a|series-b|series-c|public|acquired|stealth, según la ronda mencionada
- total_funding_usd: monto total recaudado en millones USD (número decimal) si se menciona, sino null
- last_funding_date: fecha de la última ronda en YYYY-MM-DD si se menciona, sino null
- investors: array de nombres de inversores si se mencionan, sino null
- products: array de nombres de productos principales si se mencionan, sino null
- source_url: URL del artículo de donde se extrajo

Devuelve SOLO un array JSON. Solo incluye startups de IA genuinas (no empresas grandes establecidas como OpenAI, Google, Microsoft). Máximo 8 startups.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        try {
            if (!empty($geminiKey)) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
                    ]
                );

                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $data = json_decode($raw, true);
                    if (is_array($data)) {
                        $guard->record();
                        return isset($data[0]) ? $data : ($data['startups'] ?? []);
                    }
                }
            }
        } catch (\Exception) {}

        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 3000, 0.3);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['startups'] ?? []);
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function fetchCuratedFromAI(GeminiQuotaGuard $guard): array
    {
        if (!$guard->canCall('high')) return [];

        $today       = now()->format('Y-m-d');
        $existingNames = Startup::pluck('name')->implode(', ');

        $prompt = <<<PROMPT
Hoy es {$today}. Eres un experto en el ecosistema de startups de inteligencia artificial.

Startups que ya tenemos registradas: {$existingNames}

Lista las startups de IA más relevantes y noticiosas de los últimos 6 meses que NO estén en nuestra lista. Prioriza startups que:
- Hayan levantado rondas de financiamiento importantes
- Hayan lanzado productos novedosos
- Estén generando impacto en la industria
- Incluye startups latinoamericanas si las hay

Para cada una devuelve un objeto JSON con:
- name, tagline, description (en español), website_url, founded_year, country, city
- sector: nlp|computer-vision|robotics|infrastructure|healthcare|education|finance|productivity|developer-tools|security|autonomous|multimodal|other
- stage: pre-seed|seed|series-a|series-b|series-c|public|acquired|stealth
- total_funding_usd: en millones, número decimal o null
- last_funding_date: YYYY-MM-DD o null
- investors: array de strings o null
- products: array de strings o null
- source_url: null

Devuelve SOLO un array JSON. Máximo 10 startups.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        try {
            if (!empty($geminiKey)) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
                    ]
                );

                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $data = json_decode($raw, true);
                    if (is_array($data)) {
                        $guard->record();
                        return isset($data[0]) ? $data : ($data['startups'] ?? []);
                    }
                }
            }
        } catch (\Exception) {}

        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 3000, 0.3);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['startups'] ?? []);
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (Startup::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
