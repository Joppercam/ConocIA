<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar el panel de control
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Estadísticas básicas
        $stats = [
            'total_news' => News::count(),
            'published_news' => News::where('status', 'published')->count(),
            'draft_news' => News::where('status', 'draft')->count(),
            'categories' => Category::count(),
            'users' => User::count(),
        ];

        // Noticias recientes
        $recentNews = News::with(['category', 'author'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

        // Noticias más vistas
        $popularNews = News::where('status', 'published')
                        ->orderBy('views', 'desc')
                        ->limit(5)
                        ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentNews', 
            'popularNews'
        ));
    }
}