<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;

class AiAgentController extends Controller
{
    public function index()
    {
        $agents = AiAgent::active()
            ->orderByDesc('featured')
            ->orderByDesc('stars_github')
            ->paginate(18);

        $categories = AiAgent::active()->whereNotNull('category')
            ->distinct()->pluck('category')->sort()->values();

        return view('agents.index', compact('agents', 'categories'));
    }

    public function show(AiAgent $agent)
    {
        abort_unless($agent->active, 404);

        $related = AiAgent::active()
            ->where('id', '!=', $agent->id)
            ->where('category', $agent->category)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('agents.show', compact('agent', 'related'));
    }
}
