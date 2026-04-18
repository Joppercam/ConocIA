<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadoArte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EstadoArteAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = EstadoArte::query();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subfield_label', 'like', "%{$search}%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($subfield = $request->subfield) {
            $query->where('subfield', $subfield);
        }

        $digests = $query->orderByDesc('week_start')->paginate(20)->withQueryString();

        $subfields = EstadoArte::selectRaw('subfield, subfield_label')
            ->distinct()->get();

        return view('admin.estado-arte.index', compact('digests', 'subfields'));
    }

    public function edit(EstadoArte $estadoArte)
    {
        return view('admin.estado-arte.edit', ['digest' => $estadoArte]);
    }

    public function update(Request $request, EstadoArte $estadoArte)
    {
        $validated = $request->validate([
            'title'                  => 'required|max:255',
            'excerpt'                => 'nullable|string',
            'content'                => 'nullable|string',
            'subfield_label'         => 'nullable|max:100',
            'period_label'           => 'nullable|max:100',
            'featured'               => 'boolean',
            'status'                 => 'required|in:draft,published',
            'key_developments_raw'   => 'nullable|string',
        ]);

        $estadoArte->title         = $validated['title'];
        $estadoArte->excerpt       = $validated['excerpt'] ?? $estadoArte->excerpt;
        $estadoArte->content       = $validated['content'] ?? $estadoArte->content;
        $estadoArte->subfield_label = $validated['subfield_label'] ?? $estadoArte->subfield_label;
        $estadoArte->period_label  = $validated['period_label'] ?? $estadoArte->period_label;
        $estadoArte->featured      = $request->boolean('featured');
        $estadoArte->status        = $validated['status'];

        if (!empty($validated['key_developments_raw'])) {
            $estadoArte->key_developments = array_values(array_filter(
                array_map('trim', explode("\n", $validated['key_developments_raw']))
            ));
        }

        if ($estadoArte->status === 'published' && !$estadoArte->published_at) {
            $estadoArte->published_at = now();
        }

        $estadoArte->save();

        return redirect()->route('admin.estado-arte.index')
            ->with('success', "Digest \"{$estadoArte->title}\" actualizado.");
    }

    public function destroy(EstadoArte $estadoArte)
    {
        $estadoArte->delete();
        return redirect()->route('admin.estado-arte.index')
            ->with('success', 'Digest eliminado.');
    }

    public function toggleStatus(EstadoArte $estadoArte)
    {
        $estadoArte->status = $estadoArte->status === 'published' ? 'draft' : 'published';
        if ($estadoArte->status === 'published' && !$estadoArte->published_at) {
            $estadoArte->published_at = now();
        }
        $estadoArte->save();

        return back()->with('success', "Estado cambiado a {$estadoArte->status}.");
    }

    public function runGenerate(Request $request)
    {
        $subfield = $request->subfield;
        $cmd = $subfield
            ? "digest:generate --subfield={$subfield} --force"
            : 'digest:generate --all --force';

        Artisan::queue($cmd);

        return back()->with('success', "Comando {$cmd} lanzado. Los digests aparecerán en unos minutos.");
    }
}
