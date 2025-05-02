@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Navegación de migas de pan -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('podcasts.index') }}">Podcasts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($podcast->title, 40) }}</li>
                </ol>
            </nav>
            
            <!-- Tarjeta principal del podcast -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Encabezado con botón de reproducción -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="podcast-play-button me-3" 
                             data-audio-url="{{ $podcast->audio_url }}"
                             data-podcast-id="{{ $podcast->id }}">
                            <div class="btn btn-primary rounded-circle p-3 play-btn">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="btn btn-primary rounded-circle p-3 pause-btn d-none">
                                <i class="fas fa-pause"></i>
                            </div>
                        </div>
                        
                        <div>
                            <h1 class="h3 mb-2">{{ $podcast->title }}</h1>
                            <div class="d-flex flex-wrap text-muted small mb-2">
                                <span class="me-3">
                                    <i class="fas fa-calendar-alt me-1"></i> 
                                    {{ $podcast->published_at->locale('es')->isoFormat('LL') }}
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-clock me-1"></i> 
                                    {{ $podcast->formatted_duration ?? '5-10 min' }}
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-headphones me-1"></i> 
                                    <span id="play-count">{{ number_format($podcast->play_count) }}</span> reproducciones
                                </span>
                                <span>
                                    <i class="fas fa-newspaper me-1"></i> 
                                    {{ $podcast->news->count() ?? 8 }} noticias
                                </span>
                            </div>
                            
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('podcasts.feed') }}" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                    <i class="fas fa-rss me-1"></i> Suscribirse
                                </a>
                                <button class="btn btn-sm btn-outline-secondary me-2" onclick="shareContent()">
                                    <i class="fas fa-share-alt me-1"></i> Compartir
                                </button>
                                <a href="{{ $podcast->audio_url }}" class="btn btn-sm btn-outline-success" download>
                                    <i class="fas fa-download me-1"></i> Descargar
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reproductor de audio -->
                    <div class="mb-4">
                        <audio id="podcast-player" class="w-100" controls>
                            <source src="{{ $podcast->audio_url }}" type="audio/mpeg">
                            Tu navegador no soporta la reproducción de audio.
                        </audio>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="mb-4">
                        <h5 class="mb-3">Resumen del episodio</h5>
                        <div class="podcast-description">
                            {!! $podcast->description ?? 'Este episodio presenta un resumen de las 8 noticias más importantes del día.' !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lista de noticias incluidas en el podcast -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-newspaper text-primary me-2"></i> Noticias incluidas
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($podcast->news as $index => $news)
                    <div class="list-group-item p-3 border-bottom">
                        <div class="d-flex">
                            <div class="me-3 text-primary fw-bold fs-4" style="min-width: 30px;">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('news.show', $news->slug ?? $news->id) }}" class="text-decoration-none text-dark">
                                        {{ $news->title }}
                                    </a>
                                </h5>
                                <p class="mb-2 text-muted">{{ Str::limit($news->excerpt, 150) }}</p>
                                <div class="d-flex flex-wrap align-items-center text-muted small">
                                    @if(isset($news->category))
                                    <span class="me-3">
                                        <i class="fas {{ $getCategoryIcon($news->category) ?? 'fa-tag' }} me-1"></i> 
                                        {{ $news->category->name }}
                                    </span>
                                    @endif
                                    <span class="me-3">
                                        <i class="far fa-clock me-1"></i> 
                                        {{ $news->created_at->locale('es')->diffForHumans() }}
                                    </span>
                                    <span>
                                        <i class="far fa-eye me-1"></i> 
                                        {{ number_format($news->views ?? 0) }} lecturas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item p-4 text-center">
                        <p class="text-muted mb-0">
                            No hay información detallada sobre las noticias incluidas en este podcast.
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Transcripción (si está disponible) -->
            @if(isset($podcast->transcript) && !empty($podcast->transcript))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-file-alt text-primary me-2"></i> Transcripción
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="transcript-content">
                        {!! nl2br($podcast->transcript) !!}
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar derecho -->
        <div class="col-lg-4">
            <!-- Podcasts recientes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history text-primary me-2"></i> Podcasts recientes
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach(App\Models\Podcast::where('id', '!=', $podcast->id)->orderBy('published_at', 'desc')->take(5)->get() as $recentPodcast)
                    <a href="{{ route('podcasts.show', $recentPodcast) }}" class="list-group-item list-group-item-action p-3">
                        <div class="d-flex align-items-center">
                            <div class="btn btn-light rounded-circle me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-podcast text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 text-truncate" style="max-width: 240px;">{{ $recentPodcast->title }}</h6>
                                <div class="small text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> 
                                    {{ $recentPodcast->published_at->locale('es')->isoFormat('LL') }}
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="{{ route('podcasts.index') }}" class="btn btn-primary btn-sm">
                        Ver todos los podcasts
                    </a>
                </div>
            </div>
            
            <!-- Noticias relacionadas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-newspaper text-primary me-2"></i> Noticias relacionadas
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        $relatedNews = collect();
                        
                        // Intenta obtener noticias relacionadas
                        if(isset($podcast->news) && $podcast->news->isNotEmpty()) {
                            $categories = $podcast->news->pluck('category_id')->filter()->unique()->toArray();
                            
                            if(!empty($categories)) {
                                $relatedNews = App\Models\News::whereIn('category_id', $categories)
                                    ->where('created_at', '>=', now()->subDays(7))
                                    ->whereNotIn('id', $podcast->news->pluck('id')->toArray())
                                    ->orderBy('views', 'desc')
                                    ->take(5)
                                    ->get();
                            }
                        }
                        
                        // Si no hay relacionadas, obtener las más populares
                        if($relatedNews->isEmpty()) {
                            $relatedNews = App\Models\News::where('created_at', '>=', now()->subDays(7))
                                ->orderBy('views', 'desc')
                                ->take(5)
                                ->get();
                        }
                    @endphp
                    
                    @forelse($relatedNews as $related)
                    <a href="{{ route('news.show', $related->slug ?? $related->id) }}" class="list-group-item list-group-item-action p-3">
                        <h6 class="mb-1">{{ Str::limit($related->title, 70) }}</h6>
                        <div class="d-flex justify-content-between align-items-center small">
                            <div class="text-muted">
                                <i class="far fa-clock me-1"></i> {{ $related->created_at->locale('es')->diffForHumans() }}
                            </div>
                            @if(isset($related->category))
                            <span class="badge" style="{{ $getCategoryStyle($related->category) ?? 'background-color: #6c757d;' }}">
                                {{ $related->category->name }}
                            </span>
                            @endif
                        </div>
                    </a>
                    @empty
                    <div class="list-group-item p-4 text-center">
                        <p class="text-muted mb-0">No hay noticias relacionadas disponibles.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Compartir podcast -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-share-alt text-primary me-2"></i> Compartir podcast
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Comparte este podcast en tus redes sociales.</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('podcasts.show', $podcast)) }}" 
                           class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('podcasts.show', $podcast)) }}&text={{ urlencode($podcast->title) }}" 
                           class="btn btn-outline-info" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($podcast->title . ' - ' . route('podcasts.show', $podcast)) }}" 
                           class="btn btn-outline-success" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(route('podcasts.show', $podcast)) }}&text={{ urlencode($podcast->title) }}" 
                           class="btn btn-outline-info" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ route('podcasts.show', $podcast) }}')">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                    
                    <div id="clipboard-alert" class="alert alert-success d-none">
                        <i class="fas fa-check-circle me-1"></i> Enlace copiado al portapapeles
                    </div>
                    
                    <div class="input-group">
                        <input type="text" id="podcast-url" class="form-control form-control-sm" 
                               value="{{ route('podcasts.show', $podcast) }}" readonly>
                        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('{{ route('podcasts.show', $podcast) }}')">
                            Copiar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Suscribirse al feed -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-podcast text-primary fa-3x mb-3"></i>
                    <h5 class="card-title">No te pierdas ningún episodio</h5>
                    <p class="card-text">Suscríbete para recibir automáticamente todos nuestros podcasts diarios.</p>
                    <a href="{{ route('podcasts.feed') }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-rss me-1"></i> Suscribirse al feed
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .podcast-play-button {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .podcast-play-button:hover {
        transform: scale(1.05);
    }
    
    .transcript-content {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
        line-height: 1.6;
    }
    
    .podcast-description {
        line-height: 1.6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias
    const player = document.getElementById('podcast-player');
    const playButton = document.querySelector('.podcast-play-button');
    const playBtn = playButton.querySelector('.play-btn');
    const pauseBtn = playButton.querySelector('.pause-btn');
    
    // Manejar clic en botón de reproducción
    playButton.addEventListener('click', function() {
        if (player.paused) {
            player.play();
            playBtn.classList.add('d-none');
            pauseBtn.classList.remove('d-none');
        } else {
            player.pause();
            pauseBtn.classList.add('d-none');
            playBtn.classList.remove('d-none');
        }
    });
    
    // Sincronizar botón con estado del reproductor
    player.addEventListener('play', function() {
        playBtn.classList.add('d-none');
        pauseBtn.classList.remove('d-none');
        registerPlay();
    });
    
    player.addEventListener('pause', function() {
        pauseBtn.classList.add('d-none');
        playBtn.classList.remove('d-none');
    });
    
    player.addEventListener('ended', function() {
        pauseBtn.classList.add('d-none');
        playBtn.classList.remove('d-none');
    });
    
    // Registrar reproducción cuando el usuario reproduce el podcast
    function registerPlay() {
        const podcastId = playButton.dataset.podcastId;
        
        // Evitar múltiples conteos en la misma sesión
        if (sessionStorage.getItem(`podcast_played_${podcastId}`)) {
            return;
        }
        
        fetch(`/podcasts/${podcastId}/play`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar contador en la interfaz
                const countElement = document.getElementById('play-count');
                if (countElement) {
                    countElement.innerText = new Intl.NumberFormat().format(data.play_count);
                }
                
                // Marcar como reproducido en esta sesión
                sessionStorage.setItem(`podcast_played_${podcastId}`, 'true');
            }
        })
        .catch(error => console.error('Error al registrar reproducción:', error));
    }
});

// Función para compartir contenido
function shareContent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $podcast->title }}',
            text: 'Escucha este podcast: {{ $podcast->title }}',
            url: '{{ route("podcasts.show", $podcast) }}',
        })
        .catch(error => console.error('Error al compartir:', error));
    } else {
        // Fallback si Web Share API no está disponible
        alert('Utiliza los botones de abajo para compartir este podcast');
    }
}

// Función para copiar al portapapeles
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    
    // Mostrar alerta de éxito
    const alert = document.getElementById('clipboard-alert');
    alert.classList.remove('d-none');
    
    setTimeout(() => {
        alert.classList.add('d-none');
    }, 2000);
}
</script>
@endpush