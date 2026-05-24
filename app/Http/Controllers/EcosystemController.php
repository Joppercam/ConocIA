<?php

namespace App\Http\Controllers;

use App\Models\EcosystemActor;

class EcosystemController extends Controller
{
    public function index()
    {
        $actors = EcosystemActor::orderBy('type')->orderBy('name')->get();
        $total  = $actors->count();

        $typeCounts = [
            'universidad'          => $actors->where('type', 'universidad')->count(),
            'centro_investigacion' => $actors->where('type', 'centro_investigacion')->count(),
            'startup'              => $actors->where('type', 'startup')->count(),
            'gobierno'             => $actors->where('type', 'gobierno')->count(),
            'organizacion'         => $actors->where('type', 'organizacion')->count(),
        ];

        $academicos = $typeCounts['universidad'] + $typeCounts['centro_investigacion'];

        return view('ecosistema.index', compact('actors', 'total', 'typeCounts', 'academicos'));
    }

    public function show(string $slug)
    {
        $actor   = EcosystemActor::where('slug', $slug)->firstOrFail();
        $related = EcosystemActor::where('type', $actor->type)
            ->where('id', '!=', $actor->id)
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('ecosistema.show', compact('actor', 'related'));
    }
}
