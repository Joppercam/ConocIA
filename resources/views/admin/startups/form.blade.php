@extends('admin.layouts.app')

@section('title', isset($startup->id) ? 'Editar Startup' : 'Nueva Startup')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ isset($startup->id) ? 'Editar: '.$startup->name : 'Nueva Startup IA' }}</h1>
        <a href="{{ route('admin.startups.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @include('admin.partials.alerts')

    @php
        $action = isset($startup->id)
            ? route('admin.startups.update', $startup)
            : route('admin.startups.store');
        $method = isset($startup->id) ? 'PUT' : 'POST';
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
                                <input type="text" name="name" id="startup-name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $startup->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" id="startup-slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $startup->slug) }}">
                                <div class="form-text">Se genera automáticamente si se deja vacío.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="tagline"
                                    class="form-control @error('tagline') is-invalid @enderror"
                                    value="{{ old('tagline', $startup->tagline) }}"
                                    placeholder="Descripción de 1 línea">
                                @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $startup->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website URL</label>
                                <input type="url" name="website_url"
                                    class="form-control @error('website_url') is-invalid @enderror"
                                    value="{{ old('website_url', $startup->website_url) }}">
                                @error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Logo URL</label>
                                <input type="url" name="logo"
                                    class="form-control @error('logo') is-invalid @enderror"
                                    value="{{ old('logo', $startup->logo) }}">
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
                                <label class="form-label">Sector</label>
                                <select name="sector" class="form-select @error('sector') is-invalid @enderror">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(\App\Models\Startup::sectorLabels() as $val => $label)
                                        <option value="{{ $val }}" {{ old('sector', $startup->sector) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('sector')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Etapa</label>
                                <select name="stage" class="form-select @error('stage') is-invalid @enderror">
                                    <option value="">— Seleccionar —</option>
                                    @foreach(['pre-seed'=>'Pre-seed','seed'=>'Seed','series-a'=>'Serie A','series-b'=>'Serie B','series-c'=>'Serie C+','public'=>'Pública','acquired'=>'Adquirida','stealth'=>'Stealth'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('stage', $startup->stage) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('stage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Año de fundación</label>
                                <input type="number" name="founded_year" min="1990" max="{{ date('Y') + 1 }}"
                                    class="form-control @error('founded_year') is-invalid @enderror"
                                    value="{{ old('founded_year', $startup->founded_year) }}">
                                @error('founded_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">País</label>
                                <input type="text" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $startup->country) }}">
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $startup->city) }}">
                                @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Financiamiento</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Total recaudado (millones USD)</label>
                                <input type="number" step="0.01" name="total_funding_usd"
                                    class="form-control @error('total_funding_usd') is-invalid @enderror"
                                    value="{{ old('total_funding_usd', $startup->total_funding_usd) }}">
                                @error('total_funding_usd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha última ronda</label>
                                <input type="date" name="last_funding_date"
                                    class="form-control @error('last_funding_date') is-invalid @enderror"
                                    value="{{ old('last_funding_date', $startup->last_funding_date?->format('Y-m-d')) }}">
                                @error('last_funding_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Inversores (uno por línea)</label>
                                <textarea name="investors_raw" rows="3" class="form-control">{{ old('investors_raw', is_array($startup->investors) ? implode("\n", $startup->investors) : '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Productos principales (uno por línea)</label>
                                <textarea name="products_raw" rows="3" class="form-control">{{ old('products_raw', is_array($startup->products) ? implode("\n", $startup->products) : '') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL fuente</label>
                                <input type="url" name="source_url"
                                    class="form-control @error('source_url') is-invalid @enderror"
                                    value="{{ old('source_url', $startup->source_url) }}">
                                @error('source_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                id="featured" {{ old('featured', $startup->featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Destacada</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="active" value="1"
                                id="active" {{ old('active', $startup->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Activa (visible)</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ isset($startup->id) ? 'Actualizar' : 'Crear startup' }}
                            </button>
                            <a href="{{ route('admin.startups.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
document.getElementById('startup-name').addEventListener('blur', function () {
    const slug = document.getElementById('startup-slug');
    if (!slug.value) {
        slug.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '')
            .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
</script>
@endpush
