<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Startup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StartupController extends Controller
{
    public function index()
    {
        $startups = Startup::orderByDesc('last_funding_date')->orderByDesc('created_at')->paginate(30);
        return view('admin.startups.index', compact('startups'));
    }

    public function create()
    {
        return view('admin.startups.form', ['startup' => new Startup()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['investors'] = $this->parseJsonList($request->input('investors_raw'));
        $data['products']  = $this->parseJsonList($request->input('products_raw'));
        Startup::create($data);
        return redirect()->route('admin.startups.index')->with('success', 'Startup creada correctamente.');
    }

    public function edit(Startup $startup)
    {
        return view('admin.startups.form', compact('startup'));
    }

    public function update(Request $request, Startup $startup)
    {
        $data = $this->validated($request, $startup->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['investors'] = $this->parseJsonList($request->input('investors_raw'));
        $data['products']  = $this->parseJsonList($request->input('products_raw'));
        $startup->update($data);
        return redirect()->route('admin.startups.index')->with('success', 'Startup actualizada correctamente.');
    }

    public function destroy(Startup $startup)
    {
        $startup->delete();
        return redirect()->route('admin.startups.index')->with('success', 'Startup eliminada.');
    }

    public function toggleActive(Startup $startup)
    {
        $startup->update(['active' => !$startup->active]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:120',
            'slug'              => "nullable|string|unique:startups,slug,{$ignoreId}",
            'tagline'           => 'nullable|string|max:200',
            'description'       => 'nullable|string',
            'logo'              => 'nullable|url',
            'website_url'       => 'nullable|url',
            'founded_year'      => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'country'           => 'nullable|string|max:60',
            'city'              => 'nullable|string|max:80',
            'sector'            => 'nullable|string|max:60',
            'stage'             => 'nullable|string|max:30',
            'total_funding_usd' => 'nullable|numeric',
            'last_funding_date' => 'nullable|date',
            'source_url'        => 'nullable|url',
            'featured'          => 'boolean',
            'active'            => 'boolean',
        ]);
    }

    private function parseJsonList(?string $raw): ?array
    {
        if (empty($raw)) return null;
        $lines = array_filter(array_map('trim', explode("\n", $raw)));
        return !empty($lines) ? array_values($lines) : null;
    }
}
