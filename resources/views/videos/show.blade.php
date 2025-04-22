@extends('layouts.app')

@section('title', $video->title . ' - Portal de Noticias')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Reproductor de video -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded-top"></iframe>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            @foreach($video->categories as $category)
                            <a href="{{ route('videos.category', $category->id) }}" class="badge bg-light text-dark text-decoration-none me-2">
                                <i class="fas fa-folder me-1"></i> {{ $category->name }}
                            </a>
                            @endforeach
                        </div>
                        <div>
                            <span class="badge bg-{{ $video->platform->code === 'youtube' ? 'danger' : ($video->platform->code === 'vimeo' ? 'info' : 'primary') }}">
                                <i class="fab fa-{{ $video->platform->code }} me-1"></i> {{ $video->platform->name }}
                            </span>
                        </div>
                    </div>
                    
                    <h1 class="video-title h3 fw-bold mb-3">{{ $video->title }}</h1>
                    
                    <div class="video-meta d-flex flex-wrap mb-3">
                        <div class="me-4 text-muted">
                            <i class="far fa-calendar-alt me-1"></i> {{ $video->published_at->format('d M Y') }}
                        </div>
                        <div class="me-4 text-muted">
                            <i class="far fa-clock me-1"></i> {{ formatDuration($video->duration_seconds) }}
                        </div>
                        <div class="text-muted">
                            <i class="far fa-eye me-1"></i> {{ number_format($video->view_count) }} reproducciones
                        </div>
                    </div>
                    
                    <div class="video-description">
                        <p class="mb-0">{{ $video->description }}</p>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="video-tags">
                            @foreach($video->tags as $tag)
                            <a href="{{ route('videos.tag', $tag->id) }}" class="btn btn-sm btn-outline-secondary mb-1 me-1 rounded-pill">
                                <i class="fas fa-tag me-1"></i> {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                        <div class="video-share">
                            <button type="button" class="btn btn-sm btn-primary rounded-circle me-1" onclick="copyVideoUrl()" title="Copiar enlace">
                                <i class="fas fa-link"></i>
                            </button>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-primary rounded-circle me-1" title="Compartir en Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($video->title) }}" target="_blank" class="btn btn-sm btn-info rounded-circle me-1" title="Compartir en Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($video->title . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-success rounded-circle" title="Compartir en WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de comentarios (si está habilitada) -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">Comentarios</h3>
                </div>
                <div class="card-body">
                    <!-- Sistema de comentarios aquí -->
                    @if(Auth::check())
                    <form action="{{ route('videos.comment', $video->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" placeholder="Escribe tu comentario..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="far fa-paper-plane me-1"></i> Comentar
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Debes <a href="{{ route('login') }}">iniciar sesión</a> para comentar.
                    </div>
                    @endif
                    
                    <div class="comments-list">
                        <!-- Si tienes un sistema de comentarios, aquí irían los comentarios del video -->
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Videos relacionados -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">Videos relacionados</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($relatedVideos as $relatedVideo)
                        <a href="{{ route('videos.show', $relatedVideo->id) }}" class="list-group-item list-group-item-action py-3">
                            <div class="row g-0">
                                <div class="col-4 position-relative">
                                    <img src="{{ $relatedVideo->thumbnail_url }}" alt="{{ $relatedVideo->title }}" class="img-fluid rounded">
                                    <div class="position-absolute bottom-0 end-0 bg-dark text-white px-1 py-0 m-1 rounded fs-9">
                                        <i class="fas fa-clock me-1"></i> {{ formatDuration($relatedVideo->duration_seconds) }}
                                    </div>
                                </div>
                                <div class="col-8 ps-3">
                                    <h4 class="h6 mb-1 line-clamp-2">{{ $relatedVideo->title }}</h4>
                                    <div class="d-flex flex-column text-muted small">
                                        <span><i class="fab fa-{{ $relatedVideo->platform->code }} me-1"></i> {{ $relatedVideo->platform->name }}</span>
                                        <span><i class="far fa-eye me-1"></i> {{ number_format($relatedVideo->view_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay videos relacionados disponibles.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="{{ route('videos.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver más videos <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
            
            <!-- Categorías de videos -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">Categorías</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($video->categories as $category)
                        <a href="{{ route('videos.category', $category->id) }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill">
                            <i class="fas fa-folder me-1"></i> {{ $category->name }}
                        </a>
                        @endforeach
                        
                        @php
                            // Obtener algunas categorías populares adicionales
                            $additionalCategories = \App\Models\VideoCategory::withCount('videos')
                                ->whereNotIn('id', $video->categories->pluck('id')->toArray())
                                ->orderBy('videos_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @foreach($additionalCategories as $category)
                        <a href="{{ route('videos.category', $category->id) }}" class="btn btn-sm btn-outline-secondary mb-2 rounded-pill">
                            <i class="fas fa-folder me-1"></i> {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Videos populares -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0">Más populares</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            // Obtener algunos videos populares
                            $popularVideos = \App\Models\Video::with('platform')
                                ->where('id', '!=', $video->id)
                                ->orderBy('view_count', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @forelse($popularVideos as $popularVideo)
                        <a href="{{ route('videos.show', $popularVideo->id) }}" class="list-group-item list-group-item-action py-3">
                            <div class="row g-0">
                                <div class="col-4 position-relative">
                                    <img src="{{ $popularVideo->thumbnail_url }}" alt="{{ $popularVideo->title }}" class="img-fluid rounded">
                                    <div class="position-absolute bottom-0 end-0 bg-dark text-white px-1 py-0 m-1 rounded fs-9">
                                        <i class="fas fa-clock me-1"></i> {{ formatDuration($popularVideo->duration_seconds) }}
                                    </div>
                                </div>
                                <div class="col-8 ps-3">
                                    <h4 class="h6 mb-1 line-clamp-2">{{ $popularVideo->title }}</h4>
                                    <div class="d-flex flex-column text-muted small">
                                        <span><i class="fab fa-{{ $popularVideo->platform->code }} me-1"></i> {{ $popularVideo->platform->name }}</span>
                                        <span><i class="far fa-eye me-1"></i> {{ number_format($popularVideo->view_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No hay videos populares disponibles.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="{{ route('videos.popular') }}" class="btn btn-sm btn-outline-primary">
                        Ver más populares <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Estilos específicos para la página de detalles del video */
.video-title {
    line-height: 1.3;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-description {
    white-space: pre-line;
}

.fs-9 {
    font-size: 0.75rem !important;
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

function copyVideoUrl() {
    // Crear un elemento de texto temporal
    const el = document.createElement('textarea');
    el.value = window.location.href;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    
    // Mostrar un mensaje de confirmación
    alert('Enlace copiado al portapapeles');
}
</script>
@endpush