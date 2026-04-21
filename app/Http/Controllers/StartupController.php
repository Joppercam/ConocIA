<?php

namespace App\Http\Controllers;

use App\Models\Startup;

class StartupController extends Controller
{
    public function index()
    {
        $startups = Startup::active()
            ->orderByDesc('featured')
            ->orderByDesc('last_funding_date')
            ->paginate(18);

        $sectors = Startup::active()->whereNotNull('sector')
            ->distinct()->pluck('sector')->sort()->values();

        $stages = ['seed', 'series-a', 'series-b', 'series-c', 'pre-seed', 'public', 'acquired', 'stealth'];

        return view('startups.index', compact('startups', 'sectors', 'stages'));
    }

    public function show(Startup $startup)
    {
        abort_unless($startup->active, 404);

        $related = Startup::active()
            ->where('id', '!=', $startup->id)
            ->where('sector', $startup->sector)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('startups.show', compact('startup', 'related'));
    }
}
