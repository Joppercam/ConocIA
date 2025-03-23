<!-- resources/views/columns/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">Columnas de Opinión</h1>
            <p class="text-muted">Análisis y perspectivas sobre IA y tecnología</p>
        </div>
        <div class="col-md-4 text-md-end">
            <!-- Filtros o acciones adicionales si es necesario -->
        </div>
    </div>

    <!-- Listado de columnas -->
    <div class="row g-4">
        @foreach($columns as $column)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset($column->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}" 
                             class="rounded-circle me-2" width="40" height="40" 
                             alt="{{ $column->author->name }}" 
                             onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                        <div>
                            <h6 class="mb-0">{{ $column->author->name }}</h6>
                            <span class="text-muted small">{{ $column->published_at->format('d M, Y') }}</span>
                        </div>
                    </div>
                    
                    <h5 class="card-title">
                        <a href="{{ route('columns.show', $column->slug) }}" class="text-decoration-none text-dark">
                            {{ $column->title }}
                        </a>
                    </h5>
                    
                    <p class="card-text text-muted">
                        {{ Str::limit($column->excerpt ?? strip_tags($column->content), 120) }}
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        @if($column->category)
                        <span class="badge bg-light text-dark">{{ $column->category->name }}</span>
                        @endif
                        <small class="text-muted">{{ $column->reading_time }} min de lectura</small>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('columns.show', $column->slug) }}" class="btn btn-sm btn-outline-primary">Leer columna</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $columns->links() }}
    </div>
</div>
@endsection