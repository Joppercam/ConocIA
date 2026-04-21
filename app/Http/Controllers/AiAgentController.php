<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Models\News;

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

        // Noticias recientes sobre agentes IA
        $agentNews = News::with('category')
            ->where('status', 'published')
            ->where(function ($q) {
                $q->where('title', 'like', '%agente%')
                  ->orWhere('title', 'like', '%agent%')
                  ->orWhere('title', 'like', '%autónomo%')
                  ->orWhere('title', 'like', '%autonomous%')
                  ->orWhere('title', 'like', '%agentic%')
                  ->orWhere('excerpt', 'like', '%agente de IA%')
                  ->orWhere('excerpt', 'like', '%AI agent%');
            })
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('agents.index', compact('agents', 'categories', 'agentNews'));
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
