@extends('layouts.app')

@section('title', 'ConocIA Briefing — ' . $episode->formatted_date)
@section('meta_description', 'Resumen de inteligencia artificial del ' . $episode->formatted_date . '. ' . $episode->news_count . ' noticias cubiertas.')

@section('content')

{{-- ══ HEADER ══ --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #1e1e1e;" class="py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;background:var(--primary-color);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-microphone text-white" style="font-size:.95rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white" style="font-size:1.1rem;">
                        ConocIA <span style="color:var(--primary-color);">Radio</span>
                    </div>
                    <div style="color:#666;font-size:.7rem;margin-top:-2px;">Briefings diarios generados por IA</div>
                </div>
            </div>
            <nav style="font-size:.8rem;">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('radio.index') }}" class="text-primary text-decoration-none">ConocIA Radio</a></li>
                    <li class="breadcrumb-item active text-light">{{ $episode->date->isoFormat('D MMM YYYY') }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- ══ PLAYER PRINCIPAL ══ --}}
<section style="background:linear-gradient(135deg,#0a0f1e 0%,#0d1525 60%,#0a0f1e 100%);">
    <div class="container py-5">
        <div class="row g-5">

            {{-- Columna principal --}}
            <div class="col-lg-8">

                {{-- Título --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span style="background:var(--primary-color);color:#000;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;">EPISODIO</span>
                        <span style="color:#555;font-size:.8rem;">{{ $episode->date->toDateString() }}</span>
                    </div>
                    <h1 class="text-white fw-bold mb-1" style="font-size:1.8rem;">ConocIA Briefing</h1>
                    <p style="color:var(--primary-color);font-size:1rem;">{{ $episode->formatted_date }}</p>
                </div>

                {{-- Player expandido --}}
                <div class="radio-player mb-4"
                     id="main-player"
                     data-script="{{ e($episode->script) }}"
                     data-duration="{{ $episode->duration_seconds }}">

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <button class="radio-play-btn radio-play-btn--lg" title="Reproducir">
                            <i class="fas fa-play radio-play-icon"></i>
                        </button>

                        <div class="flex-grow-1">
                            <div class="radio-waveform mb-2" aria-hidden="true">
                                @for($i = 0; $i < 60; $i++)
                                <div class="radio-bar" style="animation-delay:{{ $i * 0.045 }}s;"></div>
                                @endfor
                            </div>
                            <div class="radio-progress-track">
                                <div class="radio-progress-fill"></div>
                                <div class="radio-progress-thumb"></div>
                            </div>
                        </div>

                        <div class="text-end flex-shrink-0" style="min-width:64px;">
                            <div>
                                <span class="radio-time-current" style="color:#888;font-size:.8rem;">0:00</span>
                                <span style="color:#444;font-size:.8rem;"> / </span>
                                <span class="radio-time-total" style="color:#666;font-size:.8rem;">
                                    {{ floor($episode->duration_seconds/60) }}:{{ str_pad($episode->duration_seconds%60,2,'0',STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3" style="border-top:1px solid #1e2540;padding-top:12px;">
                        <span style="color:#666;font-size:.8rem;">
                            <i class="fas fa-newspaper me-1" style="color:var(--primary-color);"></i>
                            {{ $episode->news_count }} noticias
                        </span>
                        <span style="color:#666;font-size:.8rem;">
                            <i class="fas fa-clock me-1" style="color:var(--primary-color);"></i>
                            {{ $episode->estimated_minutes }}
                        </span>
                        <span style="color:#666;font-size:.8rem;">
                            <i class="fas fa-robot me-1" style="color:var(--primary-color);"></i>
                            Generado por IA
                        </span>
                    </div>
                </div>

                {{-- Script completo --}}
                <div style="background:rgba(255,255,255,.02);border:1px solid #1e2540;border-radius:12px;padding:24px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h2 class="text-white fw-semibold mb-0" style="font-size:1rem;">Guión del episodio</h2>
                    </div>
                    <div style="color:#bbb;font-size:.92rem;line-height:1.85;white-space:pre-wrap;">{{ $episode->script }}</div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Noticias cubiertas --}}
                @if($episode->headlines)
                <div style="background:rgba(255,255,255,.02);border:1px solid #1e2540;border-radius:12px;padding:20px;" class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h3 class="text-white fw-semibold mb-0" style="font-size:.95rem;">Noticias cubiertas</h3>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @foreach($episode->headlines as $i => $h)
                        <a href="{{ $h['url'] }}" class="radio-headline-full text-decoration-none">
                            <div class="d-flex align-items-start gap-2">
                                <div style="width:6px;height:6px;border-radius:50%;background:{{ $h['color'] ?? '#38b6ff' }};flex-shrink:0;margin-top:6px;"></div>
                                <div>
                                    @if($h['category'] ?? null)
                                    <div style="color:{{ $h['color'] ?? 'var(--primary-color)' }};font-size:.68rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;" class="mb-1">
                                        {{ $h['category'] }}
                                    </div>
                                    @endif
                                    <div style="color:#ccc;font-size:.82rem;line-height:1.4;">{{ $h['title'] }}</div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Navegación entre episodios --}}
                <div style="background:rgba(255,255,255,.02);border:1px solid #1e2540;border-radius:12px;padding:20px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h3 class="text-white fw-semibold mb-0" style="font-size:.95rem;">Otros episodios</h3>
                    </div>

                    @if($next)
                    <a href="{{ route('radio.show', $next->date->toDateString()) }}" class="radio-nav-ep text-decoration-none d-flex align-items-center gap-3 mb-2">
                        <i class="fas fa-chevron-left" style="color:var(--primary-color);font-size:.8rem;"></i>
                        <div>
                            <div style="color:#555;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;">Más reciente</div>
                            <div style="color:#bbb;font-size:.82rem;">{{ $next->formatted_date }}</div>
                        </div>
                    </a>
                    @endif

                    @if($prev)
                    <a href="{{ route('radio.show', $prev->date->toDateString()) }}" class="radio-nav-ep text-decoration-none d-flex align-items-center gap-3">
                        <i class="fas fa-chevron-right" style="color:var(--primary-color);font-size:.8rem;"></i>
                        <div>
                            <div style="color:#555;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;">Anterior</div>
                            <div style="color:#bbb;font-size:.82rem;">{{ $prev->formatted_date }}</div>
                        </div>
                    </a>
                    @endif

                    <div class="mt-3 pt-3" style="border-top:1px solid #1e2540;">
                        <a href="{{ route('radio.index') }}" style="color:var(--primary-color);font-size:.82rem;text-decoration:none;">
                            <i class="fas fa-list me-1"></i>Ver todos los episodios
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
.radio-on-air-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--primary-color); display: inline-block;
    animation: radio-pulse 2s ease-in-out infinite; margin-right: 4px;
}
@keyframes radio-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(56,182,255,.5); }
    70%  { box-shadow: 0 0 0 7px rgba(56,182,255,0); }
    100% { box-shadow: 0 0 0 0 rgba(56,182,255,0); }
}
.radio-player {
    background: rgba(255,255,255,.03);
    border: 1px solid #1e2540; border-radius: 12px; padding: 20px;
}
.radio-play-btn {
    width: 52px; height: 52px; border-radius: 50%;
    background: var(--primary-color); border: none; color: #fff;
    font-size: 1rem; display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0;
    box-shadow: 0 0 16px rgba(56,182,255,.35);
    transition: transform .15s ease, box-shadow .15s ease;
}
.radio-play-btn--lg { width: 60px; height: 60px; font-size: 1.2rem; }
.radio-play-btn:hover { transform: scale(1.08); box-shadow: 0 0 24px rgba(56,182,255,.55); }
.radio-play-btn.playing { background: linear-gradient(135deg, var(--primary-color), var(--primary-color-light)); }
.radio-waveform { display: flex; align-items: center; gap: 2px; height: 32px; overflow: hidden; }
.radio-bar {
    flex: 1; border-radius: 2px; background: #2a3040; min-height: 3px;
    transition: background .3s ease; animation: none;
}
.radio-bar.active { background: var(--primary-color); animation: radio-wave 1.1s ease-in-out infinite alternate; }
.radio-bar.played { background: rgba(56,182,255,.3); animation: none; height: 5px !important; }
@keyframes radio-wave { 0% { transform: scaleY(.25); } 100% { transform: scaleY(1); } }
.radio-progress-track {
    height: 4px; background: #1e2030; border-radius: 4px; position: relative; cursor: pointer;
}
.radio-progress-fill {
    height: 100%; border-radius: 4px; width: 0%;
    background: linear-gradient(to right, var(--primary-color), var(--primary-color-light));
    transition: width .5s linear;
}
.radio-progress-thumb {
    position: absolute; top: 50%; left: 0%;
    transform: translate(-50%,-50%);
    width: 10px; height: 10px; background: #fff; border-radius: 50%;
    box-shadow: 0 0 6px rgba(56,182,255,.5);
    transition: left .5s linear; opacity: 0; pointer-events: none;
}
.radio-progress-track:hover .radio-progress-thumb { opacity: 1; }
.radio-headline-full {
    padding: 8px; border-radius: 8px; transition: background .15s;
}
.radio-headline-full:hover { background: rgba(255,255,255,.04); }
.radio-nav-ep {
    padding: 8px; border-radius: 8px; transition: background .15s;
}
.radio-nav-ep:hover { background: rgba(255,255,255,.04); }
</style>
@endpush

@push('scripts')
<script>
(function () {
    function fmtTime(s) {
        return Math.floor(s/60) + ':' + String(Math.floor(s%60)).padStart(2,'0');
    }

    const container = document.getElementById('main-player');
    if (!container) return;

    const script   = container.dataset.script || '';
    const duration = parseInt(container.dataset.duration || '120', 10);
    const playBtn  = container.querySelector('.radio-play-btn');
    const playIcon = container.querySelector('.radio-play-icon');
    const bars     = Array.from(container.querySelectorAll('.radio-bar'));
    const fill     = container.querySelector('.radio-progress-fill');
    const thumb    = container.querySelector('.radio-progress-thumb');
    const timeCur  = container.querySelector('.radio-time-current');
    const track    = container.querySelector('.radio-progress-track');

    const heights  = [10,16,24,30,20,28,34,18,12,26,32,20,14,28,22];
    bars.forEach(function (b, i) { b.style.height = heights[i % heights.length] + 'px'; });

    let utterance = null, isPlaying = false, elapsed = 0, timer = null;

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
            if (i < played) { b.classList.add('played'); }
            else { b.classList.add('active'); b.style.height = heights[i % heights.length] + 'px'; }
        });
        if (pct >= 1) stop();
    }

    function stop() {
        window.speechSynthesis && window.speechSynthesis.cancel();
        isPlaying = false; elapsed = 0; clearInterval(timer);
        playIcon.className = 'fas fa-play radio-play-icon';
        playBtn.classList.remove('playing');
        if (fill)  fill.style.width = '0%';
        if (thumb) thumb.style.left = '0%';
        if (timeCur) timeCur.textContent = '0:00';
        bars.forEach(function (b, i) {
            b.classList.remove('active','played');
            b.style.height = heights[i % heights.length] + 'px';
            b.style.background = '';
        });
    }

    function play() {
        if (!window.speechSynthesis) { alert('Tu navegador no soporta síntesis de voz.'); return; }
        window.speechSynthesis.cancel();
        utterance = new SpeechSynthesisUtterance(script);
        utterance.lang = 'es-AR'; utterance.rate = 0.95; utterance.pitch = 1.0;
        const esVoice = window.speechSynthesis.getVoices().find(function (v) { return v.lang.startsWith('es'); });
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

    playBtn.addEventListener('click', function () {
        if (!isPlaying) {
            elapsed > 0 ? (window.speechSynthesis.resume(), isPlaying = true,
                playIcon.className = 'fas fa-pause radio-play-icon', playBtn.classList.add('playing'),
                timer = setInterval(function () { elapsed++; updateProgress(); }, 1000)) : play();
        } else {
            window.speechSynthesis.pause();
            isPlaying = false; clearInterval(timer);
            playIcon.className = 'fas fa-play radio-play-icon';
            playBtn.classList.remove('playing');
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

    window.addEventListener('beforeunload', function () {
        window.speechSynthesis && window.speechSynthesis.cancel();
    });

    if (window.speechSynthesis) {
        window.speechSynthesis.getVoices();
        window.speechSynthesis.onvoiceschanged = function () { window.speechSynthesis.getVoices(); };
    }
})();
</script>
@endpush

@endsection
