<?php

namespace App\Http\Controllers;

use App\Models\ConceptoIa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConceptoIaController extends Controller
{
    public function index()
    {
        $conceptos = ConceptoIa::published()
            ->orderBy('title')
            ->paginate(24);

        $featured = Cache::remember('conceptos_featured', 3600, fn() =>
            ConceptoIa::published()->featured()->latest('published_at')->take(3)->get()
        );

        $categories = Cache::remember('conceptos_categories', 3600, fn() =>
            ConceptoIa::published()
                ->selectRaw('category, COUNT(*) as count')
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderByDesc('count')
                ->pluck('count', 'category')
        );

        return view('conceptos-ia.index', compact('conceptos', 'featured', 'categories'));
    }

    public function show(string $slug)
    {
        $concepto = ConceptoIa::where('slug', $slug)->published()->firstOrFail();

        DB::table('conceptos_ia')->where('id', $concepto->id)->increment('views');

        $related = Cache::remember("concepto_related_{$concepto->id}", 3600,
            fn() => $concepto->getRelatedConceptModels(4)
        );

        return view('conceptos-ia.show', compact('concepto', 'related'));
    }
}
