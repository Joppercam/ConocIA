@extends('admin.layouts.app')

@section('title', isset($agent->id) ? 'Editar Agente IA' : 'Nuevo Agente IA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ isset($agent->id) ? 'Editar: '.$agent->name : 'Nuevo Agente IA' }}</h1>
        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @include('admin.partials.alerts')

    @php
        $action = isset($agent->id)
            ? route('admin.agents.update', $agent)
            : route('admin.agents.store');
        $method = isset($agent->id) ? 'PUT' : 'POST';
    @endphp

    <form method="POST" action="{{ $action }}">
        @csrf @method($method)

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Información básica</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="agent-name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $agent->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" id="agent-slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $agent->slug) }}">
                                <div class="form-text">Se genera automáticamente si se deja vacío.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $agent->tagline) }}"
                                    placeholder="Descripción de 1 línea">
                                @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $agent->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="website_url"
                                    class="form-control @error('website_url') is-invalid @enderror"
                                    value="{{ old('website_url', $agent->website_url) }}">
                                @error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">GitHub URL</label>
                                <input type="url" name="github_url"
                                    class="form-control @error('github_url') is-invalid @enderror"
                                    value="{{ old('github_url', $agent->github_url) }}">
                                @error('github_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Logo URL</label>
                                <input type="url" name="logo"
                                    class="form-control @error('logo') is-invalid @enderror"
                                    value="{{ old('logo', $agent->logo) }}">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Clasificación</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Categoría</label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(\App\Models\AiAgent::categoryLabels() as $val => $label)
                                        <option value="{{ $val }}" {{ old('category', $agent->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach(['open-source' => 'Open Source', 'closed' => 'Propietario', 'api' => 'Solo API'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('type', $agent->type ?? 'open-source') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Framework base</label>
                                <select name="framework" class="form-select @error('framework') is-invalid @enderror">
                                    <option value="">— Ninguno —</option>
                                    @foreach(['langchain'=>'LangChain','autogen'=>'AutoGen','crewai'=>'CrewAI','langgraph'=>'LangGraph','custom'=>'Custom','none'=>'Independiente'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('framework', $agent->framework) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('framework')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Precio</label>
                                <select name="pricing_model" class="form-select @error('pricing_model') is-invalid @enderror" required>
                                    @foreach(['free'=>'Gratuito','freemium'=>'Freemium','paid'=>'De pago','open-source'=>'Open Source'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('pricing_model', $agent->pricing_model ?? 'open-source') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('pricing_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stars GitHub</label>
                                <input type="number" name="stars_github"
                                    class="form-control @error('stars_github') is-invalid @enderror"
                                    value="{{ old('stars_github', $agent->stars_github) }}">
                                @error('stars_github')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="requires_api_key" value="1"
                                        id="requires_api_key" {{ old('requires_api_key', $agent->requires_api_key ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_api_key">Requiere API key</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_free_tier" value="1"
                                        id="has_free_tier" {{ old('has_free_tier', $agent->has_free_tier ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_free_tier">Tiene capa gratuita</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Capacidades (una por línea)</label>
                                <textarea name="capabilities_raw" rows="4" class="form-control">{{ old('capabilities_raw', is_array($agent->capabilities) ? implode("\n", $agent->capabilities) : '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Casos de uso (uno por línea)</label>
                                <textarea name="use_cases_raw" rows="4" class="form-control">{{ old('use_cases_raw', is_array($agent->use_cases) ? implode("\n", $agent->use_cases) : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Publicación</div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="featured" value="1"
                                id="featured" {{ old('featured', $agent->featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Destacado</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="active" value="1"
                                id="active" {{ old('active', $agent->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Activo (visible)</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ isset($agent->id) ? 'Actualizar' : 'Crear agente' }}
                            </button>
                            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
document.getElementById('agent-name').addEventListener('blur', function () {
    const slug = document.getElementById('agent-slug');
    if (!slug.value) {
        slug.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
</script>
@endpush
