@extends('admin.layouts.app')

@section('title', isset($event->id) ? 'Editar Evento' : 'Nuevo Evento')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ isset($event->id) ? 'Editar: '.$event->title : 'Nuevo Evento IA' }}</h1>
        <a href="{{ route('admin.agenda.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @include('admin.partials.alerts')

    @php
        $action = isset($event->id)
            ? route('admin.agenda.update', $event)
            : route('admin.agenda.store');
        $method = isset($event->id) ? 'PUT' : 'POST';
    @endphp

    <form method="POST" action="{{ $action }}">
        @csrf @method($method)

        <div class="row g-4">
            {{-- Columna principal --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Información del evento</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $event->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug"
                                    class="form-control @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $event->slug) }}">
                                <div class="form-text">Se genera automáticamente si se deja vacío.</div>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach(['conference' => 'Conferencia', 'webinar' => 'Webinar', 'deadline' => 'Deadline', 'workshop' => 'Workshop', 'summit' => 'Summit'] as $v => $l)
                                        <option value="{{ $v }}" {{ old('type', $event->type) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $event->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Fechas y lugar</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d') : '') }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha fin</label>
                                <input type="date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date', $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') : '') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Lugar</label>
                                <input type="text" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $event->location) }}"
                                    placeholder="Ej: Vancouver, Canadá">
                                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_online" value="1"
                                        id="is_online" {{ old('is_online', $event->is_online ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_online">Es online</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL del evento</label>
                                <input type="url" name="url"
                                    class="form-control @error('url') is-invalid @enderror"
                                    value="{{ old('url', $event->url) }}">
                                @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Organización y precios</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Organizador</label>
                                <input type="text" name="organizer"
                                    class="form-control @error('organizer') is-invalid @enderror"
                                    value="{{ old('organizer', $event->organizer) }}">
                                @error('organizer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_free" value="1"
                                        id="is_free" {{ old('is_free', $event->is_free ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_free">Entrada gratuita</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna lateral --}}
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-semibold">Publicación</div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="featured" value="1"
                                id="featured" {{ old('featured', $event->featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Destacado</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="active" value="1"
                                id="active" {{ old('active', $event->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Activo (visible)</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ isset($event->id) ? 'Actualizar' : 'Crear evento' }}
                            </button>
                            <a href="{{ route('admin.agenda.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
document.getElementById('title').addEventListener('blur', function () {
    const slug = document.getElementById('slug');
    if (!slug.value) {
        slug.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
</script>
@endpush
