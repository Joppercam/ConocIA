<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiModelController extends Controller
{
    public function index()
    {
        $models = AiModel::orderBy('sort_order')->get();
        return view('admin.modelos.index', compact('models'));
    }

    public function create()
    {
        return view('admin.modelos.form', ['model' => new AiModel()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        AiModel::create($data);
        return redirect()->route('admin.modelos.index')->with('success', 'Modelo creado correctamente.');
    }

    public function edit(AiModel $modelo)
    {
        return view('admin.modelos.form', ['model' => $modelo]);
    }

    public function update(Request $request, AiModel $modelo)
    {
        $data = $this->validated($request, $modelo->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $modelo->update($data);
        return redirect()->route('admin.modelos.index')->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroy(AiModel $modelo)
    {
        $modelo->delete();
        return redirect()->route('admin.modelos.index')->with('success', 'Modelo eliminado.');
    }

    public function toggleActive(AiModel $modelo)
    {
        $modelo->update(['active' => !$modelo->active]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'                => 'required|string|max:100',
            'slug'                => "nullable|string|unique:ai_models,slug,{$ignoreId}",
            'company'             => 'required|string|max:100',
            'company_slug'        => 'required|string|max:100',
            'type'                => 'required|in:llm,multimodal,image,audio',
            'access'              => 'required|in:closed,open,api-only',
            'release_date'        => 'nullable|string|max:50',
            'context_window'      => 'nullable|integer',
            'context_window_label'=> 'nullable|string|max:20',
            'price_input'         => 'nullable|numeric',
            'price_output'        => 'nullable|numeric',
            'has_free_tier'       => 'boolean',
            'score_mmlu'          => 'nullable|numeric',
            'score_humaneval'     => 'nullable|numeric',
            'score_math'          => 'nullable|numeric',
            'cap_text'            => 'boolean',
            'cap_image_input'     => 'boolean',
            'cap_image_output'    => 'boolean',
            'cap_code'            => 'boolean',
            'cap_voice'           => 'boolean',
            'cap_web_search'      => 'boolean',
            'cap_files'           => 'boolean',
            'cap_reasoning'       => 'boolean',
            'description'         => 'nullable|string',
            'official_url'        => 'nullable|url',
            'featured'            => 'boolean',
            'active'              => 'boolean',
            'sort_order'          => 'integer',
        ]);
    }
}
