<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\User;
use App\Models\SocialMediaQueue;
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
        // Estadísticas generales
        $stats = [
            'total_news' => News::count(),
            'published_news' => News::where('status', 'published')->count(),
            'categories' => Category::count(),
            'users' => User::count(),
        ];

        // Noticias recientes
        $recentNews = News::with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Noticias populares
        $popularNews = News::with('category')
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        // Publicaciones pendientes en redes sociales
        $pendingSocialPosts = SocialMediaQueue::where('status', 'pending')
            ->with('news')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Contador de publicaciones pendientes para la barra superior
        $pendingSocialCount = SocialMediaQueue::where('status', 'pending')->count();    

        // Asegúrate de incluirlo en el compact al final
        return view('admin.dashboard', compact(
            'stats',
            'recentNews',
            'popularNews',
            'pendingSocialPosts',
            'pendingSocialCount'
        ));
    }
}