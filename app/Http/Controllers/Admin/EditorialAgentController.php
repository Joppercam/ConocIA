<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EditorialAgentTask;
use App\Models\News;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EditorialAgentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        if (!Schema::hasTable('editorial_agent_tasks')) {
            return view('admin.editorial-agent.index', [
                'tasks' => new LengthAwarePaginator([], 0, 20),
                'counts' => ['pending' => 0, 'approved' => 0, 'completed' => 0, 'rejected' => 0],
                'types' => collect(),
                'status' => $status,
                'tableReady' => false,
            ]);
        }

        $tasks = EditorialAgentTask::query()
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->when($request->filled('type'), fn($query) => $query->where('task_type', $request->input('type')))
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending' => EditorialAgentTask::where('status', 'pending')->count(),
            'approved' => EditorialAgentTask::where('status', 'approved')->count(),
            'completed' => EditorialAgentTask::where('status', 'completed')->count(),
            'rejected' => EditorialAgentTask::where('status', 'rejected')->count(),
        ];

        $types = EditorialAgentTask::query()
            ->select('task_type')
            ->distinct()
            ->orderBy('task_type')
            ->pluck('task_type');

        return view('admin.editorial-agent.index', compact('tasks', 'counts', 'types', 'status') + ['tableReady' => true]);
    }

    public function show(EditorialAgentTask $task)
    {
        return view('admin.editorial-agent.show', compact('task'));
    }

    public function approve(Request $request, EditorialAgentTask $task)
    {
        $createdNews = null;

        if ($task->task_type === 'news_draft') {
            $createdNews = $this->createNewsDraftFromTask($task, $request);
        }

        $task->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
            'content_type' => $createdNews ? 'noticia' : $task->content_type,
            'content_id' => $createdNews?->id ?? $task->content_id,
            'content_url' => $createdNews ? route('admin.news.edit', $createdNews) : $task->content_url,
            'payload' => $createdNews
                ? array_merge($task->payload ?? [], ['created_news_id' => $createdNews->id])
                : $task->payload,
        ]);

        if ($createdNews) {
            return redirect()->route('admin.news.edit', $createdNews)->with('success', 'Propuesta aprobada y convertida en noticia borrador.');
        }

        return redirect()->route('admin.editorial-agent.index')->with('success', 'Propuesta aprobada. Quedó lista para ejecutar.');
    }

    public function complete(Request $request, EditorialAgentTask $task)
    {
        $task->update([
            'status' => 'completed',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes', $task->review_notes),
        ]);

        return redirect()->route('admin.editorial-agent.index', ['status' => 'approved'])->with('success', 'Tarea marcada como ejecutada.');
    }

    public function reject(Request $request, EditorialAgentTask $task)
    {
        $task->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        return redirect()->route('admin.editorial-agent.index')->with('success', 'Propuesta descartada.');
    }

    public function createNews()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.editorial-agent.create-news', compact('categories'));
    }

    public function storeNewsRequest(Request $request)
    {
        $data = $request->validate([
            'topic' => 'required|string|max:220',
            'category_slug' => 'nullable|string|max:120',
            'priority' => 'required|in:high,medium,low',
            'notes' => 'nullable|string|max:2000',
        ]);

        $dedupeKey = 'news_request:' . sha1(Str::lower($data['topic']) . '|' . now()->format('Y-m-d-H'));

        EditorialAgentTask::firstOrCreate(
            ['dedupe_key' => $dedupeKey],
            [
                'task_type' => 'news_request',
                'priority' => $data['priority'],
                'status' => 'pending',
                'title' => 'Solicitud de noticia: ' . $data['topic'],
                'summary' => 'Solicitud manual para que el agente cree una noticia no publicada.',
                'suggested_action' => 'Ejecutar el comando editorial-agent:create-news con este tema y revisar el borrador generado.',
                'payload' => [
                    'topic' => $data['topic'],
                    'category_slug' => $data['category_slug'] ?: 'inteligencia-artificial',
                    'notes' => $data['notes'] ?? null,
                    'command' => sprintf(
                        'php artisan editorial-agent:create-news --topic="%s" --category=%s',
                        str_replace('"', '\"', $data['topic']),
                        $data['category_slug'] ?: 'inteligencia-artificial'
                    ),
                ],
            ]
        );

        return redirect()->route('admin.editorial-agent.index')->with('success', 'Solicitud creada. El agente ya la muestra como pendiente.');
    }

    private function createNewsDraftFromTask(EditorialAgentTask $task, Request $request): ?News
    {
        $payload = $task->payload ?? [];

        if (!empty($payload['created_news_id'])) {
            return News::find($payload['created_news_id']);
        }

        $draft = $payload['news_draft'] ?? null;
        if (!is_array($draft) || empty($draft['title']) || empty($draft['content'])) {
            return null;
        }

        $category = null;
        if (!empty($payload['category_id'])) {
            $category = Category::find($payload['category_id']);
        }

        if (!$category && !empty($payload['category_slug'])) {
            $category = Category::where('slug', $payload['category_slug'])->first();
        }

        $category ??= Category::firstOrCreate(
            ['slug' => 'inteligencia-artificial'],
            [
                'name' => 'Inteligencia Artificial',
                'description' => 'Noticias sobre Inteligencia Artificial',
                'color' => '4285F4',
                'icon' => 'fa-brain',
            ]
        );

        $slug = $this->uniqueNewsSlug($draft['slug'] ?? $draft['title']);
        $content = (string) $draft['content'];

        $news = News::create([
            'title' => $draft['title'],
            'slug' => $slug,
            'excerpt' => $draft['excerpt'] ?? Str::limit(strip_tags($content), 220),
            'summary' => $draft['summary'] ?? null,
            'content' => $content,
            'keywords' => $draft['keywords'] ?? null,
            'category_id' => $category->id,
            'user_id' => $request->user()->id,
            'author_id' => $request->user()->id,
            'views' => 0,
            'status' => 'draft',
            'featured' => false,
            'source' => $draft['source'] ?? 'ConocIA',
            'source_url' => $draft['source_url'] ?? collect($draft['sources'] ?? [])->pluck('url')->filter()->first(),
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
            'published_at' => null,
        ]);

        if (Schema::hasColumn('news', 'is_published')) {
            $news->forceFill(['is_published' => false])->save();
        }

        return $news;
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
}
