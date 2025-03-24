<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class NewsController extends Controller
{
    public function index()
    {
        // Usa inRandomOrder() que es un método de Query Builder
        $news = News::latest()->paginate(10);
        
        // Obtener todas las categorías para el menú lateral
        $categories = Category::all();
        
        // Usar la vista específica de noticias
        return view('news.index', compact('news', 'categories'));
    }

   


    /**
     * Muestra las noticias de una categoría específica
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function category($slug)
    {
        // Buscar la categoría por su slug
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Obtener noticias de esta categoría
        $news = News::where('category_id', $category->id)
                    ->latest()
                    ->paginate(10);
        
        // Obtener todas las categorías para el menú lateral
        $categories = Category::all();
        
        // Pasar la categoría actual para destacarla en la UI si es necesario
        return view('news.index', compact('news', 'categories', 'category'));
    }


    /**
     * Redirecciona a la página específica de categoría en la sección de noticias
     * 
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function byCategory($slug)
    {
        return redirect()->route('news.category', $slug);
    }



   
    /**
     * Mostrar una noticia específica
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Obtener la noticia por su slug
        $article = News::where('slug', $slug)
            ->with(['category', 'tags', 'author', 'comments' => function($query) {
                $query->where('status', 'approved') // Solo comentarios aprobados
                      ->whereNull('parent_id') // Solo comentarios principales (no respuestas)
                      ->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();
            
        // IMPORTANTE: Incrementar contador de vistas directamente aquí
        // sin llamar a ningún método privado o protegido
        $this->incrementArticleViews($article);
        
        // Obtener artículos relacionados
        $relatedArticles = News::where('id', '!=', $article->id)
            ->with('category')
            ->where(function ($query) use ($article) {
                // Relacionados por categoría
                if ($article->category) {
                    $query->where('category_id', $article->category->id);
                }
                
                // O por etiquetas si están disponibles
                if ($article->tags && $article->tags->count() > 0) {
                    $tagIds = $article->tags->pluck('id')->toArray();
                    $query->orWhereHas('tags', function($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->published()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
            
        // Obtener artículos más leídos usando el contador real de vistas
        $mostReadArticles = News::with('category')
            ->published()
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();
            
        // Obtener etiquetas populares (versión más simple)
        $popularTags = Tag::select('tags.*')
            ->join('news_tag', 'tags.id', '=', 'news_tag.tag_id')
            ->groupBy('tags.id')
            ->selectRaw('COUNT(news_tag.news_id) as news_count')
            ->orderByDesc('news_count')
            ->limit(10)
            ->get();
            
        return view('news.show', compact(
            'article', 
            'relatedArticles', 
            'mostReadArticles', 
            'popularTags'
        ));
    }
    
    /**
     * Incrementa el contador de vistas de un artículo
     *
     * @param \App\Models\News $article
     * @return void
     */
    public function incrementArticleViews($article)
    {
        // Crear un identificador único para esta noticia
        $viewKey = 'news_viewed_' . $article->id;
        
        // Verificar si ya se ha visto esta noticia en la sesión actual
        if (!session()->has($viewKey)) {
            // Incrementar contador de vistas directamente con DB para evitar race conditions
            DB::table('news')->where('id', $article->id)->increment('views');
            
            // Marcar como vista en la sesión (dura hasta que expira la sesión)
            session()->put($viewKey, true);
            
            // Opcional: También podemos usar una cookie para un seguimiento más duradero
            $cookieKey = 'news_viewed_' . $article->id;
            if (!Cookie::has($cookieKey)) {
                Cookie::queue($cookieKey, true, 1440); // 24 horas en minutos
            }
            
            // Registrar estadísticas por día para análisis
            try {
                DB::table('news_views_stats')->updateOrInsert(
                    [
                        'news_id' => $article->id,
                        'view_date' => now()->format('Y-m-d')
                    ],
                    [
                        'views' => DB::raw('views + 1'),
                        'updated_at' => now()
                    ]
                );
            } catch (\Exception $e) {
                // Ignorar errores de la tabla de estadísticas si aún no existe
                // La migración debería crearla, pero esto evita errores en producción
            }
        }
    }


}