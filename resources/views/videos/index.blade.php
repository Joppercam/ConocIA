@extends('layouts.app')

@section('title', 'Galería de Videos - Portal de Noticias')

@section('content')
<!-- Banner de título para sección de Videos - Estilo consistente con otras secciones -->
<div class="py-3 bg-dark text-white mb-0 position-relative overflow-hidden">
    <!-- Elementos decorativos de fondo -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute start-0 top-50 translate-middle-y opacity-10">
            <i class="fas fa-film fa-3x"></i>
        </div>
        <div class="position-absolute end-0 top-50 translate-middle-y opacity-10">
            <i class="fas fa-play-circle fa-3x"></i>
        </div>
    </div>
    
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="mb-0 text-uppercase fw-bold fs-4">
                    <span class="d-inline-block border-bottom border-2 pb-1">Galería de Videos</span>
                </h1>
                <p class="mb-0 mt-1 text-white-50 fs-6">Contenido audiovisual sobre actualidad, análisis y tendencias</p>
            </div>
        </div>
    </div>
</div>

<!-- Sección principal de videos -->
<section class="py-4 bg-light">
    <div class="container">
        <!-- Videos destacados -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="featured-video-container">
                    @if($featuredVideos->count() > 0)
                    <!-- Video principal (el más destacado) -->
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300">
                        <div class="position-relative">
                            <img src="{{ $featuredVideos->first()->thumbnail_url }}" class="card-img-top featured-thumbnail" alt="{{ $featuredVideos->first()->title }}">
                            <a href="{{ route('videos.show', $featuredVideos->first()->id) }}" class="stretched-link video-play-overlay">
                                <div class="video-play-button">
                                    <i class="fas fa-play"></i>
                                </div>
                            </a>
                            <div class="video-duration">
                                <i class="fas fa-clock me-1"></i> {{ formatDuration($featuredVideos->first()->duration_seconds) }}
                            </div>
                            <div class="video-platform position-absolute top-0 start-0 m-3">
                                <span class="badge bg-{{ $featuredVideos->first()->platform->code === 'youtube' ? 'danger' : ($featuredVideos->first()->platform->code === 'vimeo' ? 'info' : 'primary') }}">
                                    <i class="fab fa-{{ $featuredVideos->first()->platform->code }}"></i> {{ $featuredVideos->first()->platform->name }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title h4 fw-bold">{{ $featuredVideos->first()->title }}</h2>
                            <p class="card-text text-secondary mb-3">{{ Str::limit($featuredVideos->first()->description, 150) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="video-meta">
                                    <span class="me-3 text-muted">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $featuredVideos->first()->published_at->format('d M Y') }}
                                    </span>
                                    <span class="text-muted">
                                        <i class="far fa-eye me-1"></i> {{ number_format($featuredVideos->first()->view_count) }} reproducciones
                                    </span>
                                </div>
                                <a href="{{ route('videos.show', $featuredVideos->first()->id) }}" class="btn btn-primary btn-sm">
                                    Ver ahora <i class="fas fa-play ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        No hay videos destacados disponibles en este momento.
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="mb-3">
                    <h3 class="border-start border-4 border-primary ps-3 mb-3 fw-bold fs-5">
                        <i class="fas fa-star me-2"></i> Destacados
                    </h3>
                </div>
                
                <!-- Lista de videos destacados secundarios -->
                <div class="featured-videos-list">
                    @forelse($featuredVideos->skip(1)->take(3) as $video)
                    <div class="card border-0 shadow-sm mb-3 hover-scale transition-300 rounded-3 overflow-hidden">
                        <div class="row g-0">
                            <div class="col-4">
                                <div class="position-relative h-100">
                                    <img src="{{ $video->thumbnail_url }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $video->title }}">
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }} bg-opacity-75">
                                            <i class="fab fa-{{ $video->platform->code }}"></i>
                                        </span>
                                    </div>
                                    <div class="video-duration small">
                                        {{ formatDuration($video->duration_seconds) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="card-body py-2 px-3">
                                    <h4 class="card-title h6 mb-1 fw-semibold line-clamp-2">
                                        <a href="{{ route('videos.show', $video->id) }}" class="text-decoration-none text-dark stretched-link">
                                            {{ $video->title }}
                                        </a>
                                    </h4>
                                    <div class="text-muted fs-9">
                                        <span><i class="far fa-calendar-alt me-1"></i> {{ $video->published_at->locale('es')->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-muted fs-9">
                                        <span><i class="far fa-eye me-1"></i> {{ number_format($video->view_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center p-4 bg-light rounded">
                        <p class="text-muted mb-0">No hay más videos destacados disponibles.</p>
                    </div>
                    @endforelse
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('videos.featured') }}" class="btn btn-sm btn-outline-primary">
                            Ver todos los destacados <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <!-- Pestañas para las diferentes categorías de videos -->
                <ul class="nav nav-tabs nav-fill mb-4" id="videosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="latest-tab" data-bs-toggle="tab" data-bs-target="#latest" type="button" role="tab" aria-controls="latest" aria-selected="true">
                            <i class="fas fa-clock me-2"></i> Más recientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="popular-tab" data-bs-toggle="tab" data-bs-target="#popular" type="button" role="tab" aria-controls="popular" aria-selected="false">
                            <i class="fas fa-fire me-2"></i> Más populares
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab" aria-controls="categories" aria-selected="false">
                            <i class="fas fa-th-large me-2"></i> Por categoría
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="videosTabContent">
                    <!-- Pestaña: Más recientes -->
                    <div class="tab-pane fade show active" id="latest" role="tabpanel" aria-labelledby="latest-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @forelse($latestVideos as $video)
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
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-title h5 fw-bold line-clamp-2">{{ $video->title }}</h3>
                                        <p class="card-text text-secondary small line-clamp-2">{{ $video->description }}</p>
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
                                    No hay videos recientes disponibles en este momento.
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Pestaña: Más populares -->
                    <div class="tab-pane fade" id="popular" role="tabpanel" aria-labelledby="popular-tab">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @forelse($popularVideos as $video)
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
                                        <!-- Indicador de popularidad -->
                                        <div class="video-popular-badge">
                                            <span class="badge bg-danger"><i class="fas fa-fire"></i> Popular</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-title h5 fw-bold line-clamp-2">{{ $video->title }}</h3>
                                        <p class="card-text text-secondary small line-clamp-2">{{ $video->description }}</p>
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
                                    No hay videos populares disponibles en este momento.
                                </div>
                            </div>
                            @endforelse
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            <a href="{{ route('videos.popular') }}" class="btn btn-outline-primary">
                                Ver más videos populares <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Pestaña: Por categoría -->
                    <div class="tab-pane fade" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                        <!-- Selector de categorías -->
                        <div class="row mb-4">
                            <div class="col-md-8 mx-auto">
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body py-3">
                                        <div class="categories-selector d-flex flex-wrap justify-content-center gap-2">
                                            @forelse($videoCategories as $category)
                                            <button type="button" class="btn btn-sm btn-outline-secondary video-category-btn" data-category="{{ $category->id }}">
                                                <i class="{{ $category->icon ?? 'fas fa-folder' }} me-1"></i> {{ $category->name }}
                                                <span class="badge bg-secondary rounded-pill ms-1">{{ $category->videos_count }}</span>
                                            </button>
                                            @empty
                                            <p class="text-muted mb-0">No hay categorías de videos disponibles.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contenedor para videos por categoría (se carga vía AJAX) -->
                        <div class="category-videos-container">
                            <div class="text-center py-5">
                                <p class="text-muted">Selecciona una categoría para ver sus videos</p>
                            </div>
                        </div>
                    </div>
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

.featured-thumbnail {
    height: 400px;
    object-fit: cover;
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

.video-play-button {
    width: 80px;
    height: 80px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    transition: all 0.3s ease;
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

.video-play-overlay:hover .video-play-button,
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

/* Indicador de popularidad */
.video-popular-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

/* Tamaños de fuente específicos */
.fs-9 {
    font-size: 0.75rem !important;
}

/* Pestañas personalizadas */
#videosTabs .nav-link {
    color: #555;
    border: none;
    padding: 0.75rem 1rem;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

#videosTabs .nav-link:hover {
    color: var(--bs-primary);
}

#videosTabs .nav-link.active {
    color: var(--bs-primary);
    background: none;
    border-bottom: 3px solid var(--bs-primary);
}

/* Botones de categoría */
.video-category-btn {
    border-radius: 50px;
    padding: 0.4rem 1rem;
    transition: all 0.3s ease;
}

.video-category-btn:hover,
.video-category-btn.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
// Formateo de duración de video
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

document.addEventListener('DOMContentLoaded', function() {
    // Manejo de categorías
    const categoryButtons = document.querySelectorAll('.video-category-btn');
    const categoryVideosContainer = document.querySelector('.category-videos-container');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover clase activa de todos los botones
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Agregar clase activa al botón seleccionado
            this.classList.add('active');
            
            // Obtener ID de categoría
            const categoryId = this.dataset.category;
            
            // Mostrar loader
            categoryVideosContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando videos...</p>
                </div>
            `;
            
            // Cargar videos por categoría
            fetch(`/api/videos/by-category/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.videos.length > 0) {
                        renderCategoryVideos(data.videos);
                    } else {
                        categoryVideosContainer.innerHTML = `
                            <div class="text-center py-5">
                                <p class="text-muted">No hay videos disponibles en esta categoría</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    categoryVideosContainer.innerHTML = `
                        <div class="text-center py-5">
                            <p class="text-danger">Error al cargar los videos</p>
                        </div>
                    `;
                });
        });
    });
    
    function renderCategoryVideos(videos) {
        let html = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
        
        videos.forEach(video => {
            html += `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300">
                        <div class="position-relative video-card-thumb">
                            <img src="${video.thumbnail_url}" class="card-img-top" alt="${video.title}">
                            <a href="/videos/${video.id}" class="stretched-link video-play-overlay">
                                <div class="video-play-button-sm">
                                    <i class="fas fa-play"></i>
                                </div>
                            </a>
                            <div class="video-duration">
                                <i class="fas fa-clock me-1"></i> ${formatDuration(video.duration_seconds)}
                            </div>
                            <div class="video-platform position-absolute top-0 start-0 m-2">
                                <span class="badge bg-${video.platform.code === 'youtube' ? 'danger' : (video.platform.code === 'vimeo' ? 'info' : 'primary')} bg-opacity-75">
                                    <i class="fab fa-${video.platform.code}"></i>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title h5 fw-bold line-clamp-2">${video.title}</h3>
                            <p class="card-text text-secondary small line-clamp-2">${video.description || ''}</p>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="far fa-calendar-alt me-1"></i> ${formatRelativeDate(video.published_at)}</span>
                                <span><i class="far fa-eye me-1"></i> ${formatNumber(video.view_count)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Agregar botón para ver más
        html += `
            <div class="d-flex justify-content-center mt-4">
                <a href="/videos/category/${videos[0].categories[0].id}" class="btn btn-outline-primary">
                    Ver más videos de ${videos[0].categories[0].name} <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </div>
        `;
        
        categoryVideosContainer.innerHTML = html;
    }
    
    // Función para formatear números de manera legible
    function formatNumber(num) {
        if (!num) return '0';
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }
    
    // Función para formatear fechas relativas
    function formatRelativeDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 1) {
            return 'Hoy';
        } else if (diffDays === 1) {
            return 'Ayer';
        } else if (diffDays < 7) {
            return `Hace ${diffDays} días`;
        } else if (diffDays < 30) {
            const weeks = Math.floor(diffDays / 7);
            return `Hace ${weeks} ${weeks === 1 ? 'semana' : 'semanas'}`;
        } else if (diffDays < 365) {
            const months = Math.floor(diffDays / 30);
            return `Hace ${months} ${months === 1 ? 'mes' : 'meses'}`;
        } else {
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    }
});