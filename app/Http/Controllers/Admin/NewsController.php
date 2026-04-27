<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use App\Support\AdminDashboardCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Mostrar el listado de noticias
     */
    public function index(Request $request)
    {
        $query = News::query()->with(['category', 'author']);
        $sortWindow = (int) $request->input('analytics_window', 7);
        $sortWindow = in_array($sortWindow, [1, 7, 30], true) ? $sortWindow : 7;
        
        // Añadir relación de tags si existe
        if (method_exists(News::class, 'tags')) {
            $query->with('tags');
        }
        
        // Filtrado por búsqueda
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filtrado por categoría
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }
        
        // Filtrado por estado
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $orderBy = $request->input('order_by', 'created_at');
        $orderDir = $request->input('order_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $supportedOrderColumns = ['created_at', 'published_at', 'title', 'views'];

        if ($orderBy === 'recent_views' && Schema::hasTable('news_views_stats')) {
            $recentViewsSubquery = DB::table('news_views_stats')
                ->selectRaw('news_id, SUM(views) as recent_views')
                ->where('view_date', '>=', now()->subDays($sortWindow - 1)->toDateString())
                ->groupBy('news_id');

            $query->leftJoinSub($recentViewsSubquery, 'recent_view_stats', function ($join) {
                $join->on('news.id', '=', 'recent_view_stats.news_id');
            })
                ->select('news.*')
                ->selectRaw('COALESCE(recent_view_stats.recent_views, 0) as recent_views')
                ->orderBy('recent_views', $orderDir)
                ->orderByDesc('news.created_at');
        } else {
            if (!in_array($orderBy, $supportedOrderColumns, true)) {
                $orderBy = 'created_at';
            }

            $query->orderBy($orderBy, $orderDir);
        }
        
        $news = $query->paginate(15);
        $categories = Category::all();
        
        // Obtener etiquetas si la relación existe
        $tags = [];
        if (method_exists(News::class, 'tags')) {
            $tags = Tag::orderBy('name')->get();
        }
        
        return view('admin.news.index', compact('news', 'categories', 'tags', 'sortWindow'));
    }

    /**
     * Mostrar el formulario para crear una noticia
     */
    public function create()
    {
        return view('admin.news.create', $this->getFormData());
    }

    /**
     * Almacenar una nueva noticia
     */
    public function store(Request $request)
    {
        $validated = $this->validateNews($request);
        $validated = $this->prepareNewsPayload($validated, $request);
        $validated['user_id'] = Auth::id();

        $news = News::create($validated);
        $this->syncTags($news, $validated);
        
        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia creada exitosamente.');
    }

    /**
     * Mostrar una noticia específica
     */
    public function show(News $news)
    {
        // Cargar relaciones
        $news->load(['category', 'author']);
        
        // Cargar etiquetas si existe la relación
        if (method_exists($news, 'tags')) {
            $news->load('tags');
        }
        
        // Cargar comentarios si existe la relación
        if (method_exists($news, 'comments')) {
            $news->load('comments');
        }
        
        return view('admin.news.show', compact('news'));
    }

    /**
     * Mostrar el formulario para editar una noticia
     */
    public function edit(News $news)
    {
        if (method_exists(News::class, 'tags')) {
            $news->load('tags');
        }

        return view('admin.news.edit', array_merge(['news' => $news], $this->getFormData()));
    }

    /**
     * Actualizar una noticia en la base de datos
     */
    public function update(Request $request, News $news)
    {
        $validated = $this->validateNews($request, $news);
        $validated = $this->prepareNewsPayload($validated, $request, $news);

        $news->update($validated);
        $this->syncTags($news, $validated);
        
        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia actualizada exitosamente.');
    }

    /**
     * Eliminar una noticia
     */
    public function destroy(News $news)
    {
        // Eliminar imagen si existe
        if ($news->image && str_starts_with($news->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $news->image));
        }
        
        // Eliminar noticia
        $news->delete();
        
        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia eliminada exitosamente.');
    }

    /**
     * Realizar acciones en lote sobre múltiples noticias
     */
    public function bulkActions(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:publish,draft,delete,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id'
        ]);
        
        $count = 0;
        
        switch ($validated['action']) {
            case 'publish':
                $count = News::whereIn('id', $validated['ids'])
                    ->update(['status' => 'published', 'published_at' => now()]);
                $message = "{$count} noticias publicadas.";
                break;
                
            case 'draft':
                $count = News::whereIn('id', $validated['ids'])
                    ->update(['status' => 'draft']);
                $message = "{$count} noticias movidas a borrador.";
                break;
                
            case 'delete':
                // Primero obtener noticias para eliminar sus imágenes
                $newsToDelete = News::whereIn('id', $validated['ids'])->get();
                
                foreach ($newsToDelete as $news) {
                    if ($news->image && str_starts_with($news->image, 'storage/')) {
                        Storage::disk('public')->delete(str_replace('storage/', '', $news->image));
                    }
                }
                
                $count = News::whereIn('id', $validated['ids'])->delete();
                $message = "{$count} noticias eliminadas.";
                break;

            case 'feature': // Agregar este caso
                $count = News::whereIn('id', $validated['ids'])
                    ->update(['featured' => true]);
                $message = "{$count} noticias destacadas.";
                break;
                
            case 'unfeature': // Agregar este caso
                $count = News::whereIn('id', $validated['ids'])
                    ->update(['featured' => false]);
                $message = "{$count} noticias quitadas de destacados.";
                break;
        }

        News::clearHomeCache();
        AdminDashboardCache::clear();
        
        return redirect()->route('admin.news.index')
            ->with('success', $message);
    }

    /**
     * Exportar noticias a CSV
     */
    public function export()
    {
        $fileName = 'noticias_' . date('Y-m-d') . '.csv';
        
        $news = News::with(['category', 'author'])
            ->select('id', 'title', 'slug', 'summary', 'content', 'category_id', 'user_id', 'status', 'published_at', 'created_at')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];
        
        $callback = function() use ($news) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para caracteres UTF-8
            
            // Encabezados
            fputcsv($file, ['ID', 'Título', 'Slug', 'Resumen', 'Categoría', 'Autor', 'Estado', 'Fecha de Publicación', 'Fecha de Creación', 'Etiquetas']);
            
            foreach ($news as $article) {
                $tags = '';
                if (method_exists($article, 'tags') && $article->tags) {
                    $tags = $article->tags->pluck('name')->implode(', ');
                }
                
                $row = [
                    $article->id,
                    $article->title,
                    $article->slug,
                    $article->summary,
                    $article->category->name ?? 'Sin categoría',
                    is_object($article->author)
                        ? ($article->author->name ?? 'Sin autor')
                        : ($article->author ?: 'Sin autor'),
                    $article->status,
                    $article->published_at,
                    $article->created_at,
                    $tags
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Cargar imagen desde el editor de contenido
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);
        
        $path = $request->file('image')->store('news/content', 'public');
        $url = asset('storage/' . $path);
        
        return response()->json(['location' => $url]);
    }

    /**
     * Vista previa de una noticia antes de publicarla
     */
    public function preview(News $news)
    {
        return view('news.show', compact('news'));
    }

    private function validateNews(Request $request, ?News $news = null): array
    {
        $this->normalizeLegacyFields($request);

        $slugRule = 'nullable|string|unique:news,slug';

        if ($news) {
            $slugRule .= ',' . $news->id;
        }

        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => $slugRule,
            'summary' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured' => 'nullable|boolean',
            'access_level' => 'nullable|in:free,premium',
            'is_premium' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
        ]);
    }

    private function prepareNewsPayload(array $validated, Request $request, ?News $news = null): array
    {
        $validated['featured'] = $request->boolean('featured');
        $validated['access_level'] = $validated['access_level'] ?? 'free';
        $validated['is_premium'] = $request->boolean('is_premium') || $validated['access_level'] === 'premium';
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['summary'] = trim($validated['summary']);
        $validated['excerpt'] = trim($validated['excerpt'] ?? '') !== ''
            ? trim($validated['excerpt'])
            : Str::limit(strip_tags($validated['summary']), 500);

        if ($request->boolean('remove_image') && $news && $news->image && str_starts_with($news->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $news->image));
            $validated['image'] = null;
        }

        $imageField = $request->hasFile('featured_image') ? 'featured_image' : ($request->hasFile('image') ? 'image' : null);

        if ($imageField !== null) {
            if ($news && $news->image && str_starts_with($news->image, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $news->image));
            }

            $path = $request->file($imageField)->store('news', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        if (
            $validated['status'] === 'published'
            && empty($validated['published_at'])
            && (!$news || !$news->published_at)
        ) {
            $validated['published_at'] = now();
        }

        return $validated;
    }

    private function normalizeLegacyFields(Request $request): void
    {
        $summary = $request->input('summary');
        $excerpt = $request->input('excerpt');

        if (blank($summary) && filled($excerpt)) {
            $summary = $excerpt;
        }

        if (blank($excerpt) && filled($summary)) {
            $excerpt = Str::limit(strip_tags($summary), 500);
        }

        $status = $request->input('status');
        if (blank($status)) {
            $status = $request->boolean('is_published') ? 'published' : 'draft';
        }

        $featured = $request->has('featured')
            ? $request->boolean('featured')
            : $request->boolean('is_featured');

        $request->merge([
            'summary' => $summary,
            'excerpt' => $excerpt,
            'status' => $status,
            'featured' => $featured,
            'remove_image' => $request->boolean('remove_image'),
        ]);
    }

    private function syncTags(News $news, array $validated): void
    {
        if (method_exists($news, 'tags') && array_key_exists('tags', $validated)) {
            $news->tags()->sync($validated['tags'] ?? []);
        }
    }

    private function getFormData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => method_exists(News::class, 'tags')
                ? Tag::orderBy('name')->get()
                : [],
        ];
    }
}
