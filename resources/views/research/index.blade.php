@extends('layouts.app')

@section('title', 'Investigación y Análisis - ConocIA')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fs-2 mb-3">Investigación y Análisis</h1>
            <p class="text-muted">Descubre los últimos avances, estudios y análisis en el campo de la inteligencia artificial y tecnología.</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-lg-8">
            <div class="row g-4">
                @if($researches->count() > 0)
                    @foreach($researches as $research)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}">
                                    <img src="{{ $getImageUrl($research->image, 'research', 'medium') }}" class="card-img-top" alt="{{ $research->title }}" onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/research-default-medium.jpg') }}';">
                                </a>
                                @if(isset($research->category))
                                <div class="position-absolute bottom-0 end-0 m-2">
                                    <span class="badge" style="{{ $getCategoryStyle($research->category) }} font-size: 0.7rem;">
                                        <i class="fas {{ $getCategoryIcon($research->category) }} me-1"></i>
                                        {{ $research->category->name }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none text-dark">
                                        {{ $research->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted small mb-2">{{ $research->created_at->format('d M, Y') }} • {{ $research->views }} lecturas</p>
                                <p class="card-text">{{ Str::limit($research->excerpt, 120) }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($research->author) }}&background=random" class="rounded-circle me-2" width="30" height="30" alt="{{ $research->author }}">
                                        <span class="small text-muted">{{ $research->author }}</span>
                                    </div>
                                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="btn btn-sm btn-outline-primary">Leer más</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <p class="mb-0">No se encontraron artículos de investigación.</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $researches->links() }}
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Filtro de Categorías -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fs-6">Categorías</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($categories as $category)
                            <a href="{{ route('research.category', $category->slug) }}" class="badge text-white text-decoration-none p-2 mb-2" style="{{ $getCategoryStyle($category) }}">
                                <i class="fas {{ $getCategoryIcon($category) }} me-1"></i>
                                {{ $category->name }}
                                @if(isset($category->research_count))
                                <span class="badge bg-light text-dark ms-1">{{ $category->research_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Investigaciones Destacadas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fs-6">Investigaciones Destacadas</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($featuredResearch as $featured)
                        <li class="list-group-item px-3 py-2 border-0">
                            <div class="d-flex">
                                <div class="me-2">
                                    <img src="{{ $getImageUrl($featured->image, 'research', 'small') }}" class="rounded" width="60" height="60" alt="{{ $featured->title }}" style="object-fit: cover;">
                                </div>
                                <div>
                                    <h6 class="mb-1 fs-6">
                                        <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}" class="text-decoration-none text-dark">{{ Str::limit($featured->title, 60) }}</a>
                                    </h6>
                                    <div class="d-flex align-items-center mt-1">
                                        @if(isset($featured->category))
                                        <span class="badge me-2" style="{{ $getCategoryStyle($featured->category) }}">
                                            {{ $featured->category->name }}
                                        </span>
                                        @endif
                                        <small class="text-muted">{{ $featured->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3 fs-6">Suscríbete al newsletter</h5>
                    <p class="text-muted small">Recibe las últimas investigaciones y análisis directamente en tu correo.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="research">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Tu correo electrónico" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Suscribirse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection