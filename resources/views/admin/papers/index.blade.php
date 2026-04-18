@extends('admin.layouts.app')

@section('title', 'ConocIA Papers')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>ConocIA Papers</h1>
        <div class="d-flex gap-2">
            {{-- Run fetch form --}}
            <form action="{{ route('admin.papers.run-fetch') }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf
                <select name="max_results" class="form-select form-select-sm" style="width:80px;">
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                </select>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-sync-alt me-1"></i>Importar arXiv
                </button>
            </form>
            <a href="{{ route('papers.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
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
            <form action="{{ route('admin.papers.index') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar título, arxiv ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Borrador</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
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
                            <th style="width:40%">Título</th>
                            <th>Categoría</th>
                            <th>Dificultad</th>
                            <th>Estado</th>
                            <th>Vistas</th>
                            <th>Fecha arXiv</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($papers as $paper)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($paper->title, 70) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $paper->arxiv_id }}</div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $paper->arxiv_category }}</span></td>
                            <td>
                                @php $d = $paper->difficulty_level; @endphp
                                @if($d)
                                <span class="badge {{ $d === 'básico' ? 'bg-success' : ($d === 'intermedio' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($d) }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $paper->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $paper->status === 'published' ? 'Publicado' : 'Borrador' }}
                                </span>
                                @if($paper->featured)
                                <span class="badge bg-warning text-dark ms-1">Destacado</span>
                                @endif
                            </td>
                            <td>{{ number_format($paper->views) }}</td>
                            <td>{{ $paper->arxiv_published_date?->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.papers.edit', $paper) }}"
                                       class="btn btn-xs btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.papers.toggle-status', $paper) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs {{ $paper->status === 'published' ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $paper->status === 'published' ? 'Despublicar' : 'Publicar' }}">
                                            <i class="fas fa-{{ $paper->status === 'published' ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    @if($paper->slug)
                                    <a href="{{ route('papers.show', $paper->slug) }}" target="_blank"
                                       class="btn btn-xs btn-outline-secondary" title="Ver en portal">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('admin.papers.destroy', $paper) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este paper?')">
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
                                No hay papers. Usa "Importar arXiv" para traer nuevos.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($papers->hasPages())
        <div class="card-footer">{{ $papers->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-xs { padding: 2px 6px; font-size: .72rem; }
</style>
@endpush
