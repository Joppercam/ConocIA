<!-- Sección Completa de Videos para la página principal -->
<div class="video-section-container py-4 bg-light border-top border-bottom">
    <div class="container">
        <!-- Banner de título para Videos - Estilo consistente con otras secciones -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="section-title position-relative d-inline-block mb-3">
                    <span class="bg-light px-3 position-relative z-index-1">Galería de Videos</span>
                    <span class="section-line position-absolute start-0 bottom-0 w-100"></span>
                </h2>
                <p class="text-muted">Contenido audiovisual sobre actualidad, análisis y tendencias</p>
            </div>
        </div>

        <!-- Video destacado principal -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7" id="main-video-player">
                @if($featuredVideos->count() > 0)
                @php $mainVideo = $featuredVideos->first(); @endphp
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <!-- Embed del video principal -->
                    <div class="ratio ratio-16x9 bg-black">
                        <iframe id="main-video-iframe"
                                src="{{ $mainVideo->embed_url }}?rel=0&modestbranding=1"
                                title="{{ $mainVideo->title }}"
                                allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                class="rounded-top"
                                loading="lazy">
                        </iframe>
                    </div>
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-{{ $mainVideo->platform->code === 'youtube' ? 'danger' : ($mainVideo->platform->code === 'vimeo' ? 'info' : 'primary') }}">
                                <i class="fab fa-{{ $mainVideo->platform->code }}"></i>
                            </span>
                            <h3 class="card-title h6 fw-bold mb-0" id="main-video-title">{{ $mainVideo->title }}</h3>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span>
                                <i class="far fa-calendar-alt me-1"></i> {{ $mainVideo->published_at->format('d M Y') }}
                                <span class="ms-3"><i class="far fa-eye me-1"></i> {{ number_format($mainVideo->view_count) }}</span>
                            </span>
                            <a href="{{ route('videos.show', $mainVideo->id) }}" class="btn btn-sm btn-outline-primary py-0">
                                Ver página <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach($featuredVideos->skip(1)->take(4) as $video)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-scale transition-300 video-thumb-card"
                             style="cursor:pointer;"
                             data-embed="{{ $video->embed_url }}?rel=0&modestbranding=1&autoplay=1"
                             data-title="{{ $video->title }}"
                             data-platform="{{ $video->platform->code }}">
                            <div class="position-relative video-card-thumb" style="height: 140px;">
                                <img src="{{ $video->thumbnail_url }}" class="card-img-top h-100 w-100" style="object-fit: cover;" alt="{{ $video->title }}">
                                <div class="video-play-overlay">
                                    <div class="video-play-button-sm">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <div class="video-duration small">
                                    {{ formatDuration($video->duration_seconds) }}
                                </div>
                                <div class="video-platform position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }} bg-opacity-75">
                                        <i class="fab fa-{{ $video->platform->code }}"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <h4 class="card-title h6 mb-1 line-clamp-2" style="font-size: 0.9rem;">
                                    {{ $video->title }}
                                </h4>
                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                    <span>{{ $video->published_at->locale('es')->diffForHumans() }}</span>
                                    <span><i class="far fa-eye me-1"></i> {{ formatNumber($video->view_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Categorías y botón Ver más -->
        <div class="row">
            <div class="col-md-9">
                <div class="categories-selector d-flex flex-wrap gap-2">
                    @php
                        $videoCategories = \App\Models\VideoCategory::withCount('videos')
                            ->orderBy('videos_count', 'desc')
                            ->take(6)
                            ->get();
                    @endphp
                    
                    @foreach($videoCategories as $category)
                    <a href="{{ route('videos.category', $category->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="{{ $category->icon ?? 'fas fa-folder' }} me-1"></i> {{ $category->name }}
                        <span class="badge bg-secondary rounded-pill ms-1">{{ $category->videos_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                <a href="{{ route('videos.index') }}" class="btn btn-primary">
                    Ver galería completa <i class="fas fa-external-link-alt ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para la sección de videos en home */
.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.section-line {
    height: 2px;
    background-color: var(--bs-primary);
    z-index: 0;
    top: 50%;
}

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
.featured-thumbnail {
    height: 350px;
    object-fit: cover;
}

/* Overlay de reproducción en miniaturas */
.video-play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.25);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-thumb-card:hover .video-play-overlay {
    opacity: 1;
}

/* Tarjeta activa */
.video-thumb-card.active-video {
    outline: 3px solid var(--bs-primary);
}

.video-play-button {
    width: 70px;
    height: 70px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    transition: all 0.3s ease;
}

.video-play-button-sm {
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
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
</style>

<script>
// Cambiar video en el player principal al hacer clic en una miniatura
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.video-thumb-card').forEach(function (card) {
        card.addEventListener('click', function () {
            const embed  = card.dataset.embed;
            const title  = card.dataset.title;

            // Actualizar iframe
            document.getElementById('main-video-iframe').src = embed;
            document.getElementById('main-video-title').textContent = title;

            // Scroll suave al player en móvil
            if (window.innerWidth < 992) {
                document.getElementById('main-video-player').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Marcar tarjeta activa
            document.querySelectorAll('.video-thumb-card').forEach(c => c.classList.remove('active-video'));
            card.classList.add('active-video');
        });
    });
});

// Helper functions
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
</script>