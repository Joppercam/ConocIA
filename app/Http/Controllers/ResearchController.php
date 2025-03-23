<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Research;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ResearchController extends Controller
{
    /**
     * Display a listing of research articles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener artículos de investigación, paginados y con sus categorías
        $researches = Research::with('category')
            ->latest()
            ->paginate(12);
            
        // Obtener categorías para el filtro lateral
        $categories = Category::withCount('research')
            ->orderBy('research_count', 'desc')
            ->take(10)
            ->get();
            
        // Obtener investigaciones destacadas
        $featuredResearch = Research::with('category')
            ->where('featured', true)
            ->orderBy('citations', 'desc')
            ->take(5)
            ->get();
            
        // Helper function para manejar correctamente las rutas de imágenes
        $getImageUrl = function($imagePath, $type = 'research', $size = 'large') {
            // Ruta para imágenes predeterminadas según el tipo y tamaño
            $defaultImages = [
                'news' => [
                    'large' => 'storage/images/defaults/news-default-large.jpg',
                    'medium' => 'storage/images/defaults/news-default-medium.jpg',
                    'small' => 'storage/images/defaults/news-default-small.jpg',
                ],
                'research' => [
                    'large' => 'storage/images/defaults/research-default-large.jpg',
                    'medium' => 'storage/images/defaults/research-default-medium.jpg',
                    'small' => 'storage/images/defaults/research-default-small.jpg',
                ],
                'profile' => 'storage/images/defaults/user-profile.jpg',
                'avatars' => 'storage/images/defaults/avatar-default.jpg'
            ];
            
            // Si no hay imagen, devolver la imagen predeterminada según el tipo
            if (!$imagePath || $imagePath == '' || $imagePath == 'null') {
                return asset($defaultImages[$type][$size] ?? $defaultImages[$type]['medium']);
            }
            
            // Si la ruta ya comienza con 'storage/', solo usamos asset()
            if (Str::startsWith($imagePath, 'storage/')) {
                return asset($imagePath);
            }
            
            // De lo contrario, construimos la ruta completa
            return asset('storage/' . $imagePath);
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
            
        return view('research.index', compact('researches', 'categories', 'featuredResearch'))
            ->with([
                'getImageUrl' => $getImageUrl,
                'getCategoryStyle' => $getCategoryStyle,
                'getCategoryIcon' => $getCategoryIcon
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
        // Intentar encontrar por slug, si no, por ID
        $research = Research::where('slug', $slug)->first() ?? Research::findOrFail($slug);
        
        // Incrementar contador de vistas
        $research->increment('views');
        
        // Obtener investigaciones relacionadas
        try {
            $relatedResearch = Research::where('id', '!=', $research->id)
                ->latest()
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            $relatedResearch = collect([]);
        }
            
        // Obtener las investigaciones más vistas
        $mostViewedResearch = Research::orderBy('views', 'desc')
            ->where('id', '!=', $research->id)
            ->take(4)
            ->get();
        
        // Añadir la propiedad 'type' que la vista espera
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
            
        // Helper functions - definir todas las funciones antes de usarlas
        $getImageUrl = function($imagePath, $type = 'research', $size = 'large') {
            // Ruta para imágenes predeterminadas según el tipo y tamaño
            $defaultImages = [
                'news' => [
                    'large' => 'storage/images/defaults/news-default-large.jpg',
                    'medium' => 'storage/images/defaults/news-default-medium.jpg',
                    'small' => 'storage/images/defaults/news-default-small.jpg',
                ],
                'research' => [
                    'large' => 'storage/images/defaults/research-default-large.jpg',
                    'medium' => 'storage/images/defaults/research-default-medium.jpg',
                    'small' => 'storage/images/defaults/research-default-small.jpg',
                ],
                'profile' => 'storage/images/defaults/user-profile.jpg',
                'avatars' => 'storage/images/defaults/avatar-default.jpg'
            ];
            
            // Si no hay imagen, devolver la imagen predeterminada según el tipo
            if (!$imagePath || $imagePath == '' || $imagePath == 'null') {
                return asset($defaultImages[$type][$size] ?? $defaultImages[$type]['medium']);
            }
            
            // Si la ruta ya comienza con 'storage/', solo usamos asset()
            if (Str::startsWith($imagePath, 'storage/')) {
                return asset($imagePath);
            }
            
            // De lo contrario, construimos la ruta completa
            return asset('storage/' . $imagePath);
        };
        
        // Definir getCategoryStyle antes de usarlo
        $getCategoryStyle = function($category) {
            if (!$category || !isset($category->color)) {
                return 'background-color: var(--primary-color);';
            }
            
            return 'background-color: ' . $category->color . ';';
        };
        
        // Definir getCategoryIcon antes de usarlo
        $getCategoryIcon = function($category) {
            if (!$category || !isset($category->icon)) {
                return 'fa-tag';
            }
            
            return $category->icon;
        };
        
        // Ahora que todas las variables están definidas, pasarlas a la vista
        return view('research.show', compact(
            'research', 
            'relatedResearch',
            'mostViewedResearch',
            'popularTypes'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
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
        $research = Research::where('type', $type)
            ->where('is_published', true)
            ->latest()
            ->paginate(10);
            
        return view('research.by_type', compact('research', 'type'));
    }



    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $researches = Research::where('category_id', $category->id)
            ->latest()
            ->paginate(12);
            
        $categories = Category::withCount('research')
            ->orderBy('research_count', 'desc')
            ->take(10)
            ->get();
            
        $featuredResearch = Research::with('category')
            ->where('featured', true)
            ->orderBy('citations', 'desc')
            ->take(5)
            ->get();
            
        // Helper functions (igual que en el método index)
        $getImageUrl = function($imagePath, $type = 'research', $size = 'large') {
            // Implementación igual que antes...
        };
        
        // Otras funciones auxiliares...
        
        return view('research.index', compact('researches', 'categories', 'featuredResearch', 'category'))
            ->with([
                'getImageUrl' => $getImageUrl,
                // Otras funciones auxiliares...
            ]);
    }
}