@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Investigaciones: {{ $type }}</h1>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Investigaciones</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $type }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Lista de investigaciones -->
        @forelse($research as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" 
                             class="card-img-top" 
                             alt="{{ $item->title }}"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="fas fa-flask text-secondary fa-3x"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('research.show', $item->id) }}" class="text-decoration-none">
                                {{ $item->title }}
                            </a>
                        </h5>
                        
                        @if($item->excerpt)
                            <p class="card-text">{{ Str::limit($item->excerpt, 120) }}</p>
                        @endif
                        
                        <div class="d-flex align-items-center small text-muted mt-2">
                            <div class="me-3">
                                <i class="far fa-calendar-alt me-1"></i> {{ $item->created_at->format('d M, Y') }}
                            </div>
                            <div>
                                <i class="far fa-eye me-1"></i> {{ number_format($item->views) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('research.show', $item->id) }}" class="btn btn-primary btn-sm">
                            Leer más
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No hay investigaciones disponibles para el tipo "{{ $type }}".
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4">
        {{ $research->links() }}
    </div>
</div>
@endsection