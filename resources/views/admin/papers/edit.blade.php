@extends('admin.layouts.app')

@section('title', 'Editar Paper')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0"><i class="fas fa-edit me-2"></i>Editar Paper</h1>
        <div class="d-flex gap-2">
            @if($paper->slug)
            <a href="{{ route('papers.show', $paper->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i>Ver en portal
            </a>
            @endif
            <a href="{{ route('admin.papers.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger py-2 small">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.papers.update', $paper) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-3">
            {{-- Main column --}}
            <div class="col-lg-8">

                {{-- arXiv meta (readonly) --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Datos arXiv (solo lectura)</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small">arXiv ID</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->arxiv_id }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Categoría</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->arxiv_category }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Fecha arXiv</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->arxiv_published_date?->format('d/m/Y') }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Título original</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->original_title }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Autores</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->authorsFormatted() }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">URL arXiv</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $paper->arxiv_url }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Editorial title --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Título editorial (español)</div>
                    <div class="card-body">
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $paper->title) }}" required>
                        @error('title')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Resumen / Excerpt</div>
                    <div class="card-body">
                        <textarea name="excerpt" rows="3" class="form-control form-control-sm">{{ old('excerpt', $paper->excerpt) }}</textarea>
                    </div>
                </div>

                {{-- Content --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Contenido (HTML)</div>
                    <div class="card-body">
                        <textarea name="content" rows="18" class="form-control form-control-sm" style="font-family:monospace;font-size:.8rem;">{{ old('content', $paper->content) }}</textarea>
                    </div>
                </div>

                {{-- Key contributions --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Contribuciones clave <span class="text-muted fw-normal">(una por línea)</span></div>
                    <div class="card-body">
                        <textarea name="key_contributions_raw" rows="5" class="form-control form-control-sm">{{ old('key_contributions_raw', implode("\n", $paper->key_contributions ?? [])) }}</textarea>
                    </div>
                </div>

                {{-- Practical implications --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Implicaciones prácticas <span class="text-muted fw-normal">(una por línea)</span></div>
                    <div class="card-body">
                        <textarea name="practical_implications_raw" rows="4" class="form-control form-control-sm">{{ old('practical_implications_raw', implode("\n", $paper->practical_implications ?? [])) }}</textarea>
                    </div>
                </div>

                {{-- Abstract (readonly) --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Abstract original (solo lectura)</div>
                    <div class="card-body">
                        <p class="small text-muted mb-0" style="line-height:1.6;">{{ $paper->original_abstract }}</p>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Publish --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Publicación</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small">Estado</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="draft"     {{ old('status', $paper->status) === 'draft'     ? 'selected' : '' }}>Borrador</option>
                                <option value="published" {{ old('status', $paper->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="hidden" name="featured" value="0">
                            <input type="checkbox" name="featured" value="1" class="form-check-input" id="featured"
                                   {{ old('featured', $paper->featured) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="featured">Destacado</label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Dificultad</label>
                            <select name="difficulty_level" class="form-select form-select-sm">
                                <option value="">-- Sin asignar --</option>
                                <option value="básico"     {{ old('difficulty_level', $paper->difficulty_level) === 'básico'     ? 'selected' : '' }}>Básico</option>
                                <option value="intermedio" {{ old('difficulty_level', $paper->difficulty_level) === 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                                <option value="avanzado"   {{ old('difficulty_level', $paper->difficulty_level) === 'avanzado'   ? 'selected' : '' }}>Avanzado</option>
                            </select>
                        </div>
                        <div class="mb-2 text-muted small">
                            <i class="fas fa-eye me-1"></i>{{ number_format($paper->views) }} vistas &nbsp;
                            <i class="fas fa-clock me-1"></i>{{ $paper->reading_time ?? '—' }} min
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i>Guardar cambios
                        </button>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Información</div>
                    <div class="card-body small text-muted">
                        <div><strong>Slug:</strong> {{ $paper->slug ?? '—' }}</div>
                        <div><strong>Creado:</strong> {{ $paper->created_at?->format('d/m/Y H:i') }}</div>
                        <div><strong>Actualizado:</strong> {{ $paper->updated_at?->format('d/m/Y H:i') }}</div>
                        @if($paper->arxiv_url)
                        <div class="mt-2"><a href="{{ $paper->arxiv_url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100"><i class="fas fa-external-link-alt me-1"></i>Paper en arXiv</a></div>
                        @endif
                    </div>
                </div>

                {{-- Delete --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <form action="{{ route('admin.papers.destroy', $paper) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar permanentemente este paper?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-trash me-1"></i>Eliminar paper
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
