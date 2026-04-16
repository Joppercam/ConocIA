@extends('layouts.app')

@section('title', 'ConocIA Radio — Briefings diarios de IA')
@section('meta_description', 'Escuchá el resumen diario de inteligencia artificial generado por IA. Todos los episodios de ConocIA Radio.')

@section('content')

{{-- ══ HEADER ══ --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #1e1e1e;" class="py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:var(--primary-color);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-microphone text-white" style="font-size:.95rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:1.1rem;letter-spacing:.01em;">
                            ConocIA <span style="color:var(--primary-color);">Radio</span>
                        </div>
                        <div style="color:#666;font-size:.7rem;margin-top:-2px;">Briefings diarios generados por IA</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 ms-2"
                     style="background:rgba(56,182,255,.12);border:1px solid rgba(56,182,255,.2);border-radius:20px;padding:3px 10px;">
                    <span class="radio-on-air-dot"></span>
                    <span style="color:var(--primary-color);font-size:.65rem;font-weight:700;letter-spacing:.08em;">ON AIR</span>
                </div>
            </div>
            <nav style="font-size:.8rem;">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active text-light">ConocIA Radio</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- ══ EPISODIO DESTACADO ══ --}}
@if($featured)
<section style="background:linear-gradient(135deg,#0a0f1e 0%,#0d1525 50%,#0a0f1e 100%);border-bottom:1px solid #1a2035;">
    <div class="container py-5">
        <div class="row align-items-center g-4">

            {{-- Artwork --}}
            <div class="col-lg-4 text-center">
                <div class="radio-artwork mx-auto">
                    <div class="radio-artwork-inner">
                        <i class="fas fa-microphone-alt"></i>
                        <div class="radio-artwork-rings">
                            <div class="radio-ring radio-ring-1"></div>
                            <div class="radio-ring radio-ring-2"></div>
                            <div class="radio-ring radio-ring-3"></div>
                        </div>
                    </div>
                    <div class="radio-waveform-art mt-3">
                        @for($i = 0; $i < 32; $i++)
                        <div class="radio-art-bar" style="animation-delay:{{ $i * 0.07 }}s;"></div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Info + player --}}
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="background:var(--primary-color);color:#000;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;letter-spacing:.06em;">ÚLTIMO EPISODIO</span>
                    <span style="color:#555;font-size:.75rem;">{{ $featured->episodes_count ?? '' }}</span>
                </div>

                <h1 class="text-white fw-bold mb-1" style="font-size:1.6rem;">
                    ConocIA Briefing
                </h1>
                <p style="color:var(--primary-color);font-size:.9rem;" class="mb-3">
                    {{ $featured->formatted_date }}
                </p>

                {{-- Inline player --}}
                <div class="radio-player" id="featured-player"
                     data-script="{{ e($featured->script) }}"
                     data-duration="{{ $featured->duration_seconds }}"
                     data-date="{{ $featured->formatted_date }}"
                     data-count="{{ $featured->news_count }}">

                    <div class="d-flex align-items-center gap-3">
                        <button class="radio-play-btn" data-player="featured-player" title="Reproducir">
                            <i class="fas fa-play radio-play-icon"></i>
                        </button>

                        <div class="flex-grow-1">
                            <div class="radio-waveform mb-1" aria-hidden="true">
                                @for($i = 0; $i < 50; $i++)
                                <div class="radio-bar" style="animation-delay:{{ $i * 0.055 }}s;"></div>
                                @endfor
                            </div>
                            <div class="radio-progress-track">
                                <div class="radio-progress-fill"></div>
                                <div class="radio-progress-thumb"></div>
                            </div>
                        </div>

                        <div class="text-end flex-shrink-0" style="min-width:56px;">
                            <span class="radio-time-current" style="color:#888;font-size:.75rem;">0:00</span>
                            <span style="color:#444;font-size:.75rem;"> / </span>
                            <span class="radio-time-total" style="color:#666;font-size:.75rem;">
                                {{ floor($featured->duration_seconds/60) }}:{{ str_pad($featured->duration_seconds%60,2,'0',STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="d-flex align-items-center gap-3 mt-3 flex-wrap">
                    <span style="color:#666;font-size:.8rem;">
                        <i class="fas fa-newspaper me-1" style="color:var(--primary-color);"></i>
                        {{ $featured->news_count }} noticias cubiertas
                    </span>
                    <span style="color:#666;font-size:.8rem;">
                        <i class="fas fa-clock me-1" style="color:var(--primary-color);"></i>
                        {{ $featured->estimated_minutes }}
                    </span>
                    <a href="{{ route('radio.show', $featured->date->toDateString()) }}"
                       style="color:var(--primary-color);font-size:.8rem;text-decoration:none;">
                        <i class="fas fa-expand-alt me-1"></i>Ver episodio completo
                    </a>
                </div>

                {{-- Headlines --}}
                @if($featured->headlines)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach($featured->headlines as $h)
                    <a href="{{ $h['url'] }}" class="radio-headline-chip text-decoration-none">
                        <span class="radio-headline-dot" style="background:{{ $h['color'] ?? '#38b6ff' }};"></span>
                        <span>{{ Str::limit($h['title'], 50) }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@else
<section style="background:var(--dark-bg);" class="py-5 text-center">
    <div class="container">
        <i class="fas fa-microphone-slash fa-3x mb-3" style="color:#333;"></i>
        <p style="color:#555;">Aún no hay episodios disponibles. El primero se generará automáticamente mañana.</p>
    </div>
</section>
@endif

{{-- ══ ARCHIVO ══ --}}
@if($archive->count())
<section style="background:var(--dark-surface);" class="py-5">
    <div class="container">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <div style="width:4px;height:22px;background:var(--primary-color);border-radius:2px;"></div>
                <h2 class="text-white fw-bold mb-0" style="font-size:1.15rem;">Episodios anteriores</h2>
            </div>
            <span style="color:#555;font-size:.8rem;">{{ $archive->count() }} episodio{{ $archive->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach($archive as $ep)
            <div class="radio-episode-row radio-player"
                 data-script="{{ e($ep->script) }}"
                 data-duration="{{ $ep->duration_seconds }}"
                 data-date="{{ $ep->formatted_date }}"
                 data-count="{{ $ep->news_count }}"
                 id="ep-{{ $ep->id }}">

                <div class="d-flex align-items-center gap-3">
                    {{-- Play --}}
                    <button class="radio-play-btn radio-play-btn--sm flex-shrink-0"
                            data-player="ep-{{ $ep->id }}" title="Reproducir">
                        <i class="fas fa-play radio-play-icon" style="font-size:.75rem;"></i>
                    </button>

                    {{-- Info --}}
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-white fw-semibold" style="font-size:.88rem;">
                                ConocIA Briefing — {{ $ep->formatted_date }}
                            </span>
                        </div>
                        {{-- Mini waveform + progress --}}
                        <div class="radio-waveform radio-waveform--sm mb-1" aria-hidden="true">
                            @for($i = 0; $i < 30; $i++)
                            <div class="radio-bar" style="animation-delay:{{ $i * 0.07 }}s;"></div>
                            @endfor
                        </div>
                        <div class="radio-progress-track">
                            <div class="radio-progress-fill"></div>
                            <div class="radio-progress-thumb"></div>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="text-end flex-shrink-0 d-none d-md-block" style="min-width:120px;">
                        <div style="color:#666;font-size:.75rem;">
                            <i class="fas fa-newspaper me-1"></i>{{ $ep->news_count }} noticias
                        </div>
                        <div style="color:#555;font-size:.72rem;">
                            <i class="fas fa-clock me-1"></i>{{ $ep->estimated_minutes }}
                        </div>
                    </div>

                    {{-- Time + link --}}
                    <div class="text-end flex-shrink-0" style="min-width:56px;">
                        <div>
                            <span class="radio-time-current" style="color:#888;font-size:.72rem;">0:00</span>
                            <span style="color:#444;font-size:.72rem;"> / </span>
                            <span class="radio-time-total" style="color:#666;font-size:.72rem;">
                                {{ floor($ep->duration_seconds/60) }}:{{ str_pad($ep->duration_seconds%60,2,'0',STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <a href="{{ route('radio.show', $ep->date->toDateString()) }}"
                           style="color:#555;font-size:.7rem;text-decoration:none;" class="mt-1 d-inline-block">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('styles')
<style>
/* ── ON AIR dot ── */
.radio-on-air-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: var(--primary-color);
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(56,182,255,.5);
    animation: radio-pulse 2s ease-in-out infinite;
    margin-right: 4px;
}
@keyframes radio-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(56,182,255,.5); }
    70%  { box-shadow: 0 0 0 7px rgba(56,182,255,0); }
    100% { box-shadow: 0 0 0 0 rgba(56,182,255,0); }
}

/* ── Artwork ── */
.radio-artwork { position: relative; width: 200px; height: 200px; }
.radio-artwork-inner {
    width: 200px; height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle at 40% 40%, #1a2540, #0a0f1e);
    border: 2px solid rgba(56,182,255,.2);
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden;
}
.radio-artwork-inner > i {
    font-size: 3.5rem;
    color: var(--primary-color);
    opacity: .85;
    position: relative; z-index: 2;
}
.radio-artwork-rings { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
.radio-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(56,182,255,.12);
    animation: radio-ring-expand 3s ease-out infinite;
}
.radio-ring-1 { width: 120px; height: 120px; animation-delay: 0s; }
.radio-ring-2 { width: 160px; height: 160px; animation-delay: 1s; }
.radio-ring-3 { width: 196px; height: 196px; animation-delay: 2s; }
@keyframes radio-ring-expand {
    0%   { opacity: .4; transform: scale(.85); }
    100% { opacity: 0;  transform: scale(1.1); }
}

/* ── Artwork waveform ── */
.radio-waveform-art {
    display: flex; align-items: center; justify-content: center;
    gap: 2px; height: 32px;
}
.radio-art-bar {
    width: 4px; border-radius: 2px;
    background: rgba(56,182,255,.3);
    animation: radio-art-wave 1.4s ease-in-out infinite alternate;
    min-height: 4px;
}
@keyframes radio-art-wave {
    0%   { height: 4px; }
    100% { height: 28px; background: var(--primary-color); }
}

/* ── Player ── */
.radio-player {
    background: rgba(255,255,255,.03);
    border: 1px solid #1e2540;
    border-radius: 12px;
    padding: 16px 20px;
    transition: border-color .2s;
}
.radio-player:hover { border-color: rgba(56,182,255,.25); }

.radio-play-btn {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: var(--primary-color);
    border: none; color: #fff;
    font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0;
    box-shadow: 0 0 16px rgba(56,182,255,.35);
    transition: transform .15s ease, box-shadow .15s ease;
}
.radio-play-btn:hover { transform: scale(1.08); box-shadow: 0 0 24px rgba(56,182,255,.55); }
.radio-play-btn:active { transform: scale(.96); }
.radio-play-btn.playing {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color-light));
}
.radio-play-btn--sm { width: 36px; height: 36px; font-size: .8rem; }

/* ── Waveform ── */
.radio-waveform {
    display: flex; align-items: center; gap: 2px; height: 28px; overflow: hidden;
}
.radio-waveform--sm { height: 18px; }
.radio-bar {
    flex: 1; border-radius: 2px;
    background: #2a3040; min-height: 3px;
    transition: background .3s ease; animation: none;
}
.radio-bar.active {
    background: var(--primary-color);
    animation: radio-wave 1.1s ease-in-out infinite alternate;
}
.radio-bar.played { background: rgba(56,182,255,.3); animation: none; height: 5px !important; }
@keyframes radio-wave {
    0%   { transform: scaleY(.25); }
    100% { transform: scaleY(1); }
}

/* ── Progress ── */
.radio-progress-track {
    height: 3px; background: #1e2030; border-radius: 3px;
    position: relative; cursor: pointer;
}
.radio-progress-fill {
    height: 100%; border-radius: 3px; width: 0%;
    background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
    transition: width .5s linear;
}
.radio-progress-thumb {
    position: absolute; top: 50%; left: 0%;
    transform: translate(-50%,-50%);
    width: 9px; height: 9px;
    background: #fff; border-radius: 50%;
    box-shadow: 0 0 6px rgba(56,182,255,.5);
    transition: left .5s linear; opacity: 0; pointer-events: none;
}
.radio-progress-track:hover .radio-progress-thumb { opacity: 1; }

/* ── Headline chips ── */
.radio-headline-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 20px;
    background: rgba(255,255,255,.04);
    border: 1px solid #2a2a4a;
    color: #aaa; font-size: .75rem;
    transition: background .15s;
}
.radio-headline-chip:hover { background: rgba(56,182,255,.1); color: #fff; border-color: rgba(56,182,255,.3); }
.radio-headline-dot {
    width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}

/* ── Episode row ── */
.radio-episode-row { cursor: default; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    // Track the currently active player
    let activePlayer = null;

    function fmtTime(s) {
        const m = Math.floor(s / 60);
        return m + ':' + String(Math.floor(s % 60)).padStart(2, '0');
    }

    function initPlayer(container) {
        const script   = container.dataset.script || '';
        const duration = parseInt(container.dataset.duration || '120', 10);
        const playBtn  = container.querySelector('.radio-play-btn');
        const playIcon = container.querySelector('.radio-play-icon');
        const bars     = Array.from(container.querySelectorAll('.radio-bar'));
        const fill     = container.querySelector('.radio-progress-fill');
        const thumb    = container.querySelector('.radio-progress-thumb');
        const timeCur  = container.querySelector('.radio-time-current');
        const timeEnd  = container.querySelector('.radio-time-total');
        const track    = container.querySelector('.radio-progress-track');

        let utterance = null;
        let isPlaying = false;
        let elapsed   = 0;
        let timer     = null;

        // Set idle bar heights
        const heights = [8,14,20,26,18,24,30,16,10,22,28,12,20,26,18];
        bars.forEach(function (b, i) {
            b.style.height = heights[i % heights.length] + 'px';
        });

        function updateProgress() {
            const pct = Math.min(elapsed / duration, 1);
            const pctStr = (pct * 100).toFixed(1) + '%';
            if (fill)  fill.style.width = pctStr;
            if (thumb) thumb.style.left = pctStr;
            if (timeCur) timeCur.textContent = fmtTime(elapsed);
            const played = Math.floor(pct * bars.length);
            bars.forEach(function (b, i) {
                b.classList.remove('active', 'played');
                b.style.height = '';
                if (i < played) {
                    b.classList.add('played');
                } else {
                    b.classList.add('active');
                    b.style.height = heights[i % heights.length] + 'px';
                }
            });
            if (pct >= 1) stop();
        }

        function stop() {
            window.speechSynthesis && window.speechSynthesis.cancel();
            isPlaying = false;
            elapsed   = 0;
            clearInterval(timer);
            playIcon.className = 'fas fa-play radio-play-icon';
            playBtn.classList.remove('playing');
            if (fill)  fill.style.width = '0%';
            if (thumb) thumb.style.left = '0%';
            if (timeCur) timeCur.textContent = '0:00';
            bars.forEach(function (b, i) {
                b.classList.remove('active', 'played');
                b.style.height = heights[i % heights.length] + 'px';
                b.style.background = '';
            });
            if (activePlayer === container) activePlayer = null;
        }

        function play() {
            if (!window.speechSynthesis) {
                alert('Tu navegador no soporta síntesis de voz. Usá Chrome o Edge.');
                return;
            }
            // Stop any other active player
            if (activePlayer && activePlayer !== container) {
                activePlayer.dispatchEvent(new CustomEvent('radio:stop'));
            }
            activePlayer = container;

            window.speechSynthesis.cancel();
            utterance = new SpeechSynthesisUtterance(script);
            utterance.lang  = 'es-AR';
            utterance.rate  = 0.95;
            utterance.pitch = 1.0;
            const voices = window.speechSynthesis.getVoices();
            const esVoice = voices.find(function (v) { return v.lang.startsWith('es'); });
            if (esVoice) utterance.voice = esVoice;

            utterance.onstart = function () {
                isPlaying = true;
                playIcon.className = 'fas fa-pause radio-play-icon';
                playBtn.classList.add('playing');
                timer = setInterval(function () { elapsed++; updateProgress(); }, 1000);
            };
            utterance.onend = utterance.onerror = stop;
            window.speechSynthesis.speak(utterance);
        }

        function pause() {
            window.speechSynthesis.pause();
            isPlaying = false;
            clearInterval(timer);
            playIcon.className = 'fas fa-play radio-play-icon';
            playBtn.classList.remove('playing');
        }

        function resume() {
            window.speechSynthesis.resume();
            isPlaying = true;
            playIcon.className = 'fas fa-pause radio-play-icon';
            playBtn.classList.add('playing');
            timer = setInterval(function () { elapsed++; updateProgress(); }, 1000);
        }

        playBtn.addEventListener('click', function () {
            if (!isPlaying) {
                elapsed > 0 ? resume() : play();
            } else {
                pause();
            }
        });

        if (track) {
            track.addEventListener('click', function (e) {
                const pct = (e.clientX - this.getBoundingClientRect().left) / this.offsetWidth;
                elapsed = Math.floor(pct * duration);
                updateProgress();
                if (isPlaying) { window.speechSynthesis.cancel(); elapsed = 0; play(); }
            });
        }

        container.addEventListener('radio:stop', stop);
        window.addEventListener('beforeunload', function () {
            window.speechSynthesis && window.speechSynthesis.cancel();
        });
    }

    // Init all players on the page
    document.querySelectorAll('.radio-player').forEach(initPlayer);

    // Load voices async (Chrome requires this)
    if (window.speechSynthesis) {
        window.speechSynthesis.getVoices();
        window.speechSynthesis.onvoiceschanged = function () { window.speechSynthesis.getVoices(); };
    }
})();
</script>
@endpush

@endsection
