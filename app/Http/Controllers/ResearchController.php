<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ImageHelper;
use App\Helpers\CategoryHelper;

class ResearchController extends Controller
{
    /**
     * Tiempo de caché en segundos (1 hora)
     */
    protected const CACHE_TIME = 3600;
    
    /**
     * Display a listing of research articles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Usar una única clave de caché para todos los datos de la página
        $viewData = Cache::remember('research_page_data', self::CACHE_TIME, function () {
            // Obtener artículos de investigación PUBLICADOS con un scope
            $researches = Research::with(['category', 'author', 'tags'])
                ->published()
                ->latest('published_at')
                ->paginate(12);
                
            // Obtener categorías para el filtro lateral - Compatible con SQLite
            $categories = Category::withCount(['research' => function($query) {
                    $query->published();
                }])
                ->orderBy('research_count', 'desc')
                ->get()
                ->filter(function($category) {
                    return $category->research_count > 0;
                })
                ->take(10);

            // Obtener investigaciones destacadas
            $featuredResearch = Research::with(['category', 'author'])
                ->featured()
                ->published()
                ->cited() // Ordenar por número de citas
                ->take(5)
                ->get();
                
            return [
                'researches' => $researches,
                'categories' => $categories,
                'featuredResearch' => $featuredResearch
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
        return view('research.index', compact(
            'researches', 
            'categories', 
            'featuredResearch'
        ))->with([
            'getImageUrl' => function($imageName, $type = 'research', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => function($category) {
                return CategoryHelper::getCategoryStyle($category);
            },
            'getCategoryIcon' => function($category) {
                return CategoryHelper::getCategoryIcon($category);
            }
        ]);
    }

    /**
     * Display the specified research article.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Generar una clave de caché única para este artículo
        $cacheKey = "research_article_{$slug}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($slug) {
            // Intentar encontrar por slug, si no, por ID con eager loading
            $research = Research::with(['category', 'author', 'tags', 'comments' => function($query) {
                    $query->latest()->take(5); // Solo cargar los 5 comentarios más recientes
                }])
                ->where('slug', $slug)
                ->first() 
                ?? Research::with(['category', 'author', 'tags', 'comments'])
                ->findOrFail($slug);
            
            // Obtener investigaciones relacionadas - Por categoría y tags
            $relatedResearch = $this->getRelatedResearch($research);
                
            // Obtener las investigaciones más vistas
            $mostViewedResearch = Research::with('category')
                ->where('id', '!=', $research->id)
                ->published()
                ->popular()
                ->take(4)
                ->get();
            
            // Tipos populares 
            $popularTypes = [
                [
                    'name' => 'Inteligencia Artificial',
                    'icon' => 'fas fa-brain',
                    'color' => '#4285f4',
                    'count' => 24,
                    'type' => 'ia'
                ],
                [
                    'name' => 'Machine Learning',
                    'icon' => 'fas fa-cogs',
                    'color' => '#ea4335',
                    'count' => 18,
                    'type' => 'ml'
                ],
                [
                    'name' => 'Robótica',
                    'icon' => 'fas fa-robot',
                    'color' => '#fbbc05',
                    'count' => 15,
                    'type' => 'robotica'
                ],
                [
                    'name' => 'Blockchain',
                    'icon' => 'fas fa-link',
                    'color' => '#34a853',
                    'count' => 9,
                    'type' => 'blockchain'
                ],
                [
                    'name' => 'Ética y AI',
                    'icon' => 'fas fa-balance-scale',
                    'color' => '#9c27b0',
                    'count' => 12,
                    'type' => 'etica'
                ]
            ];
                
            return [
                'research' => $research,
                'relatedResearch' => $relatedResearch,
                'mostViewedResearch' => $mostViewedResearch,
                'popularTypes' => $popularTypes
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
        // Incrementar contador de vistas (fuera del caché)
        // Usando el método del modelo
        $research->incrementViews();
        
        return view('research.show', compact(
            'research',
            'relatedResearch',
            'mostViewedResearch',
            'popularTypes'
        ))->with([
            'getImageUrl' => function($imageName, $type = 'research', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => function($category) {
                return CategoryHelper::getCategoryStyle($category);
            },
            'getCategoryIcon' => function($category) {
                return CategoryHelper::getCategoryIcon($category);
            }
        ]);
    }

    /**
     * Muestra investigaciones filtradas por tipo.
     *
     * @param string $type
     * @return \Illuminate\View\View
     */
    public function byType($type)
    {
        $cacheKey = "research_by_type_{$type}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($type) {
            // Obtener investigaciones del tipo indicado
            $research = Research::with(['category', 'author'])
                ->where('type', $type)
                ->published()
                ->latest('published_at')
                ->paginate(10);
                
            // Obtener etiquetas relacionadas con este tipo
            $relatedTags = Tag::whereHas('research', function($query) use ($type) {
                    $query->where('type', $type);
                })
                ->withCount('research')
                ->orderBy('research_count', 'desc')
                ->take(10)
                ->get();
                
            return [
                'research' => $research,
                'relatedTags' => $relatedTags
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
        // Helper functions para la vista
        $getImageUrl = function($imageName, $type = 'research', $size = 'medium') {
            // Verificar si la imagen existe
            if (!empty($imageName) && $imageName != 'default.jpg' && 
                !str_contains($imageName, 'default') && !str_contains($imageName, 'placeholder')) {
                
                // Si la ruta ya comienza con 'storage/', solo usamos asset()
                if (Str::startsWith($imageName, 'storage/')) {
                    return asset($imageName);
                }
                
                // De lo contrario, construimos la ruta completa
                return asset('storage/' . $type . '/' . $imageName);
            }
            
            // Imagen predeterminada
            return asset("storage/images/defaults/{$type}-default-{$size}.jpg");
        };
        
        // Función para obtener el estilo de una categoría
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
            
        return view('research.by_type', compact(
            'research', 
            'type', 
            'relatedTags'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra investigaciones filtradas por categoría.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function category(Category $category)
    {
        $cacheKey = "research_by_category_{$category->id}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($category) {
            // Obtener artículos de investigación de esta categoría
            $researches = Research::with(['author', 'tags'])
                ->where('category_id', $category->id)
                ->published()
                ->latest('published_at')
                ->paginate(12);
                
            // Obtener categorías para el filtro lateral - Compatible con SQLite
            $categories = Category::withCount(['research' => function($query) {
                    $query->published();
                }])
                ->orderBy('research_count', 'desc')
                ->get()
                ->filter(function($category) {
                    return $category->research_count > 0;
                })
                ->take(10);
                
            // Obtener investigaciones destacadas de esta categoría
            $featuredResearch = Research::with('author')
                ->where('category_id', $category->id)
                ->featured()
                ->published()
                ->cited()
                ->take(5)
                ->get();
                
            return [
                'researches' => $researches,
                'categories' => $categories,
                'featuredResearch' => $featuredResearch
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
        return view('research.index', compact(
            'researches', 
            'categories', 
            'featuredResearch', 
            'category'
        ))->with([
            'getImageUrl' => function($imageName, $type = 'research', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => function($category) {
                return CategoryHelper::getCategoryStyle($category);
            },
            'getCategoryIcon' => function($category) {
                return CategoryHelper::getCategoryIcon($category);
            }
        ]);
    }
    
    /**
     * Obtiene investigaciones relacionadas basadas en categoría y tags.
     *
     * @param Research $research
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRelatedResearch(Research $research)
    {
        // Si el artículo tiene tags, buscar por tags comunes
        if ($research->relationLoaded('tags') && $research->tags->count() > 0) {
            $tagIds = $research->tags->pluck('id')->toArray();
            
            $relatedByTags = Research::with('category')
                ->where('id', '!=', $research->id)
                ->whereHas('tags', function($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                })
                ->published()
                ->take(2)
                ->get();
                
            // Si encontramos suficientes relacionados por tags, devolver esos
            if ($relatedByTags->count() >= 2) {
                return $relatedByTags;
            }
            
            // Si no, combinar con relacionados por categoría
            $relatedByCategory = Research::with('category')
                ->where('id', '!=', $research->id)
                ->where('category_id', $research->category_id)
                ->whereNotIn('id', $relatedByTags->pluck('id')->toArray())
                ->published()
                ->take(3 - $relatedByTags->count())
                ->get();
                
            return $relatedByTags->concat($relatedByCategory);
        }
        
        // Si no tiene tags, relacionar solo por categoría
        return Research::with('category')
            ->where('id', '!=', $research->id)
            ->where('category_id', $research->category_id)
            ->published()
            ->take(3)
            ->get();
    }
}