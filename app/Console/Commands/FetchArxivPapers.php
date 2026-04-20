<?php

namespace App\Console\Commands;

use App\Models\ConocIaPaper;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchArxivPapers extends Command
{
    protected $signature = 'papers:fetch-arxiv
                            {--categories=cs.AI,cs.LG,cs.CL,cs.CV : Categorías arXiv separadas por coma}
                            {--max-results=3 : Papers por categoría}
                            {--days=4 : Solo papers publicados en los últimos N días}
                            {--dry-run : Mostrar sin guardar}';

    protected $description = 'Importa papers de arXiv y genera resúmenes editoriales en español';

    /** Etiquetas legibles para categorías arXiv */
    protected array $categoryLabels = [
        'cs.AI'  => 'Inteligencia Artificial',
        'cs.LG'  => 'Machine Learning',
        'cs.CL'  => 'Procesamiento del Lenguaje Natural',
        'cs.CV'  => 'Computer Vision',
        'cs.RO'  => 'Robótica',
        'cs.NE'  => 'Redes Neuronales',
        'stat.ML'=> 'Estadística & ML',
    ];

    public function handle(): int
    {
        $categories = array_map('trim', explode(',', $this->option('categories')));
        $maxResults = (int) $this->option('max-results');
        $days       = (int) $this->option('days');
        $dryRun     = $this->option('dry-run');
        $guard      = app(GeminiQuotaGuard::class);

        $cutoff = now()->subDays($days);
        $total  = 0;

        foreach ($categories as $category) {
            $this->info("Consultando arXiv: {$category}...");

            $papers = $this->fetchFromArxiv($category, $maxResults, $cutoff);

            if (empty($papers)) {
                $this->warn("  Sin resultados nuevos para {$category}.");
                continue;
            }

            foreach ($papers as $paper) {
                if (ConocIaPaper::where('arxiv_id', $paper['arxiv_id'])->exists()) {
                    $this->line("  Ya existe: {$paper['arxiv_id']}");
                    continue;
                }

                if (!$guard->canCall('medium')) {
                    $this->warn('Gemini quota agotada. ' . $guard->summary());
                    break 2;
                }

                if ($dryRun) {
                    $this->line("  [dry-run] {$paper['original_title']}");
                    continue;
                }

                $editorial = $this->generateEditorial($paper, $category, $guard);

                if (empty($editorial)) {
                    $this->warn("  Sin editorial generado para: {$paper['original_title']}");
                    continue;
                }

                $slug = $this->uniqueSlug('arxiv-' . str_replace('.', '-', $paper['arxiv_id']));

                ConocIaPaper::create([
                    'arxiv_id'               => $paper['arxiv_id'],
                    'arxiv_url'              => $paper['arxiv_url'],
                    'original_title'         => $paper['original_title'],
                    'original_abstract'      => $paper['original_abstract'],
                    'authors'                => $paper['authors'],
                    'arxiv_published_date'   => $paper['published_date'],
                    'arxiv_category'         => $category,
                    'title'                  => $editorial['title'] ?? $paper['original_title'],
                    'slug'                   => $slug,
                    'excerpt'                => $editorial['excerpt'] ?? Str::limit($paper['original_abstract'], 220),
                    'content'                => $editorial['content'] ?? null,
                    'key_contributions'      => $editorial['key_contributions'] ?? null,
                    'practical_implications' => $editorial['practical_implications'] ?? null,
                    'difficulty_level'       => $editorial['difficulty_level'] ?? 'intermedio',
                    'reading_time'           => $editorial['reading_time'] ?? 5,
                    'status'                 => 'published',
                    'published_at'           => now(),
                ]);

                $this->info("  Paper guardado: {$editorial['title']}");
                $total++;

                sleep(3); // Respetar rate limit de arXiv
            }
        }

        $this->info("Total papers importados: {$total}");
        return Command::SUCCESS;
    }

    protected function fetchFromArxiv(string $category, int $maxResults, \Carbon\Carbon $cutoff): array
    {
        $url = 'http://export.arxiv.org/api/query?' . http_build_query([
            'search_query' => "cat:{$category}",
            'sortBy'       => 'submittedDate',
            'sortOrder'    => 'descending',
            'max_results'  => $maxResults * 3, // traer más para filtrar por fecha
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'ConocIA ArXiv Reader/1.0 (jpablo.basualdo@gmail.com)'])
                ->get($url);

            if ($response->failed()) {
                Log::error("FetchArxivPapers: HTTP error for {$category}: " . $response->status());
                return [];
            }

            $xml = simplexml_load_string($response->body());
            if (!$xml) {
                Log::error("FetchArxivPapers: XML parse error for {$category}");
                return [];
            }

            $papers = [];
            foreach ($xml->entry as $entry) {
                $publishedStr = (string) $entry->published;
                $publishedDate = \Carbon\Carbon::parse($publishedStr);

                if ($publishedDate->lt($cutoff)) {
                    continue; // Paper demasiado antiguo
                }

                // Extraer arXiv ID desde la URL
                $idUrl   = (string) $entry->id;
                $arxivId = basename(str_replace('http://arxiv.org/abs/', '', $idUrl));
                $arxivId = preg_replace('/v\d+$/', '', $arxivId); // quitar versión

                // Autores
                $authors = [];
                foreach ($entry->author as $author) {
                    $authors[] = (string) $author->name;
                }

                $papers[] = [
                    'arxiv_id'         => $arxivId,
                    'arxiv_url'        => "https://arxiv.org/abs/{$arxivId}",
                    'original_title'   => trim((string) $entry->title),
                    'original_abstract'=> trim((string) $entry->summary),
                    'authors'          => $authors,
                    'published_date'   => $publishedDate->toDateString(),
                ];

                if (count($papers) >= $maxResults) break;
            }

            return $papers;
        } catch (\Exception $e) {
            Log::error("FetchArxivPapers exception for {$category}: " . $e->getMessage());
            return [];
        }
    }

    protected function generateEditorial(array $paper, string $category, GeminiQuotaGuard $guard): array
    {
        $categoryLabel = $this->categoryLabels[$category] ?? $category;
        $authorsStr    = implode(', ', array_slice($paper['authors'], 0, 4));
        $authorsNote   = count($paper['authors']) > 4 ? $authorsStr . ' et al.' : $authorsStr;

        $prompt = <<<PROMPT
Eres un redactor editorial especializado en {$categoryLabel}, con el estilo de Nature News o Quanta Magazine en español. Tu audiencia son profesionales y estudiantes avanzados de IA.

PAPER ORIGINAL:
Título: {$paper['original_title']}
Autores: {$authorsNote}
Abstract: {$paper['original_abstract']}
Categoría arXiv: {$category}

Transforma este paper en un artículo editorial accesible en español. El objetivo es que un profesional NO especializado en el subtema exacto del paper pueda entender qué se hizo, por qué importa y qué significa para el campo.

ESTRUCTURA OBLIGATORIA (HTML):

<h2>¿De qué trata este paper?</h2>
Explica el problema que aborda y por qué es relevante. Sin jerga innecesaria. 2 párrafos.

<h2>¿Qué hicieron los investigadores?</h2>
Metodología y enfoque, explicado como a un colega técnico pero no especialista. 2-3 párrafos.

<h2>Resultados principales</h2>
Los hallazgos más significativos. Con números si los hay. 2 párrafos.

<blockquote>La contribución más importante del paper, en una o dos oraciones.</blockquote>

<h2>¿Por qué importa para el campo?</h2>
Implicaciones para investigadores, ingenieros y la industria. 1-2 párrafos.

<h2>Limitaciones y debate abierto</h2>
¿Qué queda sin resolver? ¿Qué críticas podría recibir? 1 párrafo honesto.

REQUISITOS:
- Extensión mínima: 600 palabras en content.
- HTML válido: <p>, <h2>, <blockquote>.
- difficulty_level: una de "básico", "intermedio", "avanzado" según qué tan técnico es el paper.
- title: título editorial en español (no traducción literal, sino adaptado para captar atención).
- excerpt: 2 oraciones en español, máx 220 caracteres, que generen curiosidad.
- key_contributions: array de 3-4 strings con los aportes principales (frases cortas).
- practical_implications: array de 2-3 strings con implicaciones prácticas.

Responde SOLO en JSON con claves: title, content, excerpt, key_contributions, practical_implications, difficulty_level, reading_time (int).
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');
        $openaiKey   = env('OPENAI_API_KEY', '');

        try {
            if (!empty($geminiKey) && $guard->canCall('medium')) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.65, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
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
        } catch (\Exception) {}

        try {
            $claude = app(\App\Services\ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 3000, 0.65);
                if (!empty($data['content'])) {
                    Log::info('FetchArxivPapers: generado con Claude (fallback).');
                    return $data;
                }
            }
        } catch (\Exception) {}

        try {
            if (!empty($openaiKey)) {
                $r = Http::timeout(60)->withToken($openaiKey)->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => env('OPENAI_MODEL_NAME', 'gpt-4-turbo'),
                    'temperature' => 0.65,
                    'max_tokens'  => 3000,
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
        } catch (\Exception) {}

        return [];
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (ConocIaPaper::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
