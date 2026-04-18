@extends('admin.layouts.app')

@section('title', 'Editar Digest')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0"><i class="fas fa-edit me-2"></i>Editar Digest — Estado del Arte</h1>
        <div class="d-flex gap-2">
            @if($digest->slug)
            <a href="{{ route('estado-arte.show', $digest->slug) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i>Ver en portal
            </a>
            @endif
            <a href="{{ route('admin.estado-arte.index') }}" class="btn btn-secondary btn-sm">
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

    <form action="{{ route('admin.estado-arte.update', $digest) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-3">
            {{-- Main column --}}
            <div class="col-lg-8">

                {{-- Meta (readonly) --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Datos del digest (solo lectura)</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small">Subcampo</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $digest->subfield }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Semana inicio</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $digest->week_start?->format('d/m/Y') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Semana fin</label>
                                <input type="text" class="form-control form-control-sm" value="{{ $digest->week_end?->format('d/m/Y') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Title --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Título</div>
                    <div class="card-body">
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $digest->title) }}" required>
                        @error('title')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Labels --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Etiquetas editoriales</div>
                    <div class="card-body row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Etiqueta subcampo</label>
                            <input type="text" name="subfield_label" class="form-control form-control-sm"
                                   value="{{ old('subfield_label', $digest->subfield_label) }}"
                                   placeholder="ej: IA Generativa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Etiqueta período</label>
                            <input type="text" name="period_label" class="form-control form-control-sm"
                                   value="{{ old('period_label', $digest->period_label) }}"
                                   placeholder="ej: Semana del 14 al 20 de abril">
                        </div>
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Resumen / Excerpt</div>
                    <div class="card-body">
                        <textarea name="excerpt" rows="3" class="form-control form-control-sm">{{ old('excerpt', $digest->excerpt) }}</textarea>
                    </div>
                </div>

                {{-- Key developments --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Desarrollos clave <span class="text-muted fw-normal">(uno por línea)</span></div>
                    <div class="card-body">
                        <textarea name="key_developments_raw" rows="6" class="form-control form-control-sm">{{ old('key_developments_raw', implode("\n", $digest->key_developments ?? [])) }}</textarea>
                    </div>
                </div>

                {{-- Content --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Contenido (HTML)</div>
                    <div class="card-body">
                        <textarea name="content" rows="20" class="form-control form-control-sm" style="font-family:monospace;font-size:.8rem;">{{ old('content', $digest->content) }}</textarea>
                    </div>
                </div>

                {{-- Source news --}}
                @if(!empty($digest->source_news_ids))
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Noticias fuente</div>
                    <div class="card-body">
                        @php $sourceNews = $digest->sourceNews(); @endphp
                        @if($sourceNews->isNotEmpty())
                        <ul class="small mb-0">
                            @foreach($sourceNews as $news)
                            <li><a href="{{ route('news.show', $news->slug) }}" target="_blank">{{ $news->title }}</a></li>
                            @endforeach
                        </ul>
                        @else
                        <span class="text-muted small">{{ count($digest->source_news_ids) }} noticias referenciadas (IDs: {{ implode(', ', $digest->source_news_ids) }})</span>
                        @endif
                    </div>
                </div>
                @endif

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
                                <option value="draft"     {{ old('status', $digest->status) === 'draft'     ? 'selected' : '' }}>Borrador</option>
                                <option value="published" {{ old('status', $digest->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="hidden" name="featured" value="0">
                            <input type="checkbox" name="featured" value="1" class="form-check-input" id="featured"
                                   {{ old('featured', $digest->featured) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="featured">Destacado</label>
                        </div>
                        <div class="mb-2 text-muted small">
                            <i class="fas fa-eye me-1"></i>{{ number_format($digest->views) }} vistas &nbsp;
                            <i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? '—' }} min
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
                        <div><strong>Slug:</strong> {{ $digest->slug ?? '—' }}</div>
                        <div><strong>Creado:</strong> {{ $digest->created_at?->format('d/m/Y H:i') }}</div>
                        <div><strong>Actualizado:</strong> {{ $digest->updated_at?->format('d/m/Y H:i') }}</div>
                        <div><strong>Publicado:</strong> {{ $digest->published_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </div>
                </div>

                {{-- Regenerate --}}
                <div class="card mb-3">
                    <div class="card-header py-2 small fw-semibold">Regenerar con IA</div>
                    <div class="card-body">
                        <form action="{{ route('admin.estado-arte.run-generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subfield" value="{{ $digest->subfield }}">
                            <button type="submit" class="btn btn-outline-success btn-sm w-100"
                                    onclick="return confirm('Esto creará/sobreescribirá el digest de esta semana para {{ $digest->subfield_label }}. ¿Continuar?')">
                                <i class="fas fa-magic me-1"></i>Regenerar {{ $digest->subfield_label }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Delete --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <form action="{{ route('admin.estado-arte.destroy', $digest) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar permanentemente este digest?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-trash me-1"></i>Eliminar digest
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
