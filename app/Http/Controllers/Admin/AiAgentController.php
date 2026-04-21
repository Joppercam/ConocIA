<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiAgentController extends Controller
{
    public function index()
    {
        $agents = AiAgent::orderByDesc('stars_github')->orderByDesc('created_at')->paginate(30);
        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        return view('admin.agents.form', ['agent' => new AiAgent()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug']         = $data['slug'] ?: Str::slug($data['name']);
        $data['capabilities'] = $this->parseJsonList($request->input('capabilities_raw'));
        $data['use_cases']    = $this->parseJsonList($request->input('use_cases_raw'));
        AiAgent::create($data);
        return redirect()->route('admin.agents.index')->with('success', 'Agente creado correctamente.');
    }

    public function edit(AiAgent $agent)
    {
        return view('admin.agents.form', compact('agent'));
    }

    public function update(Request $request, AiAgent $agent)
    {
        $data = $this->validated($request, $agent->id);
        $data['slug']         = $data['slug'] ?: Str::slug($data['name']);
        $data['capabilities'] = $this->parseJsonList($request->input('capabilities_raw'));
        $data['use_cases']    = $this->parseJsonList($request->input('use_cases_raw'));
        $agent->update($data);
        return redirect()->route('admin.agents.index')->with('success', 'Agente actualizado correctamente.');
    }

    public function destroy(AiAgent $agent)
    {
        $agent->delete();
        return redirect()->route('admin.agents.index')->with('success', 'Agente eliminado.');
    }

    public function toggleActive(AiAgent $agent)
    {
        $agent->update(['active' => !$agent->active]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'             => 'required|string|max:120',
            'slug'             => "nullable|string|unique:ai_agents,slug,{$ignoreId}",
            'tagline'          => 'nullable|string|max:200',
            'description'      => 'nullable|string',
            'logo'             => 'nullable|url',
            'website_url'      => 'nullable|url',
            'github_url'       => 'nullable|url',
            'category'         => 'nullable|string|max:60',
            'type'             => 'required|in:open-source,closed,api',
            'framework'        => 'nullable|string|max:60',
            'stars_github'     => 'nullable|integer',
            'requires_api_key' => 'boolean',
            'has_free_tier'    => 'boolean',
            'pricing_model'    => 'required|in:free,freemium,paid,open-source',
            'source_url'       => 'nullable|url',
            'featured'         => 'boolean',
            'active'           => 'boolean',
        ]);
    }

    private function parseJsonList(?string $raw): ?array
    {
        if (empty($raw)) return null;
        $lines = array_filter(array_map('trim', explode("\n", $raw)));
        return !empty($lines) ? array_values($lines) : null;
    }
}
