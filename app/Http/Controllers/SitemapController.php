<?php
// app/Http/Controllers/SitemapController.php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use App\Models\Column;
use App\Models\Research;

class SitemapController extends Controller
{
    public function index()
    {
        $news = News::where('status', 'published')->orderBy('updated_at', 'desc')->get();
        $researches = Research::where('status', 'published')->orderBy('updated_at', 'desc')->get();
        $columns = Column::orderBy('updated_at', 'desc')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-main.xml') . '</loc>';
        $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-news.xml') . '</loc>';
        $xml .= '<lastmod>' . ($news->count() > 0 ? $news->first()->updated_at->toIso8601String() : now()->toIso8601String()) . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-categories.xml') . '</loc>';
        $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-research.xml') . '</loc>';
        $xml .= '<lastmod>' . ($researches->count() > 0 ? $researches->first()->updated_at->toIso8601String() : now()->toIso8601String()) . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-columns.xml') . '</loc>';
        $xml .= '<lastmod>' . ($columns->count() > 0 ? $columns->first()->updated_at->toIso8601String() : now()->toIso8601String()) . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '</sitemapindex>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function news()
    {
        $news = News::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
        
        foreach ($news as $article) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('news.show', $article->slug ?? $article->id) . '</loc>';
            $xml .= '<lastmod>' . $article->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '<news:news>';
            $xml .= '<news:publication>';
            $xml .= '<news:name>' . config('app.name', 'ConocIA') . '</news:name>';
            $xml .= '<news:language>' . app()->getLocale() . '</news:language>';
            $xml .= '</news:publication>';
            $xml .= '<news:publication_date>' . ($article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String()) . '</news:publication_date>';
            $xml .= '<news:title>' . htmlspecialchars($article->title) . '</news:title>';
            if(is_object($article->category) && isset($article->category->name)) {
                $xml .= '<news:keywords>' . htmlspecialchars($article->category->name) . '</news:keywords>';
            }
            $xml .= '</news:news>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::all();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($categories as $category) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('news.by.category', $category->slug) . '</loc>';
            $xml .= '<lastmod>' . now()->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function research()
    {
        $researches = Research::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($researches as $research) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('research.show', $research->slug ?? $research->id) . '</loc>';
            $xml .= '<lastmod>' . $research->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    public function columns()
    {
        $columns = Column::orderBy('updated_at', 'desc')
            ->get();
            
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($columns as $column) {
            $xml .= '<url>';
            $xml .= '<loc>' . route('columns.show', $column->slug ?? $column->id) . '</loc>';
            $xml .= '<lastmod>' . $column->updated_at->toIso8601String() . '</lastmod>';
            $xml .= '<changefreq>monthly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }
}