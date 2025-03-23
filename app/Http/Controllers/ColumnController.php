<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    /**
     * Muestra el listado de columnas.
     */
    public function index()
    {
        $columns = Column::with(['author', 'category'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        $featuredColumns = Column::with(['author', 'category'])
            ->published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();
        
        $columnists = User::whereHas('columns', function ($query) {
                $query->published();
            })
            ->withCount(['columns' => function ($query) {
                $query->published();
            }])
            ->orderBy('columns_count', 'desc')
            ->take(6)
            ->get();
        
        $categories = Category::whereHas('columns', function ($query) {
                $query->published();
            })
            ->withCount(['columns' => function ($query) {
                $query->published();
            }])
            ->orderBy('columns_count', 'desc')
            ->take(10)
            ->get();
        
        return view('columns.index', compact('columns', 'featuredColumns', 'columnists', 'categories'));
    }

    /**
     * Muestra una columna específica.
     */
    public function show($slug)
    {
        $column = Column::with(['author', 'category'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
        
        // Incrementar contador de vistas
        $column->increment('views');
        
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
        
        return view('columns.show', compact('column', 'authorColumns', 'relatedColumns'));
    }

    /**
     * Muestra columnas por categoría.
     */
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $columns = Column::with(['author', 'category'])
            ->where('category_id', $category->id)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        return view('columns.by-category', compact('columns', 'category'));
    }

    /**
     * Muestra columnas por autor.
     */
    public function byAuthor($id)
    {
        $author = User::findOrFail($id);
        
        $columns = Column::with('category')
            ->where('author_id', $author->id)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        return view('columns.by-author', compact('columns', 'author'));
    }
}