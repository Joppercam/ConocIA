@extends('admin.layouts.app')

@section('title', 'Estado del Arte')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="fas fa-brain me-2 text-primary"></i>Estado del Arte</h1>
        <div class="d-flex gap-2">
            {{-- Run generate form --}}
            <form action="{{ route('admin.estado-arte.run-generate') }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf
                <select name="subfield" class="form-select form-select-sm" style="width:160px;">
                    <option value="">Todos los subcampos</option>
                    @foreach($subfields as $sf)
                    <option value="{{ $sf->subfield }}">{{ $sf->subfield_label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-magic me-1"></i>Generar digest
                </button>
            </form>
            <a href="{{ route('estado-arte.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i>Ver portal
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form action="{{ route('admin.estado-arte.index') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar título, subcampo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Borrador</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="subfield" class="form-select form-select-sm">
                        <option value="">Todos los subcampos</option>
                        @foreach($subfields as $sf)
                        <option value="{{ $sf->subfield }}" {{ request('subfield') === $sf->subfield ? 'selected' : '' }}>
                            {{ $sf->subfield_label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35%">Título</th>
                            <th>Subcampo</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Vistas</th>
                            <th>Semana</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($digests as $digest)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($digest->title, 65) }}</div>
                                @if($digest->excerpt)
                                <div class="text-muted" style="font-size:.74rem;">{{ Str::limit($digest->excerpt, 80) }}</div>
                                @endif
                            </td>
                            <td><span class="badge bg-primary" style="font-size:.7rem;">{{ $digest->subfield_label }}</span></td>
                            <td style="font-size:.8rem;">{{ $digest->period_label }}</td>
                            <td>
                                <span class="badge {{ $digest->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $digest->status === 'published' ? 'Publicado' : 'Borrador' }}
                                </span>
                                @if($digest->featured)
                                <span class="badge bg-warning text-dark ms-1">Dest.</span>
                                @endif
                            </td>
                            <td>{{ number_format($digest->views) }}</td>
                            <td style="font-size:.78rem;white-space:nowrap;">
                                {{ $digest->week_start?->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.estado-arte.edit', $digest) }}"
                                       class="btn btn-xs btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.estado-arte.toggle-status', $digest) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs {{ $digest->status === 'published' ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $digest->status === 'published' ? 'Despublicar' : 'Publicar' }}">
                                            <i class="fas fa-{{ $digest->status === 'published' ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    @if($digest->slug)
                                    <a href="{{ route('estado-arte.show', $digest->slug) }}" target="_blank"
                                       class="btn btn-xs btn-outline-secondary" title="Ver en portal">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('admin.estado-arte.destroy', $digest) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este digest?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay digests. Usa "Generar digest" para crear uno con Gemini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($digests->hasPages())
        <div class="card-footer">{{ $digests->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>.btn-xs { padding: 2px 6px; font-size: .72rem; }</style>
@endpush
