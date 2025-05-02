@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Título de sección -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-dark">
                    <i class="fas fa-podcast text-primary me-2"></i> Podcasts diarios
                </h1>
                <div>
                    <a href="{{ route('podcasts.feed') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fas fa-rss me-1"></i> Suscribirse al feed
                    </a>
                </div>
            </div>

            <!-- Explicación de los podcasts -->
            <div class="alert alert-light mb-4">
                <div class="d-flex">
                    <div class="me-3 fs-3 text-primary">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading">¿Qué son nuestros podcasts?</h5>
                        <p class="mb-0">Cada día, transformamos las 8 noticias más importantes en audio para que puedas escucharlas en cualquier momento. Perfecto para cuando estás en movimiento o prefieres consumir la información de manera auditiva.</p>
                    </div>
                </div>
            </div>

            <!-- Lista de podcasts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Episodios recientes</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($podcasts as $podcast)
                    <div class="list-group-item border-bottom p-3 podcast-item" data-podcast-id="{{ $podcast->id }}">
                        <div class="row align-items-center">
                            <!-- Botón de reproducción -->
                            <div class="col-auto">
                                <div class="podcast-play-button" 
                                     data-audio-url="{{ $podcast->audio_url }}"
                                     data-podcast-id="{{ $podcast->id }}">
                                    <div class="btn btn-primary rounded-circle p-3 play-btn">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="btn btn-primary rounded-circle p-3 pause-btn d-none">
                                        <i class="fas fa-pause"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Información del podcast -->
                            <div class="col">
                                <h5 class="mb-1 fw-bold">
                                    <a href="{{ route('podcasts.show', $podcast) }}" class="text-decoration-none text-dark">
                                        {{ $podcast->title }}
                                    </a>
                                </h5>
                                
                                <p class="mb-2 text-muted">{{ Str::limit($podcast->description, 120) }}</p>
                                
                                <div class="d-flex align-items-center text-muted small flex-wrap">
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
                                        {{ number_format($podcast->play_count) }} reproducciones
                                    </span>
                                    <span>
                                        <i class="fas fa-newspaper me-1"></i> 
                                        {{ $podcast->news_count ?? 8 }} noticias
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Enlace a detalles -->
                            <div class="col-auto">
                                <a href="{{ route('podcasts.show', $podcast) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-info-circle me-1"></i> Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item p-4 text-center">
                        <div class="text-muted mb-3">
                            <i class="fas fa-podcast fa-3x"></i>
                        </div>
                        <h5>No hay podcasts disponibles</h5>
                        <p class="mb-0">Vuelve pronto para escuchar nuestros resúmenes diarios de noticias.</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Paginación -->
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-center">
                        {!! $podcasts->links('pagination::bootstrap-4') !!}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar derecho -->
        <div class="col-lg-4">
            <!-- Reproductor fijo para podcast actual -->
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 80px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 fs-6">
                        <i class="fas fa-headphones me-2"></i> Reproductor
                    </h5>
                </div>
                <div class="card-body">
                    <div id="current-player-container" class="text-center p-3">
                        <div id="no-podcast-selected" class="text-muted py-4">
                            <i class="fas fa-podcast fa-2x mb-3"></i>
                            <p>Selecciona un podcast para comenzar a escuchar</p>
                        </div>
                        
                        <div id="current-podcast-info" class="d-none mb-3">
                            <h5 id="current-title" class="fw-bold"></h5>
                            <p id="current-date" class="text-muted small mb-0"></p>
                        </div>
                        
                        <audio id="podcast-player" class="w-100 mt-3 d-none" controls>
                            Tu navegador no soporta la reproducción de audio.
                        </audio>
                    </div>
                </div>
            </div>
            
            <!-- Podcasts más populares -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-fire text-primary me-2"></i> Los más escuchados
                    </h5>
                </div>
                <div class="list-group list-group-flush" id="popular-podcasts">
                    <!-- Se cargará vía AJAX -->
                    <div class="list-group-item p-3 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Explicación sobre suscripción -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-rss text-primary me-2"></i> Suscríbete a nuestros podcasts
                    </h5>
                    <p class="card-text">Mantente al día con nuestros resúmenes de noticias diarios agregando nuestro feed RSS a tu aplicación de podcasts favorita.</p>
                    <div class="d-grid">
                        <a href="{{ route('podcasts.feed') }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-rss me-2"></i> Obtener feed RSS
                        </a>
                    </div>
                    <div class="mt-3">
                        <p class="small text-muted mb-1">Compatible con:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border">
                                <i class="fab fa-apple me-1"></i> Apple Podcasts
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fab fa-spotify me-1"></i> Spotify
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fab fa-google me-1"></i> Google Podcasts
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-podcast me-1"></i> Pocket Casts
                            </span>
                        </div>
                    </div>
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
    
    .podcast-item {
        transition: background-color 0.2s ease;
    }
    
    .podcast-item:hover {
        background-color: rgba(0, 0, 0, 0.01);
    }
    
    .podcast-item.active {
        background-color: rgba(13, 110, 253, 0.05);
        border-left: 3px solid #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias
    const player = document.getElementById('podcast-player');
    const noSelection = document.getElementById('no-podcast-selected');
    const currentInfo = document.getElementById('current-podcast-info');
    const currentTitle = document.getElementById('current-title');
    const currentDate = document.getElementById('current-date');
    
    // Cargar los podcasts más populares
    fetchPopularPodcasts();
    
    // Manejar clics en botones de reproducción
    document.querySelectorAll('.podcast-play-button').forEach(button => {
        button.addEventListener('click', function() {
            const audioUrl = this.dataset.audioUrl;
            const podcastId = this.dataset.podcastId;
            const podcastItem = document.querySelector(`.podcast-item[data-podcast-id="${podcastId}"]`);
            const podcastTitle = podcastItem.querySelector('h5').innerText;
            const podcastDate = podcastItem.querySelector('.me-3:first-child').innerText;
            
            // Cambiar estado visual
            document.querySelectorAll('.podcast-item').forEach(item => {
                item.classList.remove('active');
            });
            podcastItem.classList.add('active');
            
            // Si es el mismo podcast, solo alternar play/pause
            if (player.src.includes(audioUrl)) {
                if (player.paused) {
                    player.play();
                    updatePlayButton(this, true);
                } else {
                    player.pause();
                    updatePlayButton(this, false);
                }
                return;
            }
            
            // Reiniciar todos los botones
            document.querySelectorAll('.podcast-play-button').forEach(btn => {
                btn.querySelector('.play-btn').classList.remove('d-none');
                btn.querySelector('.pause-btn').classList.add('d-none');
            });
            
            // Configurar reproductor
            player.src = audioUrl;
            player.classList.remove('d-none');
            noSelection.classList.add('d-none');
            currentInfo.classList.remove('d-none');
            currentTitle.innerText = podcastTitle;
            currentDate.innerText = podcastDate;
            
            // Iniciar reproducción
            player.play();
            updatePlayButton(this, true);
            
            // Registrar reproducción
            registerPlay(podcastId);
        });
    });
    
    // Manejar eventos del reproductor
    player.addEventListener('play', function() {
        const activePodcastId = document.querySelector('.podcast-item.active')?.dataset.podcastId;
        if (activePodcastId) {
            const button = document.querySelector(`.podcast-play-button[data-podcast-id="${activePodcastId}"]`);
            updatePlayButton(button, true);
        }
    });
    
    player.addEventListener('pause', function() {
        const activePodcastId = document.querySelector('.podcast-item.active')?.dataset.podcastId;
        if (activePodcastId) {
            const button = document.querySelector(`.podcast-play-button[data-podcast-id="${activePodcastId}"]`);
            updatePlayButton(button, false);
        }
    });
    
    player.addEventListener('ended', function() {
        const activePodcastId = document.querySelector('.podcast-item.active')?.dataset.podcastId;
        if (activePodcastId) {
            const button = document.querySelector(`.podcast-play-button[data-podcast-id="${activePodcastId}"]`);
            updatePlayButton(button, false);
        }
    });
    
    // Funciones auxiliares
    function updatePlayButton(button, isPlaying) {
        if (isPlaying) {
            button.querySelector('.play-btn').classList.add('d-none');
            button.querySelector('.pause-btn').classList.remove('d-none');
        } else {
            button.querySelector('.play-btn').classList.remove('d-none');
            button.querySelector('.pause-btn').classList.add('d-none');
        }
    }
    
    function registerPlay(podcastId) {
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
                // Actualizar contador en la interfaz si es necesario
                const countElement = document.querySelector(`.podcast-item[data-podcast-id="${podcastId}"] .fa-headphones`).parentNode;
                if (countElement) {
                    countElement.innerHTML = `<i class="fas fa-headphones me-1"></i> ${new Intl.NumberFormat().format(data.play_count)} reproducciones`;
                }
                
                // Recargar podcasts populares después de un tiempo
                setTimeout(fetchPopularPodcasts, 2000);
            }
        })
        .catch(error => console.error('Error al registrar reproducción:', error));
    }
    
    function fetchPopularPodcasts() {
        const popularContainer = document.getElementById('popular-podcasts');
        
        fetch('/podcasts/popular')
            .then(response => response.json())
            .then(data => {
                popularContainer.innerHTML = '';
                
                if (data.length === 0) {
                    popularContainer.innerHTML = `
                        <div class="list-group-item p-3 text-center">
                            <p class="text-muted mb-0">No hay datos de podcasts populares todavía</p>
                        </div>
                    `;
                    return;
                }
                
                data.forEach(podcast => {
                    const date = new Date(podcast.published_at);
                    const formattedDate = date.toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    
                    popularContainer.innerHTML += `
                        <a href="/podcasts/${podcast.id}" class="list-group-item list-group-item-action p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary">
                                    <i class="fas fa-headphones me-1"></i> ${podcast.play_count} reproducciones
                                </span>
                                <small class="text-muted">
                                    ${podcast.formatted_duration || '5-10 min'}
                                </small>
                            </div>
                            <h6 class="mb-1">${podcast.title}</h6>
                            <div class="small text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> ${formattedDate}
                            </div>
                        </a>
                    `;
                });
            })
            .catch(error => {
                console.error('Error al cargar podcasts populares:', error);
                popularContainer.innerHTML = `
                    <div class="list-group-item p-3 text-center">
                        <p class="text-danger mb-0">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Error al cargar los podcasts populares
                        </p>
                    </div>
                `;
            });
    }
});
</script>
@endpush