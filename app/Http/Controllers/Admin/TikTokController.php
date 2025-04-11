<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\TikTokScript;
use App\Services\TikTokNewsSelector;
use App\Services\TikTokScriptGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TikTokController extends Controller
{
    protected $newsSelector;
    protected $scriptGenerator;
    
    public function __construct(
        TikTokNewsSelector $newsSelector,
        TikTokScriptGenerator $scriptGenerator
    ) {
        $this->newsSelector = $newsSelector;
        $this->scriptGenerator = $scriptGenerator;
    }
    
    /**
     * Mostrar el dashboard de TikTok
     */
    public function index()
    {
        // Obtener estadísticas generales (caché por 1 hora)
        $stats = Cache::remember('tiktok_dashboard_stats', 3600, function () {
            return [
                'total_scripts' => TikTokScript::count(),
                'pending_review' => TikTokScript::where('status', 'pending_review')->count(),
                'approved' => TikTokScript::where('status', 'approved')->count(),
                'published' => TikTokScript::where('status', 'published')->count(),
                'total_views' => TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                    ->sum('tiktok_metrics.views'),
                'total_engagement' => TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                    ->selectRaw('SUM(tiktok_metrics.likes + tiktok_metrics.comments + tiktok_metrics.shares) as total_engagement')
                    ->first()->total_engagement ?? 0,
                'total_clicks' => TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                    ->sum('tiktok_metrics.clicks_to_portal'),
            ];
        });
        
        // Obtener scripts pendientes de revisión
        $pendingScripts = TikTokScript::with('news')
            ->where('status', 'pending_review')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Obtener scripts aprobados
        $approvedScripts = TikTokScript::with('news')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Obtener recomendaciones de noticias
        $recommendedNews = $this->newsSelector->getRecommendedNews(5);
        
        // Contador para el sidebar
        $pendingTikTokScriptsCount = TikTokScript::where('status', 'pending_review')->count();
        
        return view('admin.tiktok.index', compact('stats', 'pendingScripts', 'approvedScripts', 'recommendedNews', 'pendingTikTokScriptsCount'));
    }
    
    /**
     * Mostrar el formulario para crear un nuevo guión
     */
    public function create($newsId)
    {
        $news = News::findOrFail($newsId);
        
        return view('admin.tiktok.create', compact('news'));
    }
    
    /**
     * Generar automáticamente un guión con OpenAI
     */
    public function generate($newsId)
    {
        $news = News::findOrFail($newsId);
        
        // Verificar si ya existe un guión para esta noticia
        $existingScript = TikTokScript::where('news_id', $newsId)
            ->whereIn('status', ['draft', 'pending_review', 'approved', 'published'])
            ->first();
            
        if ($existingScript) {
            return redirect()->route('admin.tiktok.edit', $existingScript->id)
                ->with('warning', 'Ya existe un guión para esta noticia.');
        }
        
        // Generar nuevo guión
        $script = $this->scriptGenerator->generateScript($news);
        
        if (!$script) {
            return redirect()->back()
                ->with('error', 'No se pudo generar el guión. Por favor, intenta nuevamente.');
        }
        
        return redirect()->route('admin.tiktok.edit', $script->id)
            ->with('success', 'Guión generado correctamente. Por favor, revísalo antes de aprobarlo.');
    }
    
    /**
     * Mostrar un guión específico para edición
     */
    public function edit($id)
    {
        $script = TikTokScript::with('news')->findOrFail($id);
        
        return view('admin.tiktok.edit', compact('script'));
    }
    
    /**
     * Actualizar un guión
     */
    public function update(Request $request, $id)
    {
        $script = TikTokScript::findOrFail($id);
        
        $validated = $request->validate([
            'script_content' => 'required|string',
            'visual_suggestions' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'status' => 'required|in:draft,pending_review,approved,rejected'
        ]);
        
        $script->update($validated);
        
        return redirect()->route('admin.tiktok.edit', $script->id)
            ->with('success', 'Guión actualizado correctamente.');
    }
    
    /**
     * Cambiar estado de un guión
     */
    public function updateStatus(Request $request, $id)
    {
        $script = TikTokScript::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:draft,pending_review,approved,rejected,published'
        ]);
        
        // Si se está marcando como publicado, registrar la fecha
        if ($validated['status'] === 'published' && $script->status !== 'published') {
            $validated['published_at'] = now();
        }
        
        $script->update($validated);
        
        return redirect()->back()
            ->with('success', 'Estado del guión actualizado correctamente.');
    }
    
     /**
     * Muestra las noticias recomendadas para TikTok
     */
    public function recommendations()
    {
        // Obtener artículos recomendados para TikTok
        // Puedes ajustar la lógica según tus criterios de recomendación
        $recommendedArticles = News::where('status', 'published')
            ->where('tiktok_status', '!=', 'generated') // No mostrar los que ya tienen guión
            ->orderBy('tiktok_score', 'desc') // Ordenar por puntuación TikTok si tienes ese campo
            ->paginate(15);
            
        // Pasar los artículos recomendados a la vista
        return view('admin.tiktok.recommendations', compact('recommendedArticles'));
    }
    
    /**
     * Registrar métricas manualmente para un video publicado
     */
    public function recordMetrics(Request $request, $id)
    {
        $script = TikTokScript::findOrFail($id);
        
        $validated = $request->validate([
            'tiktok_video_id' => 'required|string',
            'views' => 'required|integer|min:0',
            'likes' => 'required|integer|min:0',
            'comments' => 'required|integer|min:0',
            'shares' => 'required|integer|min:0',
            'clicks_to_portal' => 'required|integer|min:0',
        ]);
        
        // Actualizar o crear métricas
        if ($script->metrics) {
            $script->metrics->update($validated);
        } else {
            $script->metrics()->create($validated);
        }
        
        return redirect()->back()
            ->with('success', 'Métricas actualizadas correctamente.');
    }
    
    /**
     * Mostrar estadísticas y análisis
     */
    public function stats()
    {
        // Obtener métricas agrupadas por día (últimos 30 días)
        $dailyStats = Cache::remember('tiktok_daily_stats', 3600, function () {
            return TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                ->where('tiktok_scripts.published_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(tiktok_scripts.published_at) as date, SUM(views) as total_views, SUM(likes) as total_likes, 
                    SUM(comments) as total_comments, SUM(shares) as total_shares, SUM(clicks_to_portal) as total_clicks')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        });
        
        // Obtener métricas por categoría
        $categoryStats = Cache::remember('tiktok_category_stats', 3600, function () {
            return TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                ->join('news', 'tiktok_scripts.news_id', '=', 'news.id')
                ->join('categories', 'news.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as category, SUM(tiktok_metrics.views) as total_views, SUM(likes) as total_likes, 
                    SUM(comments) as total_comments, SUM(shares) as total_shares, SUM(clicks_to_portal) as total_clicks')
                ->groupBy('categories.name')
                ->orderByDesc('total_views')
                ->get();
        });
        
        // Top 10 videos por engagement
        $topVideos = Cache::remember('tiktok_top_videos', 3600, function () {
            return TikTokScript::join('tiktok_metrics', 'tiktok_scripts.id', '=', 'tiktok_metrics.tiktok_script_id')
                ->join('news', 'tiktok_scripts.news_id', '=', 'news.id')
                ->selectRaw('tiktok_scripts.id, news.title, tiktok_metrics.views, tiktok_metrics.likes, 
                    tiktok_metrics.comments, tiktok_metrics.shares, tiktok_metrics.clicks_to_portal, 
                    (tiktok_metrics.likes + tiktok_metrics.comments + tiktok_metrics.shares) as engagement')
                ->orderByDesc('engagement')
                ->limit(10)
                ->get();
        });
        
        return view('admin.tiktok.stats', compact('dailyStats', 'categoryStats', 'topVideos'));
    }


    /**
     * Guardar un nuevo guión creado manualmente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'news_id' => 'required|exists:news,id',
            'script_content' => 'required|string',
            'visual_suggestions' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'status' => 'required|in:draft,pending_review'
        ]);
        
        // Verificar si ya existe un guión para esta noticia
        $existingScript = TikTokScript::where('news_id', $validated['news_id'])
            ->whereIn('status', ['draft', 'pending_review', 'approved', 'published'])
            ->first();
            
        if ($existingScript) {
            return redirect()->route('admin.tiktok.edit', $existingScript->id)
                ->with('warning', 'Ya existe un guión para esta noticia.');
        }
        
        // Si se envió directamente a revisión, actualizar el estado
        if ($request->has('submit') && $request->input('submit') === 'pending_review') {
            $validated['status'] = 'pending_review';
        }
        
        // Obtener la puntuación TikTok de la noticia
        $news = News::find($validated['news_id']);
        $newsSelector = app(TikTokNewsSelector::class);
        $scoredNews = $newsSelector->scoreNews(collect([$news]));
        $tiktokScore = $scoredNews->first()->tiktok_score ?? 0;
        
        // Crear el guión
        $script = TikTokScript::create([
            'news_id' => $validated['news_id'],
            'script_content' => $validated['script_content'],
            'visual_suggestions' => $validated['visual_suggestions'],
            'hashtags' => $validated['hashtags'],
            'status' => $validated['status'],
            'tiktok_score' => $tiktokScore
        ]);
        
        // Eliminar caché del dashboard
        Cache::forget('tiktok_dashboard_stats');
        
        // Redireccionar según el estado
        if ($script->status === 'pending_review') {
            return redirect()->route('admin.tiktok.index')
                ->with('success', 'Guión enviado a revisión correctamente.');
        }
        
        return redirect()->route('admin.tiktok.edit', $script->id)
            ->with('success', 'Guión guardado como borrador correctamente.');
    }

    
}