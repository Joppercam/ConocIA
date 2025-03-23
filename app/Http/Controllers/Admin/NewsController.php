<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Ordenamiento
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);
        
        $news = $query->paginate(15);
        $categories = Category::all();
        
        // Obtener etiquetas si la relación existe
        $tags = [];
        if (method_exists(News::class, 'tags')) {
            $tags = Tag::orderBy('name')->get();
        }
        
        return view('admin.news.index', compact('news', 'categories', 'tags'));
    }

    /**
     * Mostrar el formulario para crear una noticia
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        
        // Obtener etiquetas si la relación existe
        $tags = [];
        if (method_exists(News::class, 'tags')) {
            $tags = Tag::orderBy('name')->get();
        }
        
        return view('admin.news.create', compact('categories', 'tags'));
    }

    /**
     * Almacenar una nueva noticia
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:news,slug',
            'summary' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured' => 'nullable|boolean',
        ]);
        
        // Establecer featured a false si no está presente en la solicitud
        $validated['featured'] = $request->has('featured');

        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Manejar la imagen destacada
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('news', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Configurar fecha de publicación
        if ($validated['status'] == 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        // Añadir autor
        $validated['user_id'] = Auth::id();
        
        // Crear noticia
        $news = News::create($validated);
        
        // Asociar etiquetas si existe la relación
        if (method_exists(News::class, 'tags') && isset($validated['tags'])) {
            $news->tags()->sync($validated['tags']);
        }
        
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
        $categories = Category::orderBy('name')->get();
        
        // Obtener etiquetas si la relación existe
        $tags = [];
        if (method_exists(News::class, 'tags')) {
            $tags = Tag::orderBy('name')->get();
            $news->load('tags');
        }
        
        return view('admin.news.edit', compact('news', 'categories', 'tags'));
    }

    /**
     * Actualizar una noticia en la base de datos
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:news,slug,' . $news->id,
            'summary' => 'required|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured' => 'nullable|boolean',
        ]);
        
        // Establecer featured a false si no está presente en la solicitud
        $validated['featured'] = $request->has('featured');

        // Generar slug si no se proporcionó
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Manejar la imagen destacada
        if ($request->hasFile('featured_image')) {
            // Eliminar imagen anterior si existe
            if ($news->featured_image) {
                Storage::disk('public')->delete($news->featured_image);
            }
            
            $path = $request->file('featured_image')->store('news', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Configurar fecha de publicación
        if ($validated['status'] == 'published' && empty($validated['published_at']) && !$news->published_at) {
            $validated['published_at'] = now();
        }
        
        // Actualizar noticia
        $news->update($validated);
        
        // Asociar etiquetas si existe la relación
        if (method_exists($news, 'tags') && isset($validated['tags'])) {
            $news->tags()->sync($validated['tags']);
        }
        
        return redirect()->route('admin.news.index')
            ->with('success', 'Noticia actualizada exitosamente.');
    }

    /**
     * Eliminar una noticia
     */
    public function destroy(News $news)
    {
        // Eliminar imagen si existe
        if ($news->featured_image) {
            Storage::disk('public')->delete($news->featured_image);
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
                    if ($news->featured_image) {
                        Storage::disk('public')->delete($news->featured_image);
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
                    $article->author->name ?? 'Sin autor',
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
}