<?php

namespace App\Http\Controllers;

use App\Models\EcosystemActor;

class EcosystemController extends Controller
{
    public function index()
    {
        $actors = EcosystemActor::orderBy('type')->orderBy('name')->get();
        $total  = $actors->count();

        $byType = $actors->groupBy('type');

        $typeCounts = [
            'universidad'          => $actors->where('type', 'universidad')->count(),
            'centro_investigacion' => $actors->where('type', 'centro_investigacion')->count(),
            'startup'              => $actors->where('type', 'startup')->count(),
            'gobierno'             => $actors->where('type', 'gobierno')->count(),
            'organizacion'         => $actors->where('type', 'organizacion')->count(),
        ];

        return view('ecosistema.index', compact('actors', 'total', 'typeCounts'));
    }
}
