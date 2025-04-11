<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\Claim;
use App\Models\ClaimCategory;
use Illuminate\Http\Request;
use App\Services\VerificadorCacheService;

class VerificadorController extends Controller
{
    protected $cacheService;
    
    public function __construct(VerificadorCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Mostrar la página principal del verificador
     */
    public function index(Request $request)
    {
        return $this->cacheService->getVerificationsList($request->all(), function () use ($request) {
            // Filtros
            $categoryId = $request->input('category');
            $verdict = $request->input('verdict');
            
            // Consulta base para las verificaciones
            $query = Verification::with(['claim.category'])
                ->orderBy('created_at', 'desc');
            
            // Aplicar filtros si están presentes
            if ($categoryId) {
                $query->whereHas('claim', function ($q) use ($categoryId) {
                    $q->where('claim_category_id', $categoryId);
                });
            }
            
            if ($verdict) {
                $query->where('verdict', $verdict);
            }
            
            // Obtener verificaciones destacadas desde el caché
            $featuredVerifications = $this->cacheService->getFeaturedVerifications(function () {
                return Verification::with(['claim.category'])
                    ->orderBy('views_count', 'desc')
                    ->whereBetween('created_at', [now()->subDays(7), now()])
                    ->take(2)
                    ->get()
                    ->each(function ($verification) {
                        $this->addVerdictAttributes($verification);
                    });
            });
            
            // Obtener verificaciones filtradas y paginadas
            $verifications = $query->paginate(10)
                ->each(function ($verification) {
                    $this->addVerdictAttributes($verification);
                });
            
            // Obtener todas las categorías para el filtro
            $categories = ClaimCategory::all();
            
            return view('verificador.index', compact('verifications', 'featuredVerifications', 'categories'));
        });
    }
    
    /**
     * Mostrar una verificación específica
     */
    public function show($id)
    {
        // Incrementar el contador de vistas
        Verification::where('id', $id)->increment('views_count');
        
        return $this->cacheService->getVerification($id, function () use ($id) {
            // Obtener la verificación con sus relaciones
            $verification = Verification::with(['claim.category'])
                ->findOrFail($id);
            
            $this->addVerdictAttributes($verification);
            
            // Obtener verificaciones relacionadas basadas en la categoría
            $relatedVerifications = Verification::with(['claim.category'])
                ->whereHas('claim', function ($query) use ($verification) {
                    $query->where('claim_category_id', $verification->claim->claim_category_id);
                })
                ->where('id', '!=', $verification->id)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->each(function ($verification) {
                    $this->addVerdictAttributes($verification);
                });
            
            return view('verificador.show', compact('verification', 'relatedVerifications'));
        });
    }
    
    /**
     * Invalidar manualmente el caché para una verificación
     * (Útil para el panel de administración)
     */
    public function invalidateCache($id)
    {
        if (!auth()->user()->can('manage-cache')) {
            abort(403, 'No tienes permiso para realizar esta acción');
        }
        
        $this->cacheService->invalidateVerification($id);
        $this->cacheService->invalidateVerificationsList();
        $this->cacheService->invalidateFeaturedVerifications();
        $this->cacheService->invalidateStats();
        
        return redirect()->back()
            ->with('success', 'Caché invalidado correctamente');
    }
    
    /**
     * Añadir atributos relacionados con el veredicto a un modelo de verificación
     */
    private function addVerdictAttributes($verification)
    {
        // Añadir clase CSS para el veredicto
        $verification->verdict_class = match($verification->verdict) {
            'true' => 'true',
            'partially_true' => 'partially_true',
            'false' => 'false',
            default => 'unverifiable',
        };
        
        // Añadir etiqueta para el veredicto
        $verification->verdict_label = match($verification->verdict) {
            'true' => 'Verdadero',
            'partially_true' => 'Parcialmente verdadero',
            'false' => 'Falso',
            default => 'No verificable',
        };
        
        return $verification;
    }
}