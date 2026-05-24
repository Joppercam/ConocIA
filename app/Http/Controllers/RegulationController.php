<?php

namespace App\Http\Controllers;

use App\Models\Regulation;

class RegulationController extends Controller
{
    public function index()
    {
        $all          = Regulation::orderBy('scope')->orderByDesc('date_introduced')->get();
        $featured     = $all->where('slug', 'proyecto-de-ley-de-sistemas-de-inteligencia-artificial-boletin-16821-19')->first()
                        ?? $all->where('scope', 'chile')->where('status', 'en_tramitacion')->first();
        $chile        = $all->where('scope', 'chile')->where('id', '!=', optional($featured)->id);
        $internacional = $all->where('scope', 'internacional');
        $updatedAt    = $all->max('updated_at');

        return view('regulacion.index', compact('all', 'featured', 'chile', 'internacional', 'updatedAt'));
    }

    public function show(string $slug)
    {
        $regulation = Regulation::where('slug', $slug)->firstOrFail();
        $others     = Regulation::where('id', '!=', $regulation->id)
            ->orderByDesc('date_introduced')
            ->limit(3)
            ->get();

        return view('regulacion.show', compact('regulation', 'others'));
    }
}
