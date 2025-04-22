@extends('layouts.app')

@section('title', 'Videos Destacados - Portal de Noticias')

@section('content')
<!-- Banner de título para sección de Videos Destacados -->
<div class="py-3 bg-dark text-white mb-0 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute start-0 top-50 translate-middle-y opacity-10">
            <i class="fas fa-film fa-3x"></i>
        </div>
        <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
            <i class="fas fa-star fa-3x"></i>
        </div>
    </div>
    
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="mb-0 text-uppercase fw-bold fs-4">
                    <span class="d-inline-block border-bottom border-2 pb-1">Videos Destacados</span>
                </h1>
                <p class="mb-0 mt-1 text-white-50 fs-6">
                    Los mejores videos seleccionados por nuestro equipo editorial
                </p>
            </div>
        </div>
    </div>
</div>

<section class="py-4 bg-light">
    <div class="container">
        <!-- Filtros y contador -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="h4 mb-0">
                    <i class="fas fa-star me-2 text-warning"></i> Videos Destacados
                    <span class="badge bg-primary rounded-pill ms-2">{{ $videos->total() }} videos</span>
                </h2>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end align-items-center">
                    <!-- Selector de orden -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="orderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sort me-1"></i> Ordenar por
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="orderDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['order' => 'newest']) }}">Más recientes</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['order' => 'oldest']) }}">Más antiguos</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['order' => 'popular']) }}">Más populares</a></li>
                        </ul>
                    </div>
                    
                    <!-- Botón para volver a la galería de videos -->
                    <a href="{{ route('videos.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-th-large me-1"></i> Galería completa
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Lista de videos -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($videos as $video)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300">
                    <div class="position-relative video-card-thumb">
                        <img src="{{ $video->thumbnail_url }}" class="card-img-top" alt="{{ $video->title }}">
                        <a href="{{ route('videos.show', $video->id) }}" class="stretched-link video-play-overlay">
                            <div class="video-play-button-sm">
                                <i class="fas fa-play"></i>
                            </div>
                        </a>
                        <div class="video-duration">
                            <i class="fas fa-clock me-1"></i> {{ formatDuration($video->duration_seconds) }}
                        </div>
                        <div class="video-platform position-absolute top-0 start-0 m-2">
                            <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }} bg-opacity-75">
                                <i class="fab fa-{{ $video->platform->code }}"></i>
                            </span>
                        </div>
                        <!-- Indicador de destacado -->
                        <div class="video-featured-badge">
                            <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Destacado</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title h5 fw-bold line-clamp-2">{{ $video->title }}</h3>
                        <p class="card-text text-secondary small line-clamp-2">{{ $video->description }}</p>
                        
                        <!-- Categorías del video -->
                        @if($video->categories->count() > 0)
                        <div class="mb-2">
                            @foreach($video->categories->take(3) as $category)
                            <a href="{{ route('videos.category', $category->id) }}" class="badge bg-light text-dark text-decoration-none me-1">
                                <i class="fas fa-folder me-1"></i> {{ $category->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span><i class="far fa-calendar-alt me-1"></i> {{ $video->published_at->locale('es')->diffForHumans() }}</span>
                            <span><i class="far fa-eye me-1"></i> {{ number_format($video->view_count) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i> No hay videos destacados disponibles en este momento.
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('videos.index') }}" class="btn btn-primary">
                        <i class="fas fa-th-large me-1"></i> Ver todos los videos
                    </a>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $videos->links() }}
        </div>
    </div>
</section>

<!-- Sección para categorías populares -->
<section class="py-3 bg-white border-top">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="h5 mb-3">Explorar por categorías:</h3>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $categories = \App\Models\VideoCategory::withCount('videos')
                            ->orderBy('videos_count', 'desc')
                            ->take(8)
                            ->get();
                    @endphp
                    
                    @foreach($categories as $category)
                    <a href="{{ route('videos.category', $category->id) }}" class="btn btn-outline-secondary mb-2 rounded-pill">
                        <i class="fas fa-folder me-1"></i> {{ $category->name }}
                        <span class="badge bg-secondary rounded-pill ms-1">{{ $category->videos_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Estilos para tarjetas de video */
.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-scale:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.transition-300 {
    transition: all 0.3s ease;
}

/* Limitadores de líneas para textos */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Estilos para miniaturas de videos */
.video-card-thumb {
    height: 200px;
    overflow: hidden;
}

.video-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.hover-scale:hover .video-card-thumb img {
    transform: scale(1.05);
}

/* Estilos para botón de reproducción */
.video-play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-play-overlay:hover {
    opacity: 1;
}

.video-play-button-sm {
    width: 50px;
    height: 50px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s ease;
}

.video-play-overlay:hover .video-play-button-sm {
    transform: scale(1.1);
    background: var(--bs-primary);
}

/* Indicador de duración */
.video-duration {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

/* Indicador de destacado */
.video-featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>
@endpush

@push('scripts')
<script>
function formatDuration(seconds) {
    if (!seconds) return '0:00';
    
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    const remainingSeconds = seconds % 60;
    
    if (hours > 0) {
        return `${hours}:${remainingMinutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}
</script>
@endpush