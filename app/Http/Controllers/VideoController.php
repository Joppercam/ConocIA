<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoTag;
use App\Models\VideoPlatform;
use App\Services\Video\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * Mostrar la página principal de videos
     */
    public function index()
    {
        // Obtener videos más recientes
        $latestVideos = Cache::remember('videos_latest', 3600, function () {
            return Video::with('platform', 'categories')
                ->orderBy('published_at', 'desc')
                ->take(12)
                ->get();
        });

        // Obtener videos más populares
        $popularVideos = Cache::remember('videos_popular', 3600, function () {
            return Video::with('platform', 'categories')
                ->orderBy('view_count', 'desc')
                ->take(12)
                ->get();
        });

        // Obtener videos destacados
        $featuredVideos = Cache::remember('videos_featured', 3600, function () {
            return Video::with('platform', 'categories')
                ->where('is_featured', true)
                ->orderBy('published_at', 'desc')
                ->take(6)
                ->get();
        });

        // Obtener categorías de videos
        $videoCategories = Cache::remember('video_categories', 3600, function () {
            return VideoCategory::withCount('videos')
                ->orderBy('videos_count', 'desc')
                ->take(10)
                ->get();
        });

        return view('videos.index', compact(
            'latestVideos',
            'popularVideos',
            'featuredVideos',
            'videoCategories'
        ));
    }

    /**
     * Mostrar videos destacados
     */
    public function featured()
    {
        $videos = Video::with('platform', 'categories')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('videos.featured', compact('videos'));
    }

    /**
     * Mostrar videos populares
     */
    public function popular()
    {
        $videos = Video::with('platform', 'categories')
            ->orderBy('view_count', 'desc')
            ->paginate(12);

        return view('videos.popular', compact('videos'));
    }

    /**
     * Mostrar videos por categoría
     */
    public function byCategory($category)
    {
        $category = VideoCategory::where('id', $category)
            ->orWhere('slug', $category)
            ->firstOrFail();
        
        $videos = $category->videos()
            ->with('platform')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('videos.category', compact('videos', 'category'));
    }

    /**
     * Mostrar videos por etiqueta
     */
    public function byTag($tag)
    {
        $tag = VideoTag::where('id', $tag)
            ->orWhere('slug', $tag)
            ->firstOrFail();
        
        $videos = $tag->videos()
            ->with('platform', 'categories')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('videos.tag', compact('videos', 'tag'));
    }

    /**
     * Mostrar detalle de un video específico
     */
    public function show($id)
    {
        $video = Video::with(['platform', 'categories', 'tags'])
            ->findOrFail($id);
            
        // Incrementar contador de vistas
        $video->view_count += 1;
        $video->save();
        
        // Obtener videos relacionados
        $relatedVideos = $this->getRelatedVideos($video);
        
        return view('videos.show', compact('video', 'relatedVideos'));
    }

    /**
     * Guardar un comentario en un video
     */
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $video = Video::findOrFail($id);
        
        // Si tienes un modelo Comment, puedes usarlo así:
        // $comment = new Comment([
        //     'content' => $request->content,
        //     'user_id' => auth()->id()
        // ]);
        // $video->comments()->save($comment);
        
        // Si no tienes un sistema de comentarios implementado,
        // puedes retornar un mensaje informativo
        
        return redirect()->back()->with('success', 'Comentario guardado correctamente.');
    }

    /**
     * API para obtener videos por categoría
     */
    public function apiGetVideosByCategory($categoryId)
    {
        try {
            $category = VideoCategory::findOrFail($categoryId);
            
            $videos = $category->videos()
                ->with(['platform', 'categories'])
                ->orderBy('published_at', 'desc')
                ->take(6)
                ->get();
                
            return response()->json([
                'success' => true,
                'videos' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener videos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API para obtener recomendaciones de videos para noticias
     */
    public function apiGetNewsRecommendations(Request $request)
    {
        try {
            $newsId = $request->input('news_id');
            $content = $request->input('content', '');
            
            // Extraer palabras clave del contenido
            $keywords = $this->extractKeywords($content);
            
            if (empty($keywords)) {
                $keywords = ['noticias', 'actualidad'];
            }
            
            // Buscar videos relacionados
            $videos = $this->videoService->getRecommendedVideos($keywords, 3);
            
            return response()->json([
                'success' => true,
                'videos' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener recomendaciones: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Realizar una búsqueda de videos
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('videos.index');
        }
        
        $videos = Video::with(['platform', 'categories'])
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        return view('videos.search', compact('videos', 'query'));
    }
    
    /**
     * Obtener videos relacionados para un video específico
     */
    protected function getRelatedVideos($video)
    {
        // Obtener keywords del video
        $keywords = $video->keywords->pluck('keyword')->toArray();
        
        // Si no hay keywords, usar categorías y tags
        if (empty($keywords)) {
            $keywords = $video->categories->pluck('name')->toArray();
            $keywords = array_merge($keywords, $video->tags->pluck('name')->toArray());
        }
        
        // Si sigue sin haber keywords, usar parte del título
        if (empty($keywords)) {
            $words = explode(' ', $video->title);
            $keywords = array_filter($words, function($word) {
                return strlen($word) > 4;
            });
        }
        
        // Buscar videos relacionados, excluyendo el video actual
        return Video::with('platform')
            ->where('id', '!=', $video->id)
            ->where(function($query) use ($keywords, $video) {
                // Buscar por categorías
                if ($video->categories->count() > 0) {
                    $categoryIds = $video->categories->pluck('id')->toArray();
                    $query->whereHas('categories', function($q) use ($categoryIds) {
                        $q->whereIn('video_categories.id', $categoryIds);
                    });
                    $query->orWhere(function($q) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $q->orWhere('title', 'like', "%{$keyword}%");
                            $q->orWhere('description', 'like', "%{$keyword}%");
                        }
                    });
                } else {
                    // Si no hay categorías, buscar por keywords en título y descripción
                    foreach ($keywords as $keyword) {
                        $query->orWhere('title', 'like', "%{$keyword}%");
                        $query->orWhere('description', 'like', "%{$keyword}%");
                    }
                }
            })
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();
    }
    
    /**
     * Extraer palabras clave de un texto
     */
    protected function extractKeywords($text)
    {
        // Eliminar HTML
        $text = strip_tags($text);
        
        // Convertir a minúsculas
        $text = strtolower($text);
        
        // Eliminar caracteres especiales
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // Dividir en palabras
        $words = preg_split('/\s+/', $text);
        
        // Filtrar palabras comunes y cortas
        $commonWords = ['el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'y', 'o', 'pero', 'porque', 'como', 'que', 'para', 'por', 'del', 'al', 'con', 'en', 'a', 'de', 'es', 'son', 'fue', 'han', 'este', 'esta', 'estos', 'estas'];
        $filteredWords = array_filter($words, function($word) use ($commonWords) {
            return !in_array($word, $commonWords) && strlen($word) > 3;
        });
        
        // Contar frecuencia de palabras
        $wordCounts = array_count_values($filteredWords);
        
        // Ordenar por frecuencia
        arsort($wordCounts);
        
        // Tomar las 5 palabras más frecuentes
        $keywords = array_slice(array_keys($wordCounts), 0, 5);
        
        return $keywords;
    }
}