{{-- resources/views/components/home-podcasts-section.blade.php --}}

<div class="py-3 bg-dark text-white mb-0 position-relative overflow-hidden">
    {{-- Background elements --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden">
        <div class="position-absolute start-0 top-50 translate-middle-y opacity-10"><i class="fas fa-microphone-alt fa-3x"></i></div>
        <div class="position-absolute end-0 top-50 translate-middle-y opacity-10"><i class="fas fa-podcast fa-3x"></i></div>
    </div>
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-0 text-uppercase fw-bold fs-4"><span class="d-inline-block border-bottom border-2 pb-1">Podcasts</span></h3>
                <p class="mb-0 mt-1 text-white-50 fs-6">Escucha nuestras noticias destacadas en formato audio</p>
            </div>
        </div>
    </div>
</div>

<section class="py-4 bg-white">
    <div class="container">
        {{-- Daily Summary --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="daily-podcast-widget card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            {{-- Left Column: Player --}}
                            <div class="col-lg-5 position-relative bg-primary text-white bg-gradient">
                                {{-- Background decoration --}}
                                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 overflow-hidden"><div class="position-absolute top-50 start-50 translate-middle"><i class="fas fa-broadcast-tower fa-5x"></i></div></div>
                                <div class="p-4 h-100 d-flex flex-column justify-content-between position-relative">
                                    {{-- Header --}}
                                    <div class="text-center mb-3">
                                        <div class="bg-white text-primary rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;"><i class="fas fa-podcast fa-2x"></i></div>
                                        <h4 class="mb-1 fw-bold fs-5">Resumen Diario</h4>
                                        <div class="text-white-50 fs-7 mb-2">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</div>
                                        <div class="badge bg-white text-primary rounded-pill px-3 py-1"><i class="fas fa-headphones-alt me-1"></i> AI Voice</div>
                                    </div>

                                    {{-- Audio Player Area --}}
                                    <div class="audio-player-container mb-3">
                                        @php
                                            // Find the latest daily summary podcast
                                            $dailySummary = App\Models\Podcast::where(function($query) { $query->where('is_daily_summary', true)->orWhere('is_daily_summary', 1); })->orderBy('created_at', 'desc')->first();
                                            $audioPath = ''; $audioUrl = ''; $podcastId = 0; $podcastDuration = 0;
                                            if ($dailySummary) {
                                                $podcastId = $dailySummary->id;
                                                $podcastDuration = $dailySummary->duration ?? 0;
                                                $originalPath = $dailySummary->audio_path;
                                                // Try path from database first
                                                if ($originalPath && file_exists(storage_path('app/public/' . $originalPath))) {
                                                    $audioPath = $originalPath;
                                                    $audioUrl = asset('storage/' . $audioPath);
                                                }
                                                // Fallback: Check common paths if DB path fails
                                                else {
                                                    $today = now()->format('Y-m-d'); $yesterday = now()->subDay()->format('Y-m-d');
                                                    $possiblePaths = [ "podcast/daily-summary/{$today}/daily-summary-{$today}.mp3", "podcast/daily-summary/{$yesterday}/daily-summary-{$yesterday}.mp3" ];
                                                    foreach ($possiblePaths as $path) {
                                                        if (file_exists(storage_path('app/public/' . $path))) { $audioPath = $path; $audioUrl = asset('storage/' . $audioPath); break; }
                                                    }
                                                }
                                            }
                                        @endphp

                                        <div class="player-wrapper position-relative">
                                            @if(!empty($audioUrl))
                                                {{-- The audio tag itself (hidden) --}}
                                                <audio id="dailySummaryPlayer" preload="metadata" controlsList="nodownload" style="display: none;">
                                                    {{-- Source tag is still useful for preload="metadata" --}}
                                                    <source src="{{ $audioUrl }}" type="audio/mpeg">
                                                    Tu navegador no soporta el elemento de audio.
                                                </audio>

                                                {{-- Debugging info --}}
                                                @if(app()->environment('local'))
                                                <div class="d-none">
                                                    <p>Ruta Detectada: {{ $audioPath ?: 'Ninguna' }}</p>
                                                    <p>URL Generada: {{ $audioUrl ?: 'Ninguna' }}</p>
                                                    <p>ID Podcast: {{ $podcastId }}</p>
                                                </div>
                                                @endif

                                                {{-- Custom Controls --}}
                                                <div class="custom-audio-player">
                                                    <div class="play-button-wrapper text-center mb-3">
                                                        <button id="dailySummaryPlayBtn" class="btn btn-light rounded-circle play-btn" data-playing="false" data-podcast-id="{{ $podcastId }}" aria-label="Reproducir/Pausar Resumen Diario"><i class="fas fa-play"></i></button>
                                                        <div id="playerStatus" class="mt-2 text-white-50 small"></div>
                                                    </div>
                                                    <div class="progress mb-2" style="height: 6px; cursor: pointer;" id="dailySummaryProgressContainer">
                                                        <div id="dailySummaryProgress" class="progress-bar bg-white" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between text-white-50 fs-7">
                                                        <span id="dailySummaryCurrentTime">0:00</span>
                                                        <span id="dailySummaryDuration">{{ gmdate("i:s", $podcastDuration) }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Fallback when no audio URL --}}
                                                <div class="text-center text-white py-3">
                                                    <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Resumen diario no disponible</p>
                                                    @if(app()->environment('local')) <small class="d-block mt-1 text-white-50">Intento de ID: {{ $dailySummary->id ?? 'N/A' }} | Ruta DB: {{ $dailySummary->audio_path ?? 'N/A' }}</small> @endif
                                                    <div class="play-button-wrapper text-center mt-3"><button class="btn btn-light rounded-circle play-btn" disabled aria-label="Resumen no disponible"><i class="fas fa-volume-mute"></i></button></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Footer Stats --}}
                                    <div class="text-center">
                                        <div class="d-flex justify-content-center text-white-50 fs-7 mb-3">
                                            <div class="me-3"><i class="fas fa-newspaper me-1"></i> {{ $dailySummary ? $dailySummary->news_count : 0 }} noticias</div>
                                            <div><i class="fas fa-play-circle me-1"></i> {{ $dailySummary ? number_format($dailySummary->play_count) : 0 }} reproducciones</div>
                                        </div>
                                        <div><a href="{{ route('podcasts.index') }}" class="btn btn-sm btn-light rounded-pill px-3">Ver más podcasts <i class="fas fa-headphones ms-1"></i></a></div>
                                    </div>
                                </div>
                            </div>
                            {{-- Right Column: News List --}}
                            <div class="col-lg-7">
                                <div class="p-4 h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0 fw-bold fs-5">Noticias incluidas</h5>
                                        <span class="badge bg-primary rounded-pill px-3 fs-7"><i class="fas fa-volume-up me-1"></i> Audio generado por IA</span>
                                    </div>
                                    <div class="news-list flex-grow-1 overflow-auto" style="max-height: 300px;">
                                        @php $recentTopNews = \App\Models\News::published()->orderBy('views', 'desc')->where('created_at', '>=', now()->subDays(1))->take(6)->get(); @endphp
                                        <div class="list-group list-group-flush">
                                            @forelse($recentTopNews as $index => $news)
                                            <a href="{{ route('news.show', $news->slug ?? $news->id) }}" class="list-group-item list-group-item-action px-1 py-2 border-bottom-dashed">
                                                <div class="d-flex align-items-start">
                                                    <div class="category-icon flex-shrink-0 me-2">
                                                        @if(isset($news->category)) <span class="category-badge rounded-circle d-inline-flex align-items-center justify-content-center" style="background-color: {{ str_replace('background-color:', '', $getCategoryStyle($news->category)) }}; width: 36px; height: 36px; color: white;"><i class="fas {{ $getCategoryIcon($news->category) }}"></i></span>
                                                        @else <span class="category-badge rounded-circle d-inline-flex align-items-center justify-content-center bg-secondary" style="width: 36px; height: 36px; color: white;"><i class="fas fa-newspaper"></i></span> @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between">@if(isset($news->category))<small class="text-muted">{{ $news->category->name }}</small>@endif <small class="text-muted"><i class="far fa-eye me-1"></i>{{ number_format($news->views) }}</small></div>
                                                        <h6 class="mb-0 fs-7 fw-bold">{{ $news->title }}</h6>
                                                        <p class="mb-0 text-muted small line-clamp-1">{{ Str::limit($news->excerpt, 80) }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                            @empty <p class="text-muted text-center p-3">No hay noticias recientes para mostrar.</p> @endforelse
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <p class="text-muted small mb-3"><i class="fas fa-info-circle me-1"></i> Cada día generamos un podcast con las noticias más importantes utilizando inteligencia artificial.</p>
                                        <div class="d-flex justify-content-center align-items-center"><span class="me-2 text-muted small"><i class="far fa-clock me-1"></i>Actualizado diariamente</span> <span class="text-muted small"><i class="fas fa-robot me-1"></i>{{ $dailySummary ? 'Generado por IA' : 'Disponible pronto' }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Podcasts --}}
        <div class="row">
            <div class="col-md-8"><h3 class="section-title fs-5 mb-3">Podcasts Recientes</h3></div>
            <div class="col-md-4 text-md-end mb-3"><a href="{{ route('podcasts.index') }}" class="btn btn-outline-primary btn-sm">Ver todos <i class="fas fa-arrow-right ms-2"></i></a></div>
            <div class="col-12">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
                    @php $recentPodcasts = \App\Models\Podcast::where(function($query) { $query->where('is_daily_summary', false)->orWhereNull('is_daily_summary'); })->orderBy('created_at', 'desc')->take(4)->get(); @endphp
                    @forelse($recentPodcasts as $podcast)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden podcast-card hover-lift">
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="podcast-icon rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-headphones text-primary"></i></div>
                                    <span class="badge bg-secondary rounded-pill fs-9">{{ gmdate("i:s", $podcast->duration ?? 0) }}</span>
                                </div>
                                <h6 class="card-title mb-2 fw-bold text-truncate"><a href="{{ route('podcasts.show', $podcast->id) }}" class="text-decoration-none text-dark stretched-link" title="{{ $podcast->title }}">{{ Str::limit($podcast->title, 50) }}</a></h6>
                                <div class="audio-mini-player mb-3 mt-auto">
                                     @php $miniAudioUrl = ''; if ($podcast->audio_path && file_exists(storage_path('app/public/' . $podcast->audio_path))) { $miniAudioUrl = asset('storage/' . $podcast->audio_path); } @endphp
                                    @if($miniAudioUrl) <button class="btn btn-sm btn-outline-primary rounded-pill w-100 mini-play-btn" data-audio-src="{{ $miniAudioUrl }}" data-podcast-id="{{ $podcast->id }}" aria-label="Reproducir {{ Str::limit($podcast->title, 30) }}"><i class="fas fa-play me-1"></i> Reproducir</button>
                                    @else <button class="btn btn-sm btn-outline-secondary rounded-pill w-100" disabled><i class="fas fa-times me-1"></i> No disponible</button> @endif
                                </div>
                                <div class="d-flex justify-content-between text-muted small"><span><i class="fas fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($podcast->created_at)->locale('es')->format('d M') }}</span> <span><i class="fas fa-play-circle me-1"></i>{{ number_format($podcast->play_count) }}</span></div>
                            </div>
                        </div>
                    </div>
                    @empty <div class="col-12"><p class="text-muted text-center">No hay otros podcasts recientes.</p></div> @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Styles for the daily podcast widget */
    .daily-podcast-widget { transition: all 0.3s ease; }
    .daily-podcast-widget:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important; }
    /* Player styles */
    .play-btn { width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; transition: all 0.2s ease; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); }
    .play-btn:hover { transform: scale(1.1); }
    .play-btn:active { transform: scale(0.95); }
    /* News item styles */
    .border-bottom-dashed { border-bottom: 1px dashed rgba(0, 0, 0, 0.1) !important; }
    .list-group-item-action:hover { background-color: rgba(0, 0, 0, 0.02); }
    .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    /* Podcast card styles */
    .podcast-card { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important; }
    .fs-7 { font-size: 0.875rem !important; }
    .fs-9 { font-size: 0.7rem !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Helper Function ---
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
    }


    // Check if the main player elements exist
    if (dailySummaryPlayer && dailySummaryPlayBtn && dailySummaryProgress && dailySummaryCurrentTime && dailySummaryDuration) {

        console.log('Daily Summary Player elements found.');
        const initialSourceElement = dailySummaryPlayer.querySelector('source'); // Get the source element
        const initialSourceUrl = initialSourceElement ? initialSourceElement.getAttribute('src') : null; // Get URL from source tag
        console.log('Initial source URL from tag:', initialSourceUrl);

        // --- Play/Pause Button Click Handler ---
        dailySummaryPlayBtn.addEventListener('click', function() {
            console.log('Play button clicked.');
            playerStatus.textContent = ""; // Clear previous status messages

            // --- NEW STRATEGY: Manually set src ---
            if (!initialSourceUrl) {
                console.error("Cannot play: Source URL not found in the source tag.");
                playerStatus.textContent = "Error: Falta URL de audio.";
                return;
            }

            // Check if the src needs to be set or if it's already set (e.g., after first play)
            // Also check if the network state indicates an error or empty source
             if (!dailySummaryPlayer.currentSrc || dailySummaryPlayer.currentSrc !== initialSourceUrl ||
                dailySummaryPlayer.networkState === dailySummaryPlayer.NETWORK_NO_SOURCE ||
                dailySummaryPlayer.networkState === dailySummaryPlayer.NETWORK_EMPTY)
            {
                console.log(`Setting player src to: ${initialSourceUrl}`);
                dailySummaryPlayer.src = initialSourceUrl;
                try {
                    console.log("Calling load() after setting src...");
                    dailySummaryPlayer.load(); // Important: Load the new source
                } catch (loadError) {
                    console.error('Error calling load() after setting src:', loadError);
                    playerStatus.textContent = "Error al cargar fuente.";
                    return;
                }
            } else {
                 console.log("Player src already set to the correct URL.");
            }


            // --- Wait briefly for potential load() processing ---
            // This timeout might still be useful to ensure the browser is ready
            setTimeout(() => {

                // --- Check if player is ready to play ---
                // Rely more on readyState now. HAVE_FUTURE_DATA or HAVE_ENOUGH_DATA are good states.
                if (dailySummaryPlayer.readyState < dailySummaryPlayer.HAVE_FUTURE_DATA) {
                     console.error('Player not ready to play. State:', dailySummaryPlayer.readyState);
                     playerStatus.textContent = "Audio no listo. Intente de nuevo.";
                     // You might want to try load() again here or just inform the user
                     return;
                }


                // --- Play/Pause Logic ---
                if (dailySummaryPlayer.paused) {
                    console.log('Attempting to play...');
                    playerStatus.textContent = "Cargando..."; // Indicate loading

                    // Pause other players (if any)
                    document.querySelectorAll('audio').forEach(audio => {
                        if (audio !== dailySummaryPlayer) audio.pause();
                    });
                    document.querySelectorAll('.mini-play-btn').forEach(btn => {
                         btn.innerHTML = '<i class="fas fa-play me-1"></i> Reproducir';
                         btn.setAttribute('data-playing', 'false');
                    });


                    // --- Play the audio ---
                    const playPromise = dailySummaryPlayer.play();

                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                // --- Playback Started Successfully ---
                                console.log('Playback started.');
                                playerStatus.textContent = ""; // Clear loading message
                                this.innerHTML = '<i class="fas fa-pause"></i>'; // Change button icon to pause
                                this.setAttribute('data-playing', 'true');

                                // --- Register Play (Analytics) ---
                                const podcastId = this.getAttribute('data-podcast-id');
                                if (podcastId && podcastId !== '0') {
                                    fetch(`/podcasts/${podcastId}/play`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => console.log('Play registered:', data))
                                    .catch(error => console.error('Error registering play:', error));
                                }
                            })
                            .catch(error => {
                                // --- Playback Failed ---
                                console.error('Error playing audio:', error);
                                playerStatus.textContent = "Error al reproducir.";
                                this.innerHTML = '<i class="fas fa-play"></i>'; // Reset button icon
                                this.setAttribute('data-playing', 'false');
                            });
                    } else {
                         console.warn('play() did not return a promise.');
                    }

                } else {
                    // --- Pause the audio ---
                    console.log('Pausing audio.');
                    dailySummaryPlayer.pause();
                    playerStatus.textContent = ""; // Clear status
                    this.innerHTML = '<i class="fas fa-play"></i>'; // Change button icon to play
                    this.setAttribute('data-playing', 'false');
                }

            }, 150); // Timeout duration (milliseconds)
        });

        // --- Update Progress Bar and Time ---
        dailySummaryPlayer.addEventListener('timeupdate', function() {
            if (this.duration && !isNaN(this.duration) && this.duration > 0) {
                const percent = (this.currentTime / this.duration) * 100;
                dailySummaryProgress.style.width = percent + '%';
                dailySummaryProgress.setAttribute('aria-valuenow', percent);
                dailySummaryCurrentTime.textContent = formatTime(this.currentTime);
            }
        });

        // --- Handle Audio End ---
        dailySummaryPlayer.addEventListener('ended', function() {
            console.log('Audio ended.');
            playerStatus.textContent = "Finalizado";
            dailySummaryPlayBtn.innerHTML = '<i class="fas fa-play"></i>'; // Reset button
            dailySummaryPlayBtn.setAttribute('data-playing', 'false');
            dailySummaryProgress.style.width = '0%'; // Reset progress bar
            dailySummaryProgress.setAttribute('aria-valuenow', 0);
            dailySummaryCurrentTime.textContent = '0:00'; // Reset current time
        });

         // --- Handle Audio Loading Errors ---
        dailySummaryPlayer.addEventListener('error', function(e) {
            const errorCode = e.target.error ? e.target.error.code : 'unknown';
            let errorDetails = 'Detalles no disponibles';
            // Use currentSrc for the attempted URL when an error occurs after setting src
            let audioSrcAttempted = this.currentSrc || initialSourceUrl || 'URL no disponible';

            if (e.target.error) {
                 switch (e.target.error.code) {
                    case MediaError.MEDIA_ERR_ABORTED: errorDetails = 'Carga abortada.'; break;
                    case MediaError.MEDIA_ERR_NETWORK: errorDetails = 'Error de red.'; break;
                    case MediaError.MEDIA_ERR_DECODE: errorDetails = 'Error de decodificación.'; break;
                    case MediaError.MEDIA_ERR_SRC_NOT_SUPPORTED: errorDetails = 'Fuente no soportada/encontrada.'; break;
                    default: errorDetails = 'Error desconocido.'; break;
                }
            }
            console.error(`ERROR DE AUDIO DETECTADO - Código: ${errorCode}, Detalles: ${errorDetails}, URL Intentada: ${audioSrcAttempted}`);
            console.error('Objeto de error completo:', e.target.error);

            playerStatus.textContent = `Error: ${errorDetails}`;
            dailySummaryPlayBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i>'; // Indicate error
            dailySummaryPlayBtn.disabled = true; // Disable button
        });

        // --- Update Duration on Metadata Load ---
         dailySummaryPlayer.addEventListener('loadedmetadata', function() {
            if (this.duration && !isNaN(this.duration)) {
                console.log('Metadata loaded, duration:', this.duration);
                dailySummaryDuration.textContent = formatTime(this.duration);
            } else {
                console.warn('Metadata loaded, but duration is invalid:', this.duration);
            }
        });

        // --- Seek Functionality ---
        if (dailySummaryProgressContainer) {
            dailySummaryProgressContainer.addEventListener('click', function(e) {
                // Use readyState to check if seeking is possible
                if (dailySummaryPlayer.readyState < dailySummaryPlayer.HAVE_METADATA) {
                    console.log('Cannot seek: player not ready.');
                    return;
                }
                 if (!dailySummaryPlayer.duration || isNaN(dailySummaryPlayer.duration)) {
                     console.log('Cannot seek: duration unknown.');
                     return;
                 }
                const rect = this.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const width = rect.width;
                const seekRatio = Math.max(0, Math.min(1, clickX / width)); // Clamp between 0 and 1
                const seekTime = dailySummaryPlayer.duration * seekRatio;

                console.log(`Seeking to ${formatTime(seekTime)} (${(seekRatio * 100).toFixed(1)}%)`);
                dailySummaryPlayer.currentTime = seekTime;

                // Update progress immediately
                dailySummaryProgress.style.width = (seekRatio * 100) + '%';
                dailySummaryProgress.setAttribute('aria-valuenow', seekRatio * 100);
                dailySummaryCurrentTime.textContent = formatTime(seekTime);
            });
        }

    } else {
        console.error('Could not find all necessary elements for the Daily Summary Player.');
    }


    // --- Mini Player Logic (Mostly unchanged, ensure it pauses main player) ---
    let activeMiniPlayer = null;

    document.querySelectorAll('.mini-play-btn').forEach(button => {
        const audioSrc = button.getAttribute('data-audio-src');
        const podcastId = button.getAttribute('data-podcast-id');
        let audioElement = null;

        if (!audioSrc) {
            console.warn(`Mini player button for podcast ID ${podcastId} has no audio source.`);
            button.disabled = true; return;
        }

        button.addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            console.log(`Mini play clicked for podcast ${podcastId}`);

            if (!audioElement) {
                console.log(`Creating new audio element for ${podcastId}`);
                audioElement = new Audio(audioSrc);
                audioElement.preload = 'metadata';
                audioElement.setAttribute('data-podcast-id', podcastId);

                audioElement.addEventListener('ended', () => {
                    button.innerHTML = '<i class="fas fa-play me-1"></i> Reproducir'; button.setAttribute('data-playing', 'false');
                    if (activeMiniPlayer === audioElement) activeMiniPlayer = null;
                });
                audioElement.addEventListener('error', (err) => {
                    console.error(`Error mini audio ${podcastId}:`, err, audioElement.error);
                    button.innerHTML = '<i class="fas fa-times me-1"></i> Error'; button.disabled = true;
                    if (activeMiniPlayer === audioElement) activeMiniPlayer = null;
                });
                 audioElement.addEventListener('pause', () => {
                    button.innerHTML = '<i class="fas fa-play me-1"></i> Reproducir'; button.setAttribute('data-playing', 'false');
                    if (activeMiniPlayer === audioElement) activeMiniPlayer = null;
                 });
                 audioElement.addEventListener('play', () => {
                     button.innerHTML = '<i class="fas fa-pause me-1"></i> Pausar'; button.setAttribute('data-playing', 'true');
                 });
            }

            if (audioElement.paused) {
                // --- Pause Main Player ---
                if (dailySummaryPlayer && !dailySummaryPlayer.paused) {
                    dailySummaryPlayer.pause();
                    if(dailySummaryPlayBtn) { dailySummaryPlayBtn.innerHTML = '<i class="fas fa-play"></i>'; dailySummaryPlayBtn.setAttribute('data-playing', 'false'); }
                }
                // --- Pause Other Mini Player ---
                if (activeMiniPlayer && activeMiniPlayer !== audioElement) { activeMiniPlayer.pause(); }

                // --- Play This Mini Player ---
                activeMiniPlayer = audioElement;
                const miniPlayPromise = audioElement.play();
                if (miniPlayPromise !== undefined) {
                    miniPlayPromise
                        .then(() => { fetch(`/podcasts/${podcastId}/play`, { /* headers */ }).catch(error => console.error('Error registering mini play:', error)); })
                        .catch(error => { console.error(`Error playing mini audio ${podcastId}:`, error); button.innerHTML = '<i class="fas fa-times me-1"></i> Error'; button.disabled = true; activeMiniPlayer = null; });
                }
            } else {
                audioElement.pause();
            }
        });
    });
});
</script>
@endpush
