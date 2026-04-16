<?php

namespace App\Http\Controllers;

use App\Models\EstadoArte;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EstadoArteController extends Controller
{
    public function index()
    {
        // Última edición por cada subcampo
        $latestBySubfield = Cache::remember('estado_arte_latest', 3600, fn() =>
            EstadoArte::published()
                ->orderByDesc('week_start')
                ->get()
                ->groupBy('subfield')
                ->map(fn($group) => $group->first())
        );

        $recent = Cache::remember('estado_arte_recent', 3600, fn() =>
            EstadoArte::published()->latest('week_start')->take(6)->get()
        );

        $subfields = Cache::remember('estado_arte_subfields', 7200, fn() =>
            EstadoArte::published()
                ->selectRaw('subfield, subfield_label, COUNT(*) as count')
                ->groupBy('subfield', 'subfield_label')
                ->orderByDesc('count')
                ->get()
        );

        return view('estado-arte.index', compact('latestBySubfield', 'recent', 'subfields'));
    }

    public function show(string $slug)
    {
        $digest = EstadoArte::where('slug', $slug)->published()->firstOrFail();

        DB::table('estado_arte')->where('id', $digest->id)->increment('views');

        $previousEditions = Cache::remember("estado_arte_prev_{$digest->subfield}", 3600, fn() =>
            EstadoArte::published()
                ->where('subfield', $digest->subfield)
                ->where('id', '!=', $digest->id)
                ->latest('week_start')
                ->take(5)
                ->get()
        );

        $sourceNews = Cache::remember("estado_arte_news_{$digest->id}", 3600,
            fn() => $digest->sourceNews()
        );

        return view('estado-arte.show', compact('digest', 'previousEditions', 'sourceNews'));
    }

    public function bySubfield(string $subfield)
    {
        $digests = EstadoArte::published()
            ->bySubfield($subfield)
            ->latest('week_start')
            ->paginate(10);

        $subfieldLabel = $digests->first()?->subfield_label ?? $subfield;

        return view('estado-arte.by-subfield', compact('digests', 'subfield', 'subfieldLabel'));
    }
}
