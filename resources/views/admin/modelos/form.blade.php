@extends('admin.layouts.app')

@section('title', isset($model->id) ? 'Editar Modelo' : 'Nuevo Modelo')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ isset($model->id) ? 'Editar: '.$model->name : 'Nuevo Modelo IA' }}</h1>
        <a href="{{ route('admin.modelos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @include('admin.partials.alerts')

    @php
        $action = isset($model->id)
            ? route('admin.modelos.update', $model)
            : route('admin.modelos.store');
        $method = isset($model->id) ? 'PUT' : 'POST';
    @endphp

    <form method="POST" action="{{ $action }}">
        @csrf @method($method)

        <div class="row g-4">
            {{-- Columna principal --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Información básica</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $model->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $model->slug) }}">
                                <div class="form-text">Se genera automáticamente si se deja vacío.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Empresa <span class="text-danger">*</span></label>
                                <input type="text" name="company"
                                    class="form-control @error('company') is-invalid @enderror"
                                    value="{{ old('company', $model->company) }}" required>
                                @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company slug <span class="text-danger">*</span></label>
                                <input type="text" name="company_slug"
                                    class="form-control @error('company_slug') is-invalid @enderror"
                                    value="{{ old('company_slug', $model->company_slug) }}" required>
                                <div class="form-text">Ej: openai, anthropic, google</div>
                                @error('company_slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach(['llm' => 'LLM', 'multimodal' => 'Multimodal', 'image' => 'Imagen', 'audio' => 'Audio'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('type', $model->type) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Acceso <span class="text-danger">*</span></label>
                                <select name="access" class="form-select @error('access') is-invalid @enderror" required>
                                    @foreach(['closed' => 'Propietario', 'open' => 'Open Source', 'api-only' => 'Solo API'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('access', $model->access) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('access')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha de lanzamiento</label>
                                <input type="text" name="release_date"
                                    class="form-control @error('release_date') is-invalid @enderror"
                                    value="{{ old('release_date', $model->release_date) }}"
                                    placeholder="Ej: 2024-05">
                                @error('release_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $model->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL oficial</label>
                                <input type="url" name="official_url"
                                    class="form-control @error('official_url') is-invalid @enderror"
                                    value="{{ old('official_url', $model->official_url) }}">
                                @error('official_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Contexto y precios</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Ventana de contexto (tokens)</label>
                                <input type="number" name="context_window"
                                    class="form-control @error('context_window') is-invalid @enderror"
                                    value="{{ old('context_window', $model->context_window) }}">
                                @error('context_window')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contexto (etiqueta)</label>
                                <input type="text" name="context_window_label"
                                    class="form-control @error('context_window_label') is-invalid @enderror"
                                    value="{{ old('context_window_label', $model->context_window_label) }}"
                                    placeholder="Ej: 128K">
                                @error('context_window_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="has_free_tier" value="1"
                                        id="has_free_tier" {{ old('has_free_tier', $model->has_free_tier) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_free_tier">Tiene capa gratuita</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio input ($/M tokens)</label>
                                <input type="number" step="0.001" name="price_input"
                                    class="form-control @error('price_input') is-invalid @enderror"
                                    value="{{ old('price_input', $model->price_input) }}">
                                @error('price_input')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio output ($/M tokens)</label>
                                <input type="number" step="0.001" name="price_output"
                                    class="form-control @error('price_output') is-invalid @enderror"
                                    value="{{ old('price_output', $model->price_output) }}">
                                @error('price_output')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Benchmarks</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">MMLU (%)</label>
                                <input type="number" step="0.1" name="score_mmlu"
                                    class="form-control @error('score_mmlu') is-invalid @enderror"
                                    value="{{ old('score_mmlu', $model->score_mmlu) }}">
                                @error('score_mmlu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">HumanEval (%)</label>
                                <input type="number" step="0.1" name="score_humaneval"
                                    class="form-control @error('score_humaneval') is-invalid @enderror"
                                    value="{{ old('score_humaneval', $model->score_humaneval) }}">
                                @error('score_humaneval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">MATH (%)</label>
                                <input type="number" step="0.1" name="score_math"
                                    class="form-control @error('score_math') is-invalid @enderror"
                                    value="{{ old('score_math', $model->score_math) }}">
                                @error('score_math')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Capacidades</div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach([
                                'cap_text'         => 'Texto',
                                'cap_image_input'  => 'Imagen (entrada)',
                                'cap_image_output' => 'Imagen (salida)',
                                'cap_code'         => 'Código',
                                'cap_voice'        => 'Voz',
                                'cap_web_search'   => 'Búsqueda web',
                                'cap_files'        => 'Archivos',
                                'cap_reasoning'    => 'Razonamiento avanzado',
                            ] as $field => $label)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1"
                                        id="{{ $field }}" {{ old($field, $model->$field ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna lateral --}}
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Publicación</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" name="sort_order"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                value="{{ old('sort_order', $model->sort_order ?? 99) }}">
                            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="featured" value="1"
                                id="featured" {{ old('featured', $model->featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Destacado</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="active" value="1"
                                id="active" {{ old('active', $model->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Activo (visible)</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ isset($model->id) ? 'Actualizar' : 'Crear modelo' }}
                            </button>
                            <a href="{{ route('admin.modelos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('name').addEventListener('blur', function () {
    const slug = document.getElementById('slug');
    if (!slug.value) {
        slug.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
</script>
@endpush
