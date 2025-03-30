<?php
// app/Http/Controllers/SitemapController.php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use App\Models\Column;
use App\Models\Research;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $content = view('sitemap.index', [
            'categories' => Category::all(),
            'news' => News::where('status', 'published')->orderBy('updated_at', 'desc')->get(),
            'columns' => Column::orderBy('updated_at', 'desc')->get(),
            'researches' => Research::where('status', 'published')->orderBy('updated_at', 'desc')->get(),
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function news()
    {
        $news = News::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()
            ->view('sitemap.news', [
                'news' => $news,
            ], 200)
            ->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Category::all();

        return response()
            ->view('sitemap.categories', [
                'categories' => $categories,
            ], 200)
            ->header('Content-Type', 'text/xml');
    }

    public function research()
    {
        $researches = Research::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()
            ->view('sitemap.research', [
                'researches' => $researches,
            ], 200)
            ->header('Content-Type', 'text/xml');
    }

    public function columns()
    {
        $columns = Column::orderBy('updated_at', 'desc')
            ->get();

        return response()
            ->view('sitemap.columns', [
                'columns' => $columns,
            ], 200)
            ->header('Content-Type', 'text/xml');
    }
}