{{-- resources/views/components/podcast-hero-banner.blade.php --}}

<div class="podcast-hero-banner py-2 bg-primary text-white border-bottom position-relative overflow-hidden">
    {{-- Elementos decorativos de fondo --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute start-0 top-50 translate-middle-y opacity-10"><i class="fas fa-microphone-alt fa-2x"></i></div>
        <div class="position-absolute end-0 top-50 translate-middle-y opacity-10"><i class="fas fa-podcast fa-2x"></i></div>
    </div>
    
    <div class="container position-relative">
        <div class="row align-items-center">
            @php
                // Find the latest daily summary podcast
                $dailySummary = App\Models\Podcast::where(function($query) { 
                    $query->where('is_daily_summary', true)->orWhere('is_daily_summary', 1); 
                })->orderBy('created_at', 'desc')->first();
                $audioPath = ''; $audioUrl = ''; $podcastId = 0; $podcastDuration = 0;
                $podcastDate = null;
                
                if ($dailySummary) {
                    $podcastId = $dailySummary->id;
                    $podcastDuration = $dailySummary->duration ?? 0;
                    $originalPath = $dailySummary->audio_path;
                    
                    // Obtener la fecha del podcast
                    $podcastDate = $dailySummary->created_at ?? now();
                    
                    // Try path from database first
                    if ($originalPath && file_exists(storage_path('app/public/' . $originalPath))) {
                        $audioPath = $originalPath;
                        $audioUrl = asset('storage/' . $audioPath);
                    }
                    // Fallback check common paths if DB path fails
                    else {
                        $today = now()->format('Y-m-d'); 
                        $yesterday = now()->subDay()->format('Y-m-d');
                        $possiblePaths = [
                            "podcast/daily-summary/{$today}/daily-summary-{$today}.mp3",
                            "podcast/daily-summary/{$yesterday}/daily-summary-{$yesterday}.mp3"
                        ];
                        foreach ($possiblePaths as $path) {
                            if (file_exists(storage_path('app/public/' . $path))) {
                                $audioPath = $path;
                                $audioUrl = asset('storage/' . $audioPath);
                                break;
                            }
                        }
                    }
                } else {
                    $podcastDate = now();
                }
                
                // Formatear la fecha del podcast para el mensaje
                $formattedDate = $podcastDate->format('d/m');
            @endphp

            {{-- Icono y título --}}
            <div class="col-lg-3 col-md-3 mb-1 mb-md-0">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-podcast"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fs-6 fw-bold">Resumen Diario de Noticias</h5>
                        <div class="text-white-50 fs-9">{{ $podcastDate->locale('es')->isoFormat('D [de] MMMM') }} | <i class="fas fa-robot"></i> AI</div>
                    </div>
                    <span class="badge bg-danger ms-2 rounded-pill px-2">NUEVO</span>
                </div>
            </div>
            
            {{-- Reproductor audio --}}
            <div class="col-lg-6 col-md-6 mb-1 mb-md-0">
                <div class="hero-audio-player d-flex align-items-center">
                    @if(!empty($audioUrl))
                        {{-- Audio tag (hidden) --}}
                        <audio id="heroBannerPlayer" preload="metadata" controlsList="nodownload" style="display: none;">
                            <source src="{{ $audioUrl }}" type="audio/mpeg">
                            Tu navegador no soporta el elemento de audio.
                        </audio>
                        
                        {{-- Play Button --}}
                        <button id="heroBannerPlayBtn" class="btn btn-light btn-sm rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                data-playing="false" data-podcast-id="{{ $podcastId }}" 
                                style="width: 32px; height: 32px; min-width: 32px;"
                                aria-label="Reproducir/Pausar Resumen Diario">
                            <i class="fas fa-play"></i>
                        </button>
                        
                        <div class="d-flex flex-column flex-grow-1">
                            {{-- Información de noticias y duración --}}
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-white-50">
                                    <i class="fas fa-newspaper me-1"></i> {{ $dailySummary ? $dailySummary->news_count : 0 }} noticias incluidas
                                </span>
                                <span class="small text-white-50">
                                    <i class="far fa-clock me-1"></i> {{ gmdate("i:s", $podcastDuration) }}
                                </span>
                            </div>
                            
                            {{-- Progress Bar --}}
                            <div class="progress" style="height: 4px; cursor: pointer;" id="heroBannerProgressContainer">
                                <div id="heroBannerProgress" class="progress-bar bg-white" role="progressbar" 
                                    style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-white-50 fs-9 mt-1">
                                <span id="heroBannerCurrentTime">0:00</span>
                                <span class="d-none d-sm-inline">Resumen de noticias del día {{ $formattedDate }}</span>
                                <span id="heroBannerDuration">{{ gmdate("i:s", $podcastDuration) }}</span>
                            </div>
                        </div>
                    @else
                        {{-- Fallback when no audio URL --}}
                        <div class="w-100 text-center text-white-50">
                            <small><i class="fas fa-info-circle me-1"></i>Resumen diario no disponible actualmente</small>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Botones acción --}}
            <div class="col-lg-3 col-md-3 text-center text-md-end">
                <a href="{{ route('podcasts.index') }}" class="btn btn-sm btn-light rounded-pill px-2 px-md-3">
                    <i class="fas fa-headphones-alt me-1"></i> Más podcasts
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Estilos para el banner de podcast en el hero */
    .podcast-hero-banner {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .hero-audio-player .progress {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    #heroBannerPlayBtn {
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    #heroBannerPlayBtn:hover {
        transform: scale(1.1);
    }
    
    #heroBannerPlayBtn:active {
        transform: scale(0.95);
    }
    
    .fs-9 {
        font-size: 0.7rem !important;
    }
    
    @media (max-width: 767.98px) {
        .podcast-hero-banner {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper Function
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
    }
    
    // Banner Player Elements
    const heroBannerPlayer = document.getElementById('heroBannerPlayer');
    const heroBannerPlayBtn = document.getElementById('heroBannerPlayBtn');
    const heroBannerProgress = document.getElementById('heroBannerProgress');
    const heroBannerCurrentTime = document.getElementById('heroBannerCurrentTime');
    const heroBannerDuration = document.getElementById('heroBannerDuration');
    const heroBannerProgressContainer = document.getElementById('heroBannerProgressContainer');
    
    if (heroBannerPlayer && heroBannerPlayBtn && heroBannerProgress) {
        // Get source URL
        const sourceElement = heroBannerPlayer.querySelector('source');
        const sourceUrl = sourceElement ? sourceElement.getAttribute('src') : null;
        
        // Play/Pause Button Handler
        heroBannerPlayBtn.addEventListener('click', function() {
            if (!sourceUrl) {
                console.error("Cannot play: Source URL not found");
                return;
            }
            
            // Set URL if needed
            if (!heroBannerPlayer.currentSrc || heroBannerPlayer.currentSrc !== sourceUrl) {
                heroBannerPlayer.src = sourceUrl;
                try {
                    heroBannerPlayer.load();
                } catch (loadError) {
                    console.error('Error loading audio:', loadError);
                    return;
                }
            }
            
            // Determine current state
            const isPlaying = !heroBannerPlayer.paused;
            
            if (isPlaying) {
                // Si ya está reproduciendo, pausar
                heroBannerPlayer.pause();
                this.innerHTML = '<i class="fas fa-play"></i>';
                this.setAttribute('data-playing', 'false');
            } else {
                // Pause other players first
                const otherPlayers = document.querySelectorAll('audio');
                for (let i = 0; i < otherPlayers.length; i++) {
                    if (otherPlayers[i] !== heroBannerPlayer) {
                        otherPlayers[i].pause();
                    }
                }
                
                // Attempt to play after a short delay
                setTimeout(() => {
                    // Play this audio
                    const playPromise = heroBannerPlayer.play();
                    
                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                this.innerHTML = '<i class="fas fa-pause"></i>';
                                this.setAttribute('data-playing', 'true');
                                
                                // Register Play
                                const podcastId = this.getAttribute('data-podcast-id');
                                if (podcastId && podcastId !== '0') {
                                    fetch(`/podcasts/${podcastId}/play`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    }).catch(error => console.error('Error registering play:', error));
                                }
                            })
                            .catch(error => {
                                console.error('Error playing audio:', error);
                                this.innerHTML = '<i class="fas fa-play"></i>';
                                this.setAttribute('data-playing', 'false');
                            });
                    }
                }, 100);
            }
        });
        
        // Update Progress
        heroBannerPlayer.addEventListener('timeupdate', function() {
            if (this.duration && !isNaN(this.duration) && this.duration > 0) {
                const percent = (this.currentTime / this.duration) * 100;
                heroBannerProgress.style.width = percent + '%';
                heroBannerProgress.setAttribute('aria-valuenow', percent);
                heroBannerCurrentTime.textContent = formatTime(this.currentTime);
            }
        });
        
        // Handle End
        heroBannerPlayer.addEventListener('ended', function() {
            heroBannerPlayBtn.innerHTML = '<i class="fas fa-play"></i>';
            heroBannerPlayBtn.setAttribute('data-playing', 'false');
            heroBannerProgress.style.width = '0%';
            heroBannerCurrentTime.textContent = '0:00';
        });
        
        // Seek Functionality
        if (heroBannerProgressContainer) {
            heroBannerProgressContainer.addEventListener('click', function(e) {
                if (heroBannerPlayer.readyState < heroBannerPlayer.HAVE_METADATA) return;
                if (!heroBannerPlayer.duration || isNaN(heroBannerPlayer.duration)) return;
                
                const rect = this.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const width = rect.width;
                const seekRatio = Math.max(0, Math.min(1, clickX / width));
                const seekTime = heroBannerPlayer.duration * seekRatio;
                
                heroBannerPlayer.currentTime = seekTime;
                heroBannerProgress.style.width = (seekRatio * 100) + '%';
                heroBannerCurrentTime.textContent = formatTime(seekTime);
            });
        }
    }
});
</script>
@endpush