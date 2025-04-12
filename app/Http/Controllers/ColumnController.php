<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper; 

class ColumnController extends Controller
{
    /**
     * Tiempo de caché en segundos (1 hora)
     */
    protected const CACHE_TIME = 3600;

    /**
     * Muestra el listado de columnas.
     */
    /**
     * Muestra el listado de columnas.
     */
    public function index()
    {
        // Usar una única clave de caché para todos los datos de la página
        $viewData = Cache::remember('columns_page_data', self::CACHE_TIME, function () {
            // Columnas principales paginadas
            $columns = Column::with(['author', 'category'])
                ->published()
                ->orderBy('published_at', 'desc')
                ->paginate(12);
            
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
            
            return [
                'columns' => $columns,
                'featuredColumns' => $featuredColumns,
                'columnists' => $columnists,
                'categories' => $categories
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
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
            'getImageUrl' => function($imageName, $type = 'column', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
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
            'getImageUrl' => function($imageName, $type = 'column', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra columnas por categoría.
     */
    public function byCategory($slug)
    {
        // Generar una clave de caché única para esta categoría
        $cacheKey = "columns_by_category_{$slug}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($slug) {
            $category = Category::where('slug', $slug)->firstOrFail();
            
            $columns = Column::with(['author', 'category'])
                ->where('category_id', $category->id)
                ->published()
                ->orderBy('published_at', 'desc')
                ->paginate(12);
            
            return [
                'category' => $category,
                'columns' => $columns
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
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
            'getImageUrl' => function($imageName, $type = 'column', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }

    /**
     * Muestra columnas por autor.
     */
    public function byAuthor($id)
    {
        // Generar una clave de caché única para este autor
        $cacheKey = "columns_by_author_{$id}";
        
        $viewData = Cache::remember($cacheKey, self::CACHE_TIME, function () use ($id) {
            $author = User::findOrFail($id);
            
            $columns = Column::with('category')
                ->where('author_id', $author->id)
                ->published()
                ->orderBy('published_at', 'desc')
                ->paginate(12);
            
            return [
                'author' => $author,
                'columns' => $columns
            ];
        });
        
        // Extraer datos de la caché
        extract($viewData);
        
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
            'getImageUrl' => function($imageName, $type = 'author', $size = 'medium') {
                return ImageHelper::getImageUrl($imageName, $type, $size);
            },
            'getCategoryStyle' => $getCategoryStyle,
            'getCategoryIcon' => $getCategoryIcon
        ]);
    }
}