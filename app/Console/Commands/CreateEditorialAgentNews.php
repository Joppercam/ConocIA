<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\EditorialAgentTask;
use App\Models\News;
use App\Services\GeminiQuotaGuard;
use App\Support\EditorialAgentLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
            EditorialAgentLogger::error('missing_tasks_table', 'La tabla editorial_agent_tasks no existe.');

            return self::FAILURE;
        }

        $apiKey = config('services.gemini.api_key');
        if (blank($apiKey)) {
            $this->error('GEMINI_API_KEY no está configurada.');
            EditorialAgentLogger::error('missing_gemini_key', 'GEMINI_API_KEY no está configurada.');

            return self::FAILURE;
        }

        $topic = trim((string) $this->option('topic'));
        if ($topic === '') {
            $this->error('Debes indicar --topic="tema de la noticia".');
            EditorialAgentLogger::warning('missing_topic', 'Se intentó crear noticia sin topic.');

            return self::FAILURE;
        }

        $guard = app(GeminiQuotaGuard::class);
        if (!$guard->canCall('high')) {
            $this->warn('Gemini sin cuota suficiente. ' . $guard->summary());
            EditorialAgentLogger::warning('quota_blocked', 'Gemini sin cuota suficiente.', [
                'topic' => $topic,
                'quota' => $guard->summary(),
            ]);

            return self::SUCCESS;
        }

        $categorySlug = (string) $this->option('category');
        $days = max(1, (int) $this->option('days'));
        $dedupeKey = 'create_news:' . sha1(Str::lower($topic) . '|' . now()->format('Y-m-d'));

        if (EditorialAgentTask::where('dedupe_key', $dedupeKey)->exists()) {
            $this->warn('Ya existe una tarea para este tema hoy. No se consumen tokens.');
            EditorialAgentLogger::info('duplicate_skipped', 'Ya existe una tarea para este tema hoy. No se consumen tokens.', [
                'topic' => $topic,
                'category' => $categorySlug,
            ]);

            return self::SUCCESS;
        }

        $this->info("Buscando fuentes para: {$topic}");
        EditorialAgentLogger::info('create_news_started', 'Buscando fuentes para crear noticia.', [
            'topic' => $topic,
            'category' => $categorySlug,
            'days' => $days,
        ]);
        $draft = $this->createDraftWithGemini($topic, $categorySlug, $days, $apiKey, $guard);

        if (!$draft) {
            $this->error('No se pudo generar una propuesta confiable.');
            EditorialAgentLogger::error('draft_failed', 'No se pudo generar una propuesta confiable.', [
                'topic' => $topic,
                'category' => $categorySlug,
            ]);

            return self::FAILURE;
        }

        $category = Category::where('slug', $categorySlug)->first();
        $autoPublish = $this->shouldAutoPublish($topic, $categorySlug);

        if ($autoPublish) {
            $news = $this->publishNews($draft, $categorySlug);

            $task = EditorialAgentTask::create([
                'dedupe_key' => $dedupeKey,
                'task_type' => 'published_review',
                'priority' => (string) $this->option('priority'),
                'status' => 'pending',
                'title' => 'Revisar publicación automática: ' . $news->title,
                'summary' => $draft['excerpt'] ?? $draft['summary'] ?? null,
                'suggested_action' => 'Revisar enfoque, fuentes, SEO, imagen y copy social. Si algo no calza, editar o despublicar.',
                'content_type' => 'noticia',
                'content_id' => $news->id,
                'content_url' => route('admin.news.edit', $news),
                'source_urls' => collect($draft['sources'] ?? [])->pluck('url')->filter()->values()->all(),
                'payload' => [
                    'topic' => $topic,
                    'category_slug' => $categorySlug,
                    'category_id' => $news->category_id,
                    'news_id' => $news->id,
                    'public_url' => route('news.show', $news),
                    'news_draft' => $draft,
                    'auto_published' => true,
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]);

            $this->info("Noticia publicada por agente: #{$news->id}");
            $this->line(route('news.show', $news));
            $this->info("Tarea de revisión creada: #{$task->id}");
            EditorialAgentLogger::info('news_auto_published', 'Noticia publicada automáticamente por el agente.', [
                'task_id' => $task->id,
                'content_id' => $news->id,
                'content_type' => 'noticia',
                'title' => $news->title,
                'public_url' => route('news.show', $news),
                'topic' => $topic,
            ]);

            return self::SUCCESS;
        }

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
        EditorialAgentLogger::info('news_draft_created', 'Borrador de noticia creado para revisión.', [
            'task_id' => $task->id,
            'topic' => $topic,
            'category' => $categorySlug,
            'title' => $draft['title'],
        ]);

        return self::SUCCESS;
    }

    private function shouldAutoPublish(string $topic, string $categorySlug): bool
    {
        if (!config('services.editorial_agent.auto_publish', false)) {
            return false;
        }

        if ($this->isSensitiveTopic($topic, $categorySlug)
            && !config('services.editorial_agent.auto_publish_sensitive', false)) {
            $this->info('Tema sensible detectado. Se deja como borrador pendiente.');
            EditorialAgentLogger::info('sensitive_topic_blocked', 'Tema sensible detectado. Se deja como borrador pendiente.', [
                'topic' => $topic,
                'category' => $categorySlug,
            ]);

            return false;
        }

        return true;
    }

    private function isSensitiveTopic(string $topic, string $categorySlug): bool
    {
        $haystack = Str::lower($topic . ' ' . $categorySlug);

        foreach (config('services.editorial_agent.sensitive_terms', []) as $term) {
            if (Str::contains($haystack, Str::lower($term))) {
                return true;
            }
        }

        return false;
    }

    private function publishNews(array $draft, string $categorySlug): News
    {
        $category = Category::firstOrCreate(
            ['slug' => $categorySlug],
            [
                'name' => Str::headline(str_replace('-', ' ', $categorySlug)),
                'description' => 'Contenido generado por el agente editorial de ConocIA',
                'color' => '4285F4',
                'icon' => 'fa-brain',
            ]
        );

        $content = (string) $draft['content'];

        $payload = [
            'title' => $draft['title'],
            'slug' => $this->uniqueNewsSlug($draft['slug'] ?? $draft['title']),
            'excerpt' => news_editorial_teaser($draft['excerpt'] ?? null, null, $content, 220),
            'content' => $content,
            'category_id' => $category->id,
            'views' => 0,
            'featured' => false,
        ];

        $optionalColumns = [
            'summary' => $draft['summary'] ?? null,
            'keywords' => $draft['keywords'] ?? null,
            'user_id' => $this->editorId(),
            'author_id' => $this->editorId(),
            'status' => 'published',
            'source' => $draft['source'] ?? 'ConocIA',
            'source_url' => $draft['source_url'] ?? collect($draft['sources'] ?? [])->pluck('url')->filter()->first(),
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
            'published_at' => now(),
        ];

        foreach ($optionalColumns as $column => $value) {
            if (Schema::hasColumn('news', $column)) {
                $payload[$column] = $value;
            }
        }

        $news = News::create($payload);

        $forceFill = [];

        if (Schema::hasColumn('news', 'is_published')) {
            $forceFill['is_published'] = true;
        }

        if (Schema::hasColumn('news', 'author')) {
            $forceFill['author'] = 'Editor';
        }

        if (!empty($forceFill)) {
            $news->forceFill($forceFill)->save();
        }

        return $news;
    }

    private function editorId(): ?int
    {
        if (!Schema::hasTable('users')) {
            return null;
        }

        $roleIds = Schema::hasTable('roles')
            ? DB::table('roles')->whereIn('slug', ['editor', 'admin'])->pluck('id')
            : collect();

        $query = DB::table('users');

        if ($roleIds->isNotEmpty() && Schema::hasColumn('users', 'role_id')) {
            $query->whereIn('role_id', $roleIds);
        }

        $editorId = (clone $query)
            ->where(function ($query) {
                $query->where('email', 'editor@conocia.com')
                    ->orWhere('username', 'editor')
                    ->orWhere('name', 'Editor');
            })
            ->orderByRaw("CASE WHEN email = 'editor@conocia.com' THEN 0 ELSE 1 END")
            ->value('id');

        $fallbackId = (clone $query)->orderBy('id')->value('id');

        return $editorId ? (int) $editorId : ($fallbackId ? (int) $fallbackId : null);
    }

    private function uniqueNewsSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'noticia-agente-editorial';
        $slug = $base;
        $counter = 2;

        while (News::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
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

Devuelve SOLO JSON válido. No uses markdown. No envuelvas la respuesta en bloque de código.
El campo content debe ser un string JSON válido con HTML escapado correctamente.

Estructura exacta:
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
                'generationConfig' => [
                    'temperature' => 0.25,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ],
            ]
        );

        if ($response->failed()) {
            $this->error('Gemini error: ' . $response->status() . ' — ' . $response->body());
            EditorialAgentLogger::error('gemini_http_error', 'Gemini devolvió error HTTP.', [
                'topic' => $topic,
                'category' => $categorySlug,
                'status' => $response->status(),
                'body_sample' => Str::limit($response->body(), 900),
            ]);

            return null;
        }

        $guard->record();

        $parts = $response->json()['candidates'][0]['content']['parts'] ?? [];
        $finishReason = $response->json()['candidates'][0]['finishReason'] ?? null;
        $raw = implode('', array_column($parts, 'text'));
        $draft = $this->extractJson($raw);

        if (!is_array($draft) || empty($draft['title']) || empty($draft['content'])) {
            $this->warn('Gemini no devolvió un JSON completo.');
            EditorialAgentLogger::error('gemini_invalid_draft', 'Gemini no devolvió un borrador JSON completo.', [
                'topic' => $topic,
                'category' => $categorySlug,
                'finish_reason' => $finishReason,
                'has_title' => is_array($draft) && !empty($draft['title']),
                'has_content' => is_array($draft) && !empty($draft['content']),
                'raw_sample' => Str::limit($raw, 1200),
                'response_keys' => array_keys($response->json() ?? []),
            ]);

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
