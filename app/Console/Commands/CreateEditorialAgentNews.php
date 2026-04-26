<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\EditorialAgentTask;
use App\Services\GeminiQuotaGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateEditorialAgentNews extends Command
{
    protected $signature = 'editorial-agent:create-news
        {--topic= : Tema o instrucción editorial}
        {--category=inteligencia-artificial : Slug de categoría sugerida}
        {--days=2 : Buscar fuentes de los últimos N días}
        {--priority=high : Prioridad de la propuesta}';

    protected $description = 'Busca fuentes recientes y crea una propuesta de noticia no publicada para aprobar en el agente editorial.';

    public function handle(): int
    {
        if (!Schema::hasTable('editorial_agent_tasks')) {
            $this->error('La tabla editorial_agent_tasks no existe. Ejecuta migraciones primero.');

            return self::FAILURE;
        }

        $apiKey = config('services.gemini.api_key');
        if (blank($apiKey)) {
            $this->error('GEMINI_API_KEY no está configurada.');

            return self::FAILURE;
        }

        $topic = trim((string) $this->option('topic'));
        if ($topic === '') {
            $this->error('Debes indicar --topic="tema de la noticia".');

            return self::FAILURE;
        }

        $guard = app(GeminiQuotaGuard::class);
        if (!$guard->canCall('high')) {
            $this->warn('Gemini sin cuota suficiente. ' . $guard->summary());

            return self::SUCCESS;
        }

        $categorySlug = (string) $this->option('category');
        $days = max(1, (int) $this->option('days'));

        $this->info("Buscando fuentes para: {$topic}");
        $draft = $this->createDraftWithGemini($topic, $categorySlug, $days, $apiKey, $guard);

        if (!$draft) {
            $this->error('No se pudo generar una propuesta confiable.');

            return self::FAILURE;
        }

        $category = Category::where('slug', $categorySlug)->first();
        $dedupeKey = 'create_news:' . sha1(Str::lower($topic) . '|' . now()->format('Y-m-d'));

        $task = EditorialAgentTask::firstOrCreate(
            ['dedupe_key' => $dedupeKey],
            [
                'task_type' => 'news_draft',
                'priority' => (string) $this->option('priority'),
                'status' => 'pending',
                'title' => 'Borrador de noticia: ' . $draft['title'],
                'summary' => $draft['excerpt'] ?? $draft['summary'] ?? null,
                'suggested_action' => 'Revisar fuentes, ajustar enfoque editorial y aprobar para convertir en noticia borrador.',
                'source_urls' => collect($draft['sources'] ?? [])->pluck('url')->filter()->values()->all(),
                'payload' => [
                    'topic' => $topic,
                    'category_slug' => $categorySlug,
                    'category_id' => $category?->id,
                    'news_draft' => $draft,
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]
        );

        if (!$task->wasRecentlyCreated) {
            $this->warn('Ya existía una propuesta para este tema hoy.');
        }

        $this->info("Propuesta creada: #{$task->id}");
        $this->line(route('admin.editorial-agent.show', $task));

        return self::SUCCESS;
    }

    private function createDraftWithGemini(
        string $topic,
        string $categorySlug,
        int $days,
        string $apiKey,
        GeminiQuotaGuard $guard
    ): ?array {
        $model = config('services.gemini.model', 'gemini-2.0-flash');
        $today = now()->format('d/m/Y');
        $since = now()->subDays($days)->format('d/m/Y');

        $prompt = <<<PROMPT
Usa Google Search para investigar una noticia reciente sobre este tema: "{$topic}".

Condiciones:
- Busca fuentes publicadas entre {$since} y {$today}, o lo más reciente disponible si no hay resultados exactos.
- Prioriza fuentes originales, comunicados oficiales, papers, blogs técnicos de empresas, universidades o medios confiables.
- No inventes datos. Si algo no está confirmado, dilo como contexto y no como hecho.
- Redacta para ConocIA, en español de Chile, con enfoque claro para lectores de inteligencia artificial.
- La noticia debe quedar como borrador editorial, no como copia de una fuente.

Devuelve SOLO JSON válido con esta estructura:
{
  "title": "Título SEO en español, máximo 95 caracteres",
  "slug": "slug-sugerido",
  "excerpt": "Bajada de máximo 220 caracteres",
  "summary": "Resumen editorial de 2 a 3 frases",
  "content": "Artículo en HTML, 650 a 900 palabras, con <p>, <h2>, <blockquote> y <ul>",
  "keywords": "5 a 8 keywords separadas por coma",
  "source": "Fuente principal",
  "source_url": "URL principal",
  "sources": [
    {"title": "Nombre fuente", "url": "https://..."}
  ],
  "linkedin": "Texto listo para LinkedIn, sin enlace final",
  "x": "Texto para X de máximo 260 caracteres, sin enlace final"
}
PROMPT;

        $response = Http::timeout(90)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                'tools' => [['google_search' => (object) []]],
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.35, 'maxOutputTokens' => 8192],
            ]
        );

        $guard->record();

        if ($response->failed()) {
            $this->error('Gemini error: ' . $response->status() . ' — ' . $response->body());

            return null;
        }

        $parts = $response->json()['candidates'][0]['content']['parts'] ?? [];
        $raw = implode('', array_column($parts, 'text'));
        $draft = $this->extractJson($raw);

        if (!is_array($draft) || empty($draft['title']) || empty($draft['content'])) {
            $this->warn('Gemini no devolvió un JSON completo.');

            return null;
        }

        $draft['slug'] = Str::slug($draft['slug'] ?? $draft['title']);
        $draft['content'] = $this->stripMarkdownFences((string) $draft['content']);
        $draft['category_slug'] = $categorySlug;

        return $draft;
    }

    private function extractJson(string $text): ?array
    {
        $decoded = json_decode(trim($text), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $match)) {
            $decoded = json_decode(trim($match[1]), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        $start = strpos($text, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;

        for ($i = $start; $i < strlen($text); $i++) {
            $char = $text[$i];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $escaped = true;
                continue;
            }

            if ($char === '"') {
                $inString = !$inString;
                continue;
            }

            if ($inString) {
                continue;
            }

            if ($char === '{') {
                $depth++;
            }

            if ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    $candidate = substr($text, $start, $i - $start + 1);
                    $decoded = json_decode($candidate, true);

                    return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : null;
                }
            }
        }

        return null;
    }

    private function stripMarkdownFences(string $text): string
    {
        $text = trim($text);

        if (preg_match('/^```(?:html)?\s*\n?([\s\S]*?)\n?```\s*$/i', $text, $match)) {
            $text = trim($match[1]);
        }

        $text = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $text);
        $text = preg_replace('/<link\b[^>]*>/i', '', $text);
        $text = preg_replace('/\s+style\s*=\s*"[^"]*"/i', '', $text);
        $text = preg_replace("/\s+style\s*=\s*'[^']*'/i", '', $text);
        $text = preg_replace('/\s+class\s*=\s*"[^"]*"/i', '', $text);

        return trim($text);
    }
}
