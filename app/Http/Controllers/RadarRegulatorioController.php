<?php

namespace App\Http\Controllers;

use App\Models\RadarRegulatorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RadarRegulatorioController extends Controller
{
    public function index(Request $request)
    {
        $tipo      = $request->get('tipo');
        $estado    = $request->get('estado');
        $relevancia = $request->get('relevancia');

        $query = RadarRegulatorio::published()->orderByDesc('fecha_evento')->orderByDesc('published_at');

        if ($tipo)      $query->where('tipo', $tipo);
        if ($estado)    $query->where('estado', $estado);
        if ($relevancia) $query->where('relevancia', $relevancia);

        $items = $query->paginate(12)->withQueryString();

        $stats = Cache::remember('radar_regulatorio_stats', 3600, fn() => [
            'total'      => RadarRegulatorio::published()->count(),
            'alta'       => RadarRegulatorio::published()->where('relevancia', 'alta')->count(),
            'en_tramite' => RadarRegulatorio::published()->where('estado', 'en_tramite')->count(),
            'vigente'    => RadarRegulatorio::published()->whereIn('estado', ['vigente', 'promulgado', 'aprobado'])->count(),
        ]);

        $recientes = Cache::remember('radar_regulatorio_recientes', 900,
            fn() => RadarRegulatorio::published()->where('relevancia', 'alta')->orderByDesc('published_at')->take(3)->get()
        );

        return view('radar-regulatorio.index', compact('items', 'stats', 'recientes', 'tipo', 'estado', 'relevancia'));
    }

    public function show(string $slug)
    {
        $item = RadarRegulatorio::published()->where('slug', $slug)->firstOrFail();

        $relacionados = RadarRegulatorio::published()
            ->where('slug', '!=', $slug)
            ->where(fn($q) => $q->where('tipo', $item->tipo)->orWhere('organismo', $item->organismo))
            ->orderByDesc('fecha_evento')
            ->take(4)
            ->get();

        return view('radar-regulatorio.show', compact('item', 'relacionados'));
    }
}
