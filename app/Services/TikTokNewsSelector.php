<?php

namespace App\Services;

use App\Models\News;
use App\Models\TikTokScript;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TikTokNewsSelector
{
    // Pesos para cada factor
    protected $weights = [
        'recency' => 0.3,        // Noticias más recientes
        'popularity' => 0.25,     // Noticias más vistas/comentadas
        'engagement' => 0.2,      // Noticias con más interacción
        'virality' => 0.15,       // Potencial viral (keywords, etc)
        'diversity' => 0.1        // Diversidad de categorías
    ];
    
    // Categorías a incluir con cuotas mínimas
    protected $categoryQuotas = [
        'news' => 0.4,            // 40% noticias
        'investigations' => 0.3,  // 30% investigaciones
        'columns' => 0.3          // 30% columnas
    ];
    
    // Palabras clave que aumentan el potencial viral
    protected $viralKeywords = [
        'exclusiva', 'increíble', 'sorprendente', 'impactante', 'viral',
        'revelación', 'escándalo', 'descubrimiento', 'última hora',
        'secreto', 'inédito', 'inesperado', 'histórico', 'récord'
    ];
    
   /**
     * Obtener noticias recomendadas para TikTok
     * 
     * @param int $limit Número máximo de noticias a recomendar
     * @return Collection
     */
    public function getRecommendedNews(int $limit = 10): Collection
    {
        // Obtener noticias que no tienen guión de TikTok activo
        $candidateNews = $this->getCandidateNews();
        
        // Calcular puntuación para cada noticia
        $scoredNews = $this->scoreNews($candidateNews);
        
        // Aplicar diversidad por categoría
        $selectedNews = $this->applyDiversity($scoredNews, $limit);
        
        return $selectedNews;
    }
    
    /**
     * Obtener noticias candidatas (que no tengan guión de TikTok o estén rechazados)
     */
    protected function getCandidateNews(): Collection
    {
        // Obtener IDs de noticias que ya tienen guiones no rechazados
        $excludeIds = TikTokScript::whereIn('status', ['draft', 'pending_review', 'approved', 'published'])
            ->pluck('news_id')
            ->toArray();
        
        // Buscar noticias de los últimos 7 días que no tienen guiones activos
        $candidateNews = News::whereNotIn('id', $excludeIds)
            ->where('status', 'published')
            ->where('published_at', '>=', Carbon::now()->subDays(7))
            ->with('category', 'comments')
            ->get();
            
        return $candidateNews;
    }
    
    /**
     * Calcular puntuación para cada noticia según múltiples criterios
     * Ahora público para poder usarlo desde el controlador
     */
    public function scoreNews(Collection $newsItems): Collection
    {
        return $newsItems->map(function ($news) {
            // Calcular puntajes individuales (0-1)
            $recencyScore = $this->calculateRecencyScore($news);
            $popularityScore = $this->calculatePopularityScore($news);
            $engagementScore = $this->calculateEngagementScore($news);
            $viralityScore = $this->calculateViralityScore($news);
            
            // Calcular puntuación ponderada total
            $totalScore = 
                ($recencyScore * $this->weights['recency']) +
                ($popularityScore * $this->weights['popularity']) +
                ($engagementScore * $this->weights['engagement']) +
                ($viralityScore * $this->weights['virality']);
                
            // Normalizar puntuación (0-100)
            $normalizedScore = round($totalScore * 100);
            
            // Añadir puntuación a la noticia
            $news->tiktok_score = $normalizedScore;
            
            return $news;
        })->sortByDesc('tiktok_score');
    }
    
    /**
     * Aplicar diversidad por categoría
     */
    protected function applyDiversity(Collection $scoredNews, int $limit): Collection
    {
        $result = collect();
        $categoryCount = [];
        
        // Inicializar contadores por categoría
        foreach ($this->categoryQuotas as $category => $quota) {
            $categoryCount[$category] = 0;
        }
        
        // Calcular cuotas basadas en el límite
        $categoryLimits = [];
        foreach ($this->categoryQuotas as $category => $quota) {
            $categoryLimits[$category] = ceil($limit * $quota);
        }
        
        // Primera pasada: seleccionar noticias respetando las cuotas
        foreach ($scoredNews as $news) {
            if (!$news->category) {
                continue; // Saltar si no tiene categoría
            }
            
            $category = $news->category->slug; // Asumiendo que la categoría tiene un slug
            
            // Si la categoría no está en nuestras cuotas, asignarla a 'other'
            if (!isset($categoryLimits[$category])) {
                $category = 'other';
            }
            
            // Verificar si aún no alcanzamos el límite para esta categoría
            if (!isset($categoryCount[$category]) || $categoryCount[$category] < ($categoryLimits[$category] ?? $limit)) {
                $result->push($news);
                $categoryCount[$category] = ($categoryCount[$category] ?? 0) + 1;
            }
            
            // Salir si ya alcanzamos el límite total
            if ($result->count() >= $limit) {
                break;
            }
        }
        
        // Segunda pasada (si es necesario): completar con las mejores noticias restantes
        if ($result->count() < $limit) {
            $remainingNews = $scoredNews->diff($result);
            $remainingNeeded = $limit - $result->count();
            
            $result = $result->merge($remainingNews->take($remainingNeeded));
        }
        
        return $result->sortByDesc('tiktok_score');
    }
    
    /**
     * Calcular puntuación de actualidad (más reciente = mayor puntuación)
     */
    protected function calculateRecencyScore($news): float
    {
        $now = Carbon::now();
        $newsDate = Carbon::parse($news->published_at ?: $news->created_at);
        $hoursDiff = $now->diffInHours($newsDate);
        
        // Noticias de menos de 24 horas obtienen puntuaciones más altas
        if ($hoursDiff <= 24) {
            return 1 - ($hoursDiff / 24);
        }
        
        // Noticias más antiguas obtienen puntuaciones exponencialmente más bajas
        $daysDiff = $now->diffInDays($newsDate);
        return max(0, 1 - pow($daysDiff / 7, 2));
    }
    
    /**
     * Calcular puntuación de popularidad basada en vistas
     */
    protected function calculatePopularityScore($news): float
    {
        // Usar el campo views de tu modelo News
        $viewCount = $news->views ?? 0;
        
        // Aplicar logaritmo para manejar diferencias grandes
        // Asumimos que 1000 vistas es una buena noticia (0.8) y 5000 es excelente (1.0)
        if ($viewCount <= 0) {
            return 0;
        }
        
        return min(1, max(0, log10($viewCount) / log10(5000)));
    }
    
    /**
     * Calcular puntuación de engagement basada en comentarios y compartidos
     */
    protected function calculateEngagementScore($news): float
    {
        // Obtener conteo de comentarios
        $commentCount = $news->comments()->count() ?? 0;
        
        // Usar compartidos en redes sociales si existe
        $shareCount = 0;
        if (method_exists($news, 'socialPosts')) {
            $shareCount = $news->socialPosts()->count() ?? 0;
        }
        
        // Calcular engagement (comentarios tienen más peso que compartidos)
        $engagementValue = ($commentCount * 1.5) + $shareCount;
        
        // Normalizar (20+ es puntuación máxima)
        return min(1, $engagementValue / 20);
    }
    
    /**
     * Calcular potencial de viralidad basado en palabras clave
     */
    protected function calculateViralityScore($news): float
    {
        $title = strtolower($news->title);
        $content = strtolower($news->content);
        
        // Contar palabras clave virales en título y contenido
        $titleMatches = 0;
        $contentMatches = 0;
        
        foreach ($this->viralKeywords as $keyword) {
            if (str_contains($title, $keyword)) {
                $titleMatches++;
            }
            
            if (str_contains($content, $keyword)) {
                $contentMatches++;
            }
        }
        
        // Palabras clave en el título tienen más peso
        $viralityValue = ($titleMatches * 2) + ($contentMatches * 0.5);
        
        // Normalizar (máximo 5 puntos)
        return min(1, $viralityValue / 5);
    }
}