<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Claim;
use App\Models\ClaimCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerificadorStatsController extends Controller
{
    /**
     * Mostrar el panel de estadísticas
     */
    public function index()
    {
        // Usar caché para evitar generar estadísticas repetidamente
        return Cache::remember('verificador_stats', now()->addHours(3), function () {
            // Estadísticas generales
            $totalVerifications = Verification::count();
            $totalClaims = Claim::count();
            $pendingClaims = Claim::where('processed', false)->count();
            
            // Distribución de veredictos
            $verdictDistribution = Verification::select('verdict', DB::raw('count(*) as count'))
                ->groupBy('verdict')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->verdict => $item->count];
                })
                ->toArray();
            
            // Veredictos por categoría
            $categoryStats = ClaimCategory::withCount(['claims' => function($query) {
                    $query->whereHas('verification');
                }])
                ->get()
                ->map(function ($category) {
                    // Distribución de veredictos para esta categoría
                    $verdicts = Verification::select('verdict', DB::raw('count(*) as count'))
                        ->whereHas('claim', function ($query) use ($category) {
                            $query->where('claim_category_id', $category->id);
                        })
                        ->groupBy('verdict')
                        ->get()
                        ->mapWithKeys(function ($item) {
                            return [$item->verdict => $item->count];
                        })
                        ->toArray();
                        
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'claims_count' => $category->claims_count,
                        'verdicts' => $verdicts
                    ];
                });
            
            // Fuentes más verificadas
            $topSources = Claim::select('source', DB::raw('count(*) as count'))
                ->whereHas('verification')
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
                
            // Tendencia de verificaciones en el tiempo (últimos 30 días)
            $verificationTrend = Verification::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            // Estadísticas de rendimiento
            $avgProcessingTime = Claim::whereHas('verification')
                ->whereNotNull('created_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, claims.created_at, verifications.created_at)) as avg_time'))
                ->join('verifications', 'claims.id', '=', 'verifications.claim_id')
                ->value('avg_time');
            
            return view('verificador.stats', compact(
                'totalVerifications',
                'totalClaims',
                'pendingClaims',
                'verdictDistribution',
                'categoryStats',
                'topSources',
                'verificationTrend',
                'avgProcessingTime'
            ));
        });
    }
}