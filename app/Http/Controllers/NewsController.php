<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

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

    public function show($slug)
    {

        $article = News::with('category', 'comments')->where('slug', $slug)->firstOrFail();
        $article->incrementViews();
        
        // Para artículos relacionados, también carga la relación
        $relatedArticles = News::with('category')
            ->where('slug', '!=', $slug)
            ->where('category_id', $article->category_id)
            ->latest()
            ->take(4)
            ->get();


        // Obtener tags populares
            $popularTags = Tag::withCount('news')
            ->orderBy('news_count', 'desc')
            ->take(10)
            ->get();

        // Obtener los artículos más leídos
        $mostReadArticles = News::with('category')
        ->orderBy('views', 'desc')
        ->take(5)
        ->get();    

        return view('news.show', compact('article', 'relatedArticles', 'popularTags', 'mostReadArticles'));
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


}