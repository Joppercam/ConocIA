<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regulation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegulationController extends Controller
{
    public function index()
    {
        $regulations = Regulation::orderBy('scope')->orderByDesc('date_introduced')->get();
        return view('admin.regulations.index', compact('regulations'));
    }

    public function create()
    {
        return view('admin.regulations.form', ['regulation' => new Regulation()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']);
        Regulation::create($data);
        return redirect()->route('admin.regulations.index')->with('success', 'Regulación creada correctamente.');
    }

    public function edit(Regulation $regulation)
    {
        return view('admin.regulations.form', compact('regulation'));
    }

    public function update(Request $request, Regulation $regulation)
    {
        $data = $this->validated($request, $regulation);
        $regulation->update($data);
        return redirect()->route('admin.regulations.index')->with('success', 'Regulación actualizada correctamente.');
    }

    public function destroy(Regulation $regulation)
    {
        $regulation->delete();
        return redirect()->route('admin.regulations.index')->with('success', 'Regulación eliminada.');
    }

    private function validated(Request $request, ?Regulation $regulation = null): array
    {
        $slugUnique = 'unique:regulations,slug';
        if ($regulation && $regulation->exists) {
            $slugUnique .= ',' . $regulation->id;
        }

        $request->validate([
            'title'            => 'required|string|max:500',
            'slug'             => ['nullable', 'string', 'max:500', $slugUnique],
            'scope'            => 'required|in:chile,internacional',
            'status'           => 'required|in:en_tramitacion,aprobada,vigente,rechazada,propuesta',
            'institution'      => 'required|string|max:300',
            'summary'          => 'required|string',
            'content'          => 'nullable|string',
            'impact_laboral'   => 'nullable|string',
            'impact_economico' => 'nullable|string',
            'impact_social'    => 'nullable|string',
            'source_url'       => 'nullable|url|max:1000',
            'date_introduced'  => 'nullable|date',
            'date_updated'     => 'nullable|date',
        ]);

        $data = $request->only([
            'title', 'scope', 'status', 'institution', 'summary',
            'content', 'impact_laboral', 'impact_economico', 'impact_social',
            'source_url', 'date_introduced', 'date_updated',
        ]);

        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->slug);
        }

        return $data;
    }
}
