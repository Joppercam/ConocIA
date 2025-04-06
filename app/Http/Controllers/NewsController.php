<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsHistoric;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ImageHelper; // Ya existe este helper en el sistema

class NewsController extends Controller
{
    /**
     * Mostrar listado de noticias
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Cachear los resultados para mejorar rendimiento
        $news = Cache::remember('news_index_list', 1800, function () {
            return News::with(['category', 'author'])
                ->where('status', 'published') // Aseguramos mostrar solo noticias publicadas
                ->orderBy('published_at', 'desc')
                ->paginate(10);
        });
            
        // Verificar si hay noticias (mantener log para depuración)
        if ($news->isEmpty()) {
            Log::info('No se encontraron noticias en el método index');
        }
            
        // Obtener todas las categorías (cachear por más tiempo ya que cambian menos)
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::withCount(['news' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('news_count', 'desc')
            ->get();
        });
        
        // Obtener artículos más leídos (cacheados)
        $mostReadArticles = Cache::remember('most_read_articles', 1800, function () {
            return News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        });
            
        return view('news.index', compact('news', 'categories', 'mostReadArticles'));
    }

    /**
     * Muestra las noticias de una categoría específica
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function category($slug)
    {
        // Buscar la categoría por su slug (cachear resultado)
        $category = Cache::remember('category_' . $slug, 3600, function () use ($slug) {
            return Category::where('slug', $slug)->firstOrFail();
        });
        
        // Obtener noticias de esta categoría con caché dependiente
        $news = Cache::remember('news_by_category_' . $category->id, 1800, function () use ($category) {
            return News::where('category_id', $category->id)
                ->where('status', 'published')
                ->with('author')
                ->latest('published_at')
                ->paginate(10);
        });
        
        // Obtener todas las categorías para el menú lateral
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::withCount(['news' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('news_count', 'desc')
            ->get();
        });
        
        // Obtener artículos más leídos (cacheados)
        $mostReadArticles = Cache::remember('most_read_articles', 1800, function () {
            return News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        });
        
        // Pasar la categoría actual para destacarla en la UI
        return view('news.index', compact('news', 'categories', 'category', 'mostReadArticles'));
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
        // No cacheamos la noticia individual para mantener precisos los contadores de vistas
        // Intentar obtener la noticia de la tabla principal primero
        $article = News::where('slug', $slug)
            ->with(['category', 'tags', 'author', 'comments' => function($query) {
                $query->where('status', 'approved')
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc');
            }])
            ->first();
        
        // Si no se encuentra en la tabla principal, buscar en la tabla histórica
        if (!$article) {
            $article = NewsHistoric::where('slug', 'like', $slug . '%')
                ->with(['category', 'author', 'comments' => function($query) {
                    $query->where('status', 'approved')
                        ->whereNull('parent_id')
                        ->orderBy('created_at', 'desc');
                }])
                ->first();
                
            if (!$article) {
                abort(404);
            }
        }
        
        // Incrementar contador de vistas para noticias activas (solo para News no para NewsHistoric)
        if ($article instanceof News && $article->status === 'published') {
            $this->incrementArticleViews($article);
        }
        
        // Obtener artículos relacionados (cachear basado en artículo actual)
        $cacheKey = 'related_articles_' . $article->id;
        $relatedArticles = Cache::remember($cacheKey, 1800, function () use ($article) {
            return News::where('id', '!=', $article->id)
                ->with('category')
                ->where(function ($query) use ($article) {
                    // Relacionados por categoría
                    if ($article->category) {
                        $query->where('category_id', $article->category->id);
                    }
                    
                    // O por etiquetas si están disponibles
                    if (isset($article->tags) && !empty($article->tags)) {
                        // Si es una colección de etiquetas
                        if (is_object($article->tags) && method_exists($article->tags, 'pluck')) {
                            $tagIds = $article->tags->pluck('id')->toArray();
                            $query->orWhereHas('tags', function($q) use ($tagIds) {
                                $q->whereIn('tags.id', $tagIds);
                            });
                        } 
                        // Si es un string de etiquetas (formato legacy)
                        else if (is_string($article->tags)) {
                            $tagArray = explode(',', $article->tags);
                            $query->orWhere(function($q) use ($tagArray) {
                                foreach ($tagArray as $tag) {
                                    $q->orWhere('tags', 'like', '%' . trim($tag) . '%');
                                }
                            });
                        }
                    }
                })
                ->where('status', 'published')
                ->latest('published_at')
                ->take(6)
                ->get();
        });
        
        // Obtener artículos más leídos (cacheados)
        $mostReadArticles = Cache::remember('most_read_articles', 1800, function () {
            return News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        });
            
        // Obtener etiquetas populares (cacheadas)
        $popularTags = Cache::remember('popular_tags', 3600, function () {
            return Tag::select('tags.*')
                ->join('news_tag', 'tags.id', '=', 'news_tag.tag_id')
                ->join('news', 'news.id', '=', 'news_tag.news_id')
                ->where('news.status', 'published')
                ->groupBy('tags.id')
                ->selectRaw('COUNT(news_tag.news_id) as news_count')
                ->orderByDesc('news_count')
                ->limit(10)
                ->get();
        });
        
        // Usar el ImageHelper existente
        $getImageUrl = function($imagePath, $type = 'news', $size = 'large') {
            // Extraemos solo el nombre del archivo si es una ruta completa
            if ($imagePath && (Str::startsWith($imagePath, 'storage/') || Str::startsWith($imagePath, '/storage/'))) {
                $imagePath = basename($imagePath);
            }
            return ImageHelper::getImageUrl($imagePath, $type, $size);
        };
        
        // Función para obtener el estilo de una categoría
        // Estas funciones podrían trasladarse a un CategoryHelper en el futuro
        $getCategoryStyle = function($category) {
            if (!$category || !isset($category->color)) {
                return 'background-color: var(--primary-color);';
            }
            
            return 'background-color: ' . $category->color . ';';
        };
        
        // Función para obtener el icono de una categoría
        $getCategoryIcon = function($category) {
            if (!$category || !isset($category->icon)) {
                return 'fa-tag';
            }
            
            return $category->icon;
        };
        
        return view('news.show', compact(
            'article', 
            'relatedArticles', 
            'mostReadArticles', 
            'popularTags'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }
    
    /**
     * Incrementa el contador de vistas de un artículo
     * Implementa una protección contra incrementos múltiples 
     * y registra estadísticas diarias
     *
     * @param \App\Models\News $article
     * @return void
     */
    private function incrementArticleViews($article)
    {
        // Crear un identificador único para esta noticia
        $viewKey = 'news_viewed_' . $article->id;
        
        // Verificar si ya se ha visto esta noticia en la sesión actual
        if (!session()->has($viewKey)) {
            // Incrementar contador de vistas usando transacción para evitar race conditions
            DB::transaction(function() use ($article) {
                DB::table('news')
                    ->where('id', $article->id)
                    ->increment('views');
                    
                // Registrar estadísticas por día para análisis
                try {
                        // Verificar si el registro existe
                        $exists = DB::table('news_views_stats')
                        ->where('news_id', $article->id)
                        ->where('view_date', now()->format('Y-m-d'))
                        ->exists();
                        
                    if ($exists) {
                        // Si existe, actualizar incrementando las vistas
                        DB::table('news_views_stats')
                            ->where('news_id', $article->id)
                            ->where('view_date', now()->format('Y-m-d'))
                            ->update([
                                'views' => DB::raw('views + 1'),
                                'updated_at' => now()
                            ]);
                    } else {
                        // Si no existe, insertar un nuevo registro con 1 vista
                        DB::table('news_views_stats')
                            ->insert([
                                'news_id' => $article->id,
                                'view_date' => now()->format('Y-m-d'),
                                'views' => 1,
                                'updated_at' => now()
                            ]);
                    }
                } catch (\Exception $e) {
                    // Log error de forma silenciosa
                    Log::error('Error al actualizar estadísticas de vistas: ' . $e->getMessage());
                }
            });
            
            // Marcar como vista en la sesión (dura hasta que expira la sesión)
            session()->put($viewKey, true);
            
            // Cookie para un seguimiento más duradero (previene múltiples conteos)
            $cookieKey = 'news_viewed_' . $article->id;
            if (!Cookie::has($cookieKey)) {
                Cookie::queue($cookieKey, true, 1440); // 24 horas en minutos
            }
        }
    }
    
    /**
     * Muestra las noticias de una etiqueta específica
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function tag($slug)
    {
        // Buscar la etiqueta por su slug
        $tag = Cache::remember('tag_' . $slug, 3600, function () use ($slug) {
            return Tag::where('slug', $slug)->firstOrFail();
        });
        
        // Obtener noticias con esta etiqueta
        $news = Cache::remember('news_by_tag_' . $tag->id, 1800, function () use ($tag) {
            return News::whereHas('tags', function($query) use ($tag) {
                    $query->where('tags.id', $tag->id);
                })
                ->where('status', 'published')
                ->with(['category', 'author'])
                ->latest('published_at')
                ->paginate(10);
        });
        
        // Obtener categorías para el sidebar
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::withCount(['news' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('news_count', 'desc')
            ->get();
        });
        
        // Obtener artículos más leídos
        $mostReadArticles = Cache::remember('most_read_articles', 1800, function () {
            return News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        });
        
        return view('news.tag', compact('news', 'tag', 'categories', 'mostReadArticles'));
    }
    
    /**
     * Muestra las últimas noticias archivadas por fecha
     * 
     * @param string $year
     * @param string $month (opcional)
     * @return \Illuminate\View\View
     */
    public function archive($year, $month = null)
    {
        $query = News::query()->where('status', 'published');
        
        // Filtrar por año
        $query->whereYear('published_at', $year);
        
        // Si se proporciona un mes, filtrar también por mes
        if ($month) {
            $query->whereMonth('published_at', $month);
        }
        
        // Ejecutar la consulta con paginación
        $news = $query->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(10);
            
        // Obtener todas las categorías para el menú lateral
        $categories = Cache::remember('all_categories', 3600, function () {
            return Category::withCount(['news' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('news_count', 'desc')
            ->get();
        });
        
        // Obtener artículos más leídos
        $mostReadArticles = Cache::remember('most_read_articles', 1800, function () {
            return News::with('category')
                ->where('status', 'published')
                ->orderBy('views', 'desc')
                ->take(5)
                ->get();
        });
        
        // Formatear el título del archivo según si incluye mes o solo año
        $archiveTitle = $month 
            ? date('F Y', strtotime("$year-$month-01")) 
            : "Año $year";
            
        return view('news.archive', compact(
            'news', 
            'categories', 
            'mostReadArticles', 
            'year', 
            'month', 
            'archiveTitle'
        ));
    }
}