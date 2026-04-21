<?php

namespace App\Console\Commands;

use App\Models\AiAgent;
use App\Services\ClaudeService;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FetchAiAgents extends Command
{
    protected $signature = 'agents:fetch
                            {--limit=10 : Máximo de agentes a importar por ejecución}
                            {--sync-stars : Actualizar contador de stars de GitHub para existentes}
                            {--dry-run   : Mostrar sin guardar}';

    protected $description = 'Importa agentes IA desde GitHub trending y fuentes curadas';

    protected array $githubTopics = [
        'ai-agent',
        'llm-agent',
        'autonomous-agent',
        'ai-assistant',
        'agentic-ai',
    ];

    public function handle(): int
    {
        $limit     = (int) $this->option('limit');
        $syncStars = $this->option('sync-stars');
        $dryRun    = $this->option('dry-run');
        $guard     = app(GeminiQuotaGuard::class);

        if ($syncStars) {
            $this->syncGithubStars($dryRun);
        }

        $this->info('Buscando agentes IA en GitHub...');
        $githubAgents = $this->fetchFromGitHub();
        $this->info('Repos de GitHub encontrados: ' . count($githubAgents));

        $this->info('Consultando IA para agentes curados...');
        $curatedAgents = $this->fetchCuratedFromAI($guard);

        $all = array_merge($curatedAgents, $githubAgents);

        if (empty($all)) {
            $this->warn('Sin agentes encontrados.');
            return Command::SUCCESS;
        }

        $imported = 0;
        foreach (array_slice($all, 0, $limit) as $a) {
            if (empty($a['name'])) continue;

            if (AiAgent::where('name', $a['name'])->exists()) {
                $this->line("  DUPL: {$a['name']}");
                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] {$a['name']} ({$a['category']})");
                $imported++;
                continue;
            }

            $slug = $this->uniqueSlug(Str::slug($a['name']));

            AiAgent::create([
                'name'             => $a['name'],
                'slug'             => $slug,
                'tagline'          => $a['tagline'] ?? null,
                'description'      => $a['description'] ?? null,
                'website_url'      => $a['website_url'] ?? null,
                'github_url'       => $a['github_url'] ?? null,
                'category'         => $a['category'] ?? 'general',
                'type'             => $a['type'] ?? 'open-source',
                'framework'        => $a['framework'] ?? null,
                'stars_github'     => $a['stars_github'] ?? null,
                'capabilities'     => $a['capabilities'] ?? null,
                'use_cases'        => $a['use_cases'] ?? null,
                'requires_api_key' => $a['requires_api_key'] ?? true,
                'has_free_tier'    => $a['has_free_tier'] ?? false,
                'pricing_model'    => $a['pricing_model'] ?? 'open-source',
                'source_url'       => $a['source_url'] ?? null,
                'featured'         => false,
                'active'           => true,
                'auto_generated'   => true,
                'last_synced_at'   => now(),
            ]);

            $this->info("  OK: {$a['name']}");
            $imported++;
        }

        $this->info("Total agentes importados: {$imported}");
        return Command::SUCCESS;
    }

    protected function fetchFromGitHub(): array
    {
        $token   = env('GITHUB_TOKEN', '');
        $headers = ['User-Agent' => 'ConocIA AgentBot/1.0', 'Accept' => 'application/vnd.github.v3+json'];
        if (!empty($token)) $headers['Authorization'] = "token {$token}";

        $agents = [];

        foreach ($this->githubTopics as $topic) {
            try {
                $r = Http::timeout(15)->withHeaders($headers)->get('https://api.github.com/search/repositories', [
                    'q'        => "topic:{$topic} stars:>500",
                    'sort'     => 'stars',
                    'order'    => 'desc',
                    'per_page' => 5,
                ]);

                if ($r->failed()) continue;

                foreach ($r->json('items', []) as $repo) {
                    $name = $repo['name'] ?? '';
                    if (empty($name)) continue;

                    $displayName = str_replace(['-', '_'], ' ', $name);
                    $displayName = ucwords($displayName);

                    $agents[] = [
                        'name'          => $displayName,
                        'tagline'       => Str::limit($repo['description'] ?? '', 120),
                        'description'   => null,
                        'github_url'    => $repo['html_url'] ?? null,
                        'website_url'   => $repo['homepage'] ?: null,
                        'category'      => 'general',
                        'type'          => 'open-source',
                        'framework'     => null,
                        'stars_github'  => $repo['stargazers_count'] ?? null,
                        'pricing_model' => 'open-source',
                        'has_free_tier' => true,
                        'requires_api_key' => true,
                        'source_url'    => $repo['html_url'] ?? null,
                    ];
                }

                usleep(500000); // 500ms entre requests a GitHub
            } catch (\Exception $e) {
                Log::warning("FetchAiAgents GitHub error ({$topic}): " . $e->getMessage());
            }
        }

        // Deduplicar por nombre
        $seen   = [];
        $unique = [];
        foreach ($agents as $a) {
            $key = Str::slug($a['name']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[]   = $a;
            }
        }

        return $unique;
    }

    protected function fetchCuratedFromAI(GeminiQuotaGuard $guard): array
    {
        if (!$guard->canCall('high')) return [];

        $today         = now()->format('Y-m-d');
        $existingNames = AiAgent::pluck('name')->implode(', ');

        $prompt = <<<PROMPT
Hoy es {$today}. Eres un experto en el ecosistema de agentes de IA.

Agentes que ya tenemos registrados: {$existingNames}

Lista los agentes IA más relevantes y usados actualmente que NO estén en nuestra lista. Incluye:
- Frameworks de agentes: LangGraph, CrewAI, AutoGen, MetaGPT, etc.
- Agentes especializados populares: Cursor, Devin, SWE-agent, etc.
- Herramientas de automatización con IA: n8n AI, Zapier AI, etc.
- Asistentes de IA con capacidades agénticas

Para cada uno devuelve un objeto JSON con:
- name: nombre del agente/framework
- tagline: descripción de 1 línea en español
- description: 2-3 oraciones en español explicando qué hace y para qué sirve
- website_url: URL oficial
- github_url: URL de GitHub si tiene repositorio público
- category: coding|research|productivity|automation|data-analysis|customer-service|creative|general
- type: open-source|closed|api
- framework: si está basado en otro framework (langchain, autogen, crewai, langgraph, custom, none)
- stars_github: número aproximado de stars de GitHub si tiene repo, sino null
- capabilities: array de 3-5 capacidades principales (strings en español)
- use_cases: array de 2-3 casos de uso principales (strings en español)
- requires_api_key: true|false
- has_free_tier: true|false
- pricing_model: free|freemium|paid|open-source

Devuelve SOLO un array JSON. Máximo 10 agentes.
PROMPT;

        $geminiKey   = config('services.gemini.api_key', '');
        $geminiModel = config('services.gemini.model', 'gemini-2.0-flash');

        try {
            if (!empty($geminiKey)) {
                $r = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}",
                    [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 3000, 'responseMimeType' => 'application/json'],
                    ]
                );

                if ($r->successful()) {
                    $raw  = $r->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    $data = json_decode($raw, true);
                    if (is_array($data)) {
                        $guard->record();
                        return isset($data[0]) ? $data : ($data['agents'] ?? []);
                    }
                }
            }
        } catch (\Exception) {}

        try {
            $claude = app(ClaudeService::class);
            if ($claude->isAvailable()) {
                $data = $claude->generateJson($prompt, 3000, 0.2);
                if (is_array($data) && !empty($data)) {
                    return isset($data[0]) ? $data : ($data['agents'] ?? []);
                }
            }
        } catch (\Exception) {}

        return [];
    }

    protected function syncGithubStars(bool $dryRun): void
    {
        $this->info('Sincronizando stars de GitHub...');

        $token   = env('GITHUB_TOKEN', '');
        $headers = ['User-Agent' => 'ConocIA AgentBot/1.0', 'Accept' => 'application/vnd.github.v3+json'];
        if (!empty($token)) $headers['Authorization'] = "token {$token}";

        $agents = AiAgent::whereNotNull('github_url')->get();

        foreach ($agents as $agent) {
            try {
                // Extraer owner/repo de la URL
                if (!preg_match('#github\.com/([^/]+/[^/]+)#', $agent->github_url, $m)) continue;
                $repo = rtrim($m[1], '/');

                $r = Http::timeout(10)->withHeaders($headers)->get("https://api.github.com/repos/{$repo}");
                if ($r->failed()) continue;

                $stars = $r->json('stargazers_count');
                if ($stars === null) continue;

                if ($dryRun) {
                    $this->line("  [dry-run] {$agent->name}: {$stars} stars");
                    continue;
                }

                $agent->update(['stars_github' => $stars, 'last_synced_at' => now()]);
                $this->line("  SYNC: {$agent->name} → {$stars} stars");

                usleep(300000);
            } catch (\Exception $e) {
                Log::warning("FetchAiAgents stars sync error ({$agent->name}): " . $e->getMessage());
            }
        }
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i    = 1;
        while (AiAgent::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
