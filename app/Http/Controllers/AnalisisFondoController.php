<?php

namespace App\Http\Controllers;

use App\Models\AnalisisFondo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalisisFondoController extends Controller
{
    public function index()
    {
        $featured = Cache::remember('analisis_featured', 1800, fn() =>
            AnalisisFondo::with('author')->published()->featured()->latest('published_at')->first()
        );

        $analyses = AnalisisFondo::with('author')
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $topics = Cache::remember('analisis_topics', 3600, fn() =>
            AnalisisFondo::published()
                ->selectRaw('topic, COUNT(*) as count')
                ->groupBy('topic')
                ->orderByDesc('count')
                ->pluck('count', 'topic')
        );

        return view('analisis-fondo.index', compact('featured', 'analyses', 'topics'));
    }

    public function show(string $slug)
    {
        $analysis = AnalisisFondo::with('author')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        DB::table('analisis_fondo')->where('id', $analysis->id)->increment('views');

        $related = Cache::remember("analisis_related_{$analysis->id}", 1800, fn() =>
            AnalisisFondo::published()
                ->where('id', '!=', $analysis->id)
                ->where('category', $analysis->category)
                ->latest('published_at')
                ->take(3)
                ->get()
        );

        return view('analisis-fondo.show', compact('analysis', 'related'));
    }
}
