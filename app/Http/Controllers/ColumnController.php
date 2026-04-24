<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ColumnController extends Controller
{
    /**
     * Tiempo de caché en segundos (1 hora)
     */
    protected const CACHE_TIME = 3600;

    /**
     * Muestra el listado de columnas.
     */
    public function index()
    {
        // Los paginadores no son serializables — se paginan fuera del cache
        $columns = Column::with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $sideData = Cache::remember('columns_page_side_data_v2', self::CACHE_TIME, function () {
            // Columnas destacadas
            $featuredColumns = Column::with(['author', 'category'])
                ->published()
                ->featured()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();

            // Columnistas principales
            $columnists = User::whereHas('columns', function ($query) {
                    $query->published();
                })
                ->whereNotIn('email', ['admin@conocia.com', 'autor@conocia.com'])
                ->withCount(['columns' => function ($query) {
                    $query->published();
                }])
                ->orderBy('columns_count', 'desc')
                ->take(6)
                ->get();

            // Categorías con columnas
            $categories = Category::whereHas('columns', function ($query) {
                    $query->published();
                })
                ->withCount(['columns' => function ($query) {
                    $query->published();
                }])
                ->orderBy('columns_count', 'desc')
                ->take(10)
                ->get();

            return compact('featuredColumns', 'columnists', 'categories');
        });

        extract($sideData);
        
        // Helper functions para la vista (similar a ResearchController)
        $getImageUrl = function($imageName, $type = 'column', $size = 'medium') {
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
            return asset("images/defaults/{$type}-default-{$size}.jpg");
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
                return 'fa-pen-fancy';
            }
            
            return $category->icon;
        };
        
        return view('columns.index', compact(
            'columns', 
            'featuredColumns', 
            'columnists', 
            'categories'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra una columna específica.
     */
    public function show($slug)
    {
        // Generar una clave de caché única para esta columna
        $cacheKey = "column_{$slug}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($slug) {
            $column = Column::with(['author', 'category'])
                ->where('slug', $slug)
                ->published()
                ->firstOrFail();
            
            // Obtener columnas del mismo autor
            $authorColumns = Column::with('category')
                ->where('author_id', $column->author_id)
                ->where('id', '!=', $column->id)
                ->published()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
            
            // Obtener columnas relacionadas por categoría
            $relatedColumns = Column::with(['author', 'category'])
                ->where('category_id', $column->category_id)
                ->where('id', '!=', $column->id)
                ->where('author_id', '!=', $column->author_id) // Evitar duplicados del mismo autor
                ->published()
                ->orderBy('published_at', 'desc')
                ->take(3)
                ->get();
            
            return [
                'column' => $column,
                'authorColumns' => $authorColumns,
                'relatedColumns' => $relatedColumns
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
        // Incrementar contador de vistas (fuera del caché)
        $column->increment('views');
        
        // Helper functions para la vista
        $getImageUrl = function($imageName, $type = 'column', $size = 'medium') {
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
            return asset("images/defaults/{$type}-default-{$size}.jpg");
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
                return 'fa-pen-fancy';
            }
            
            return $category->icon;
        };
        
        return view('columns.show', compact(
            'column', 
            'authorColumns', 
            'relatedColumns'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra columnas por categoría.
     */
    public function byCategory($slug)
    {
        $category = Cache::remember("column_category_{$slug}", self::CACHE_TIME,
            fn() => Category::where('slug', $slug)->firstOrFail()
        );

        // Paginator fuera del cache (no serializable)
        $columns = Column::with(['author', 'category'])
            ->where('category_id', $category->id)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        // Helper functions para la vista
        $getImageUrl = function($imageName, $type = 'column', $size = 'medium') {
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
            return asset("images/defaults/{$type}-default-{$size}.jpg");
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
                return 'fa-pen-fancy';
            }
            
            return $category->icon;
        };
        
        return view('columns.by-category', compact(
            'columns', 
            'category'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra columnas por autor.
     */
    public function byAuthor($id)
    {
        $author = Cache::remember("column_author_{$id}", self::CACHE_TIME,
            fn() => User::findOrFail($id)
        );

        // Paginator fuera del cache (no serializable)
        $columns = Column::with('category')
            ->where('author_id', $author->id)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        // Helper functions para la vista
        $getImageUrl = function($imageName, $type = 'author', $size = 'medium') {
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
            return asset("images/defaults/{$type}-default-{$size}.jpg");
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
                return 'fa-pen-fancy';
            }
            
            return $category->icon;
        };
        
        return view('columns.by-author', compact(
            'columns',
            'author'
        ))->with([
            'getImageUrl' => $getImageUrl,
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    public function columnists()
    {
        $featured = User::featuredColumnists()
            ->withCount(['columns' => fn($q) => $q->published()])
            ->having('columns_count', '>', 0)
            ->orderByDesc('columns_count')
            ->get();

        $all = User::where('is_active', true)
            ->whereHas('columns', fn($q) => $q->published())
            ->where('is_featured_columnist', false)
            ->withCount(['columns' => fn($q) => $q->published()])
            ->orderByDesc('columns_count')
            ->get();

        return view('columns.columnists', compact('featured', 'all'));
    }

    public function writeForUs()
    {
        return view('columns.write-for-us');
    }

    public function submitWriteForUs(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'email'       => 'required|email|max:120',
            'institution' => 'nullable|string|max:120',
            'expertise'   => 'nullable|string|max:120',
            'title'       => 'required|string|max:200',
            'summary'     => 'required|string|min:30|max:2000',
            'linkedin'    => 'nullable|url|max:255',
        ]);

        try {
            $adminEmail = config('mail.from.address', 'newsletter@conocia.cl');
            Mail::raw(
                "Nueva propuesta de columna para ConocIA\n\n" .
                "Nombre: {$validated['name']}\n" .
                "Email: {$validated['email']}\n" .
                "Institución: " . ($validated['institution'] ?? '—') . "\n" .
                "Expertise: " . ($validated['expertise'] ?? '—') . "\n" .
                "LinkedIn: " . ($validated['linkedin'] ?? '—') . "\n\n" .
                "Título propuesto:\n{$validated['title']}\n\n" .
                "Resumen:\n{$validated['summary']}",
                fn($msg) => $msg->to($adminEmail)
                                 ->subject("Propuesta columna: {$validated['title']}")
                                 ->replyTo($validated['email'], $validated['name'])
            );
        } catch (\Exception $e) {
            Log::error('WriteForUs mail failed: ' . $e->getMessage());
        }

        return redirect()->route('columns.write-for-us')
            ->with('success', '¡Propuesta recibida! Te responderemos a ' . $validated['email'] . ' en los próximos 5 días hábiles.');
    }
}
