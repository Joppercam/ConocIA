{{--
    Daily AI Briefing Player
    Incluir en home.blade.php:
        @include('partials.daily-briefing')
--}}
<section class="briefing-section" style="background:linear-gradient(90deg,#0d1117 0%,#161b2e 50%,#0d1117 100%);border-bottom:1px solid #1e2540;">
    <div class="container">

        {{-- ── Single-row player ── --}}
        <div id="briefing-player" class="briefing-inline d-flex align-items-center gap-3 py-2">

            {{-- Live label --}}
            <div class="d-none d-sm-flex align-items-center gap-2 flex-shrink-0">
                <div class="briefing-live-dot"></div>
                <span class="text-uppercase fw-bold"
                      style="font-size:.6rem;letter-spacing:.18em;color:var(--primary-color);">
                    <i class="fas fa-robot me-1"></i>AI Briefing
                </span>
            </div>

            {{-- Divider --}}
            <div class="d-none d-sm-block flex-shrink-0" style="width:1px;height:20px;background:#2a2a4a;"></div>

            {{-- Loading --}}
            <div id="briefing-loading" class="d-flex align-items-center gap-2 flex-grow-1">
                <div class="briefing-spinner"></div>
                <span style="color:#666;font-size:.8rem;">Cargando...</span>
            </div>

            {{-- Unavailable --}}
            <div id="briefing-unavailable" class="d-none flex-grow-1">
                <span style="color:#555;font-size:.8rem;">
                    <i class="fas fa-microphone-slash me-1"></i>Sin briefing disponible hoy
                </span>
            </div>

            {{-- Ready --}}
            <div id="briefing-ready" class="d-none d-flex align-items-center gap-3 flex-grow-1 min-w-0">

                {{-- Play button --}}
                <button id="briefing-play-btn" class="briefing-play-btn flex-shrink-0"
                        title="Reproducir" aria-label="Reproducir">
                    <i class="fas fa-play" id="briefing-play-icon"></i>
                </button>

                {{-- Title + date --}}
                <div class="flex-shrink-0 d-none d-md-block">
                    <div class="fw-semibold text-white text-nowrap" style="font-size:.82rem;">ConocIA Briefing</div>
                    <div style="color:#666;font-size:.7rem;" id="briefing-date-label"></div>
                </div>

                {{-- Waveform + progress --}}
                <div class="flex-grow-1 min-w-0 d-flex flex-column gap-1">
                    <div class="briefing-waveform" id="briefing-waveform" aria-hidden="true">
                        @for($i = 0; $i < 40; $i++)
                        <div class="briefing-bar" style="animation-delay:{{ $i * 0.065 }}s;"></div>
                        @endfor
                    </div>
                    <div class="briefing-progress-track" id="briefing-progress-track">
                        <div class="briefing-progress-fill" id="briefing-progress-fill"></div>
                        <div class="briefing-progress-thumb" id="briefing-progress-thumb"></div>
                    </div>
                </div>

                {{-- Time --}}
                <div class="flex-shrink-0 text-end d-none d-sm-block" style="min-width:52px;">
                    <span style="color:#888;font-size:.72rem;" id="briefing-time-current">0:00</span>
                    <span style="color:#444;font-size:.72rem;"> / </span>
                    <span style="color:#666;font-size:.72rem;" id="briefing-time-total"></span>
                </div>

                {{-- Headlines toggle --}}
                <button id="briefing-headlines-btn"
                        class="btn btn-sm rounded-pill flex-shrink-0"
                        style="background:rgba(56,182,255,.1);color:var(--primary-color);border:1px solid rgba(56,182,255,.2);font-size:.72rem;padding:3px 10px;">
                    <i class="fas fa-list-ul me-1"></i><span id="briefing-news-count"></span>
                </button>
            </div>

        </div>

        {{-- Headlines dropdown --}}
        <div id="briefing-headlines" style="display:none;">
            <div style="border-top:1px solid #1e2540;padding:.6rem 0 .75rem;">
                <div id="briefing-headlines-list" class="d-flex flex-wrap gap-2"></div>
            </div>
        </div>

        {{-- Audio element oculto (usado cuando hay audio_url real) --}}
        <audio id="briefing-audio" preload="none" style="display:none;"></audio>

    </div>
</section>

@push('styles')
<style>
.briefing-inline { min-height: 48px; }

.briefing-live-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--primary-color);
    box-shadow: 0 0 0 0 rgba(56,182,255,.5);
    animation: briefing-pulse 2s ease-in-out infinite;
}
@keyframes briefing-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(56,182,255,.5); }
    70%  { box-shadow: 0 0 0 7px rgba(56,182,255,0); }
    100% { box-shadow: 0 0 0 0 rgba(56,182,255,0); }
}

.briefing-spinner {
    width: 18px; height: 18px;
    border: 2px solid #333; border-top-color: var(--primary-color);
    border-radius: 50%; animation: briefing-spin .7s linear infinite; flex-shrink: 0;
}
@keyframes briefing-spin { to { transform: rotate(360deg); } }

.briefing-play-btn {
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--primary-color); border: none; color: #fff;
    font-size: .95rem; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: transform .15s ease, box-shadow .15s ease;
    box-shadow: 0 0 16px rgba(56,182,255,.4); flex-shrink: 0;
}
.briefing-play-btn:hover { transform: scale(1.08); box-shadow: 0 0 24px rgba(56,182,255,.6); }
.briefing-play-btn:active { transform: scale(.96); }
.briefing-play-btn.playing { background: linear-gradient(135deg, var(--primary-color), var(--primary-color-light)); }

.briefing-waveform { display: flex; align-items: center; gap: 2px; height: 26px; overflow: hidden; }
.briefing-bar { flex: 1; border-radius: 2px; background: #2a3040; min-height: 3px; transition: background .3s ease; animation: none; }
.briefing-bar.active { background: var(--primary-color); animation: briefing-wave 1.1s ease-in-out infinite alternate; }
.briefing-bar.played { background: rgba(56,182,255,.35); animation: none; height: 6px !important; }
@keyframes briefing-wave { 0% { transform: scaleY(.3); } 100% { transform: scaleY(1); } }

.briefing-progress-track { height: 4px; background: #1e2030; border-radius: 4px; position: relative; cursor: pointer; }
.briefing-progress-fill { height: 100%; border-radius: 4px; background: linear-gradient(to right, var(--primary-color), var(--primary-color-light)); width: 0%; transition: width .5s linear; }
.briefing-progress-thumb { position: absolute; top: 50%; left: 0%; transform: translate(-50%,-50%); width: 10px; height: 10px; background: #fff; border-radius: 50%; box-shadow: 0 0 6px rgba(56,182,255,.6); transition: left .5s linear; opacity: 0; pointer-events: none; }
.briefing-progress-track:hover .briefing-progress-thumb { opacity: 1; }

.briefing-headline-item { display: flex; align-items: flex-start; gap: 8px; text-decoration: none; padding: 6px 8px; border-radius: 6px; transition: background .15s ease; }
.briefing-headline-item:hover { background: rgba(255,255,255,.04); }
.briefing-headline-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const BRIEFING_URL = '{{ route("briefing.today") }}';
    const BAR_COUNT    = 40;

    const loading       = document.getElementById('briefing-loading');
    const unavailable   = document.getElementById('briefing-unavailable');
    const ready         = document.getElementById('briefing-ready');
    const playBtn       = document.getElementById('briefing-play-btn');
    const playIcon      = document.getElementById('briefing-play-icon');
    const waveform      = document.getElementById('briefing-waveform');
    const bars          = waveform ? Array.from(waveform.querySelectorAll('.briefing-bar')) : [];
    const progressFill  = document.getElementById('briefing-progress-fill');
    const progressThumb = document.getElementById('briefing-progress-thumb');
    const timeCurrent   = document.getElementById('briefing-time-current');
    const timeTotal     = document.getElementById('briefing-time-total');
    const dateLabel     = document.getElementById('briefing-date-label');
    const headlinesBtn  = document.getElementById('briefing-headlines-btn');
    const headlinesList = document.getElementById('briefing-headlines-list');
    const headlinesEl   = document.getElementById('briefing-headlines');
    const newsCount     = document.getElementById('briefing-news-count');
    const audioEl       = document.getElementById('briefing-audio');

    let totalSeconds      = 120;
    let headlinesVisible  = false;

    // ── Modo: 'native' (audio_url real) o 'tts' (Web Speech API fallback) ──
    let mode = 'tts';

    function show(el) { el && el.classList.remove('d-none'); }
    function hide(el) { el && el.classList.add('d-none'); }
    function fmtTime(s) { return Math.floor(s/60) + ':' + String(Math.floor(s%60)).padStart(2,'0'); }
    function escHtml(s) { const d = document.createElement('div'); d.textContent = s||''; return d.innerHTML; }

    // ── Waveform ──────────────────────────────────────────────────────────────
    function setWaveformIdle() {
        bars.forEach(function(bar) { bar.classList.remove('active','played'); bar.style.height=''; });
    }
    function setWaveformPlaying(pct) {
        const played = Math.floor(pct * BAR_COUNT);
        const heights = [18,24,30,26,20,32,28,22,16,28,32,24,18,26,30];
        bars.forEach(function(bar, i) {
            bar.classList.remove('active','played');
            if (i < played) { bar.classList.add('played'); bar.style.height=''; }
            else { bar.classList.add('active'); bar.style.height = heights[i % heights.length] + 'px'; }
        });
    }

    // ── Progress bar ──────────────────────────────────────────────────────────
    function updateProgressUI(elapsed, total) {
        const pct = total > 0 ? Math.min(elapsed / total, 1) : 0;
        const pctStr = (pct * 100).toFixed(1) + '%';
        if (progressFill)  progressFill.style.width = pctStr;
        if (progressThumb) progressThumb.style.left  = pctStr;
        if (timeCurrent)   timeCurrent.textContent   = fmtTime(elapsed);
        setWaveformPlaying(pct);
    }

    // ════════════════════════════════════════════════════════════════════════
    // MODO NATIVE — usa <audio> con el MP3 de R2
    // ════════════════════════════════════════════════════════════════════════
    function initNativeMode(audioUrl) {
        mode = 'native';
        audioEl.src = audioUrl;
        audioEl.preload = 'metadata';

        audioEl.addEventListener('loadedmetadata', function() {
            totalSeconds = Math.ceil(audioEl.duration) || totalSeconds;
            if (timeTotal) timeTotal.textContent = fmtTime(totalSeconds);
        });

        audioEl.addEventListener('timeupdate', function() {
            updateProgressUI(audioEl.currentTime, audioEl.duration || totalSeconds);
        });

        audioEl.addEventListener('ended', function() {
            playIcon.className = 'fas fa-play';
            playBtn.classList.remove('playing');
            setWaveformIdle();
            if (progressFill)  progressFill.style.width = '0%';
            if (progressThumb) progressThumb.style.left  = '0%';
            if (timeCurrent)   timeCurrent.textContent   = '0:00';
            audioEl.currentTime = 0;
        });

        // Seek al hacer clic en la barra de progreso
        const track = document.getElementById('briefing-progress-track');
        if (track) {
            track.addEventListener('click', function(e) {
                if (!audioEl.duration) return;
                const pct = (e.clientX - track.getBoundingClientRect().left) / track.offsetWidth;
                audioEl.currentTime = pct * audioEl.duration;
            });
        }
    }

    function nativePlay() {
        audioEl.play().catch(function(e) { console.warn('Briefing audio play error:', e); });
        playIcon.className = 'fas fa-pause';
        playBtn.classList.add('playing');
    }

    function nativePause() {
        audioEl.pause();
        playIcon.className = 'fas fa-play';
        playBtn.classList.remove('playing');
        setWaveformIdle();
    }

    // ════════════════════════════════════════════════════════════════════════
    // MODO TTS — fallback Web Speech API (igual que antes)
    // ════════════════════════════════════════════════════════════════════════
    let briefingScript = '', scriptChunks = [], currentChunkIndex = 0;
    let isPlayingTts   = false, progressTimer = null, elapsedSeconds = 0;
    let spanishVoice = null, chunkTimeout = null, keepAliveTimer = null;

    function resolveVoice() {
        var voices = window.speechSynthesis.getVoices();
        spanishVoice = voices.find(v => v.lang==='es-MX') || voices.find(v => v.lang==='es-AR')
            || voices.find(v => v.lang==='es-ES') || voices.find(v => v.lang.startsWith('es')) || null;
    }
    if (window.speechSynthesis && window.speechSynthesis.onvoiceschanged !== undefined) {
        window.speechSynthesis.onvoiceschanged = resolveVoice;
    }

    function buildChunks(text) {
        var sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
        var chunks = [], current = '';
        sentences.forEach(function(s) {
            s = s.trim(); if (!s) return;
            if (s.length > 120) {
                var parts = s.split(/,\s*/), cur = '';
                parts.forEach(function(p) {
                    if ((cur + p).length > 120) { if (cur) chunks.push(cur.trim()); cur = p; }
                    else { cur += (cur ? ', ' : '') + p; }
                });
                if (cur.trim()) chunks.push(cur.trim());
            } else { chunks.push(s); }
        });
        return chunks.filter(c => c.length > 0);
    }

    function startKeepAlive() {
        stopKeepAlive();
        keepAliveTimer = setInterval(function() {
            if (window.speechSynthesis && window.speechSynthesis.speaking) {
                window.speechSynthesis.pause(); window.speechSynthesis.resume();
            }
        }, 10000);
    }
    function stopKeepAlive() { clearInterval(keepAliveTimer); keepAliveTimer = null; }

    function speakChunk(index) {
        if (index >= scriptChunks.length || !isPlayingTts) { if (index >= scriptChunks.length) ttsStop(); return; }
        currentChunkIndex = index;
        clearTimeout(chunkTimeout);
        var chunkText = scriptChunks[index];
        chunkTimeout = setTimeout(function() { if (isPlayingTts) speakChunk(index+1); }, Math.max(3000, (chunkText.length/15)*1000)+2000);
        var u = new SpeechSynthesisUtterance(chunkText);
        u.lang = 'es-MX'; u.rate = 0.95; u.pitch = 1.0;
        if (spanishVoice) u.voice = spanishVoice;
        u.onend = function() { clearTimeout(chunkTimeout); if (isPlayingTts) speakChunk(index+1); };
        u.onerror = function(e) { clearTimeout(chunkTimeout); if (isPlayingTts && e.error !== 'interrupted') speakChunk(index+1); };
        window.speechSynthesis.speak(u);
    }

    function ttsPlay() {
        window.speechSynthesis.cancel(); resolveVoice();
        scriptChunks = buildChunks(briefingScript); currentChunkIndex = 0;
        isPlayingTts = true; elapsedSeconds = 0;
        playIcon.className = 'fas fa-pause'; playBtn.classList.add('playing');
        startKeepAlive(); setWaveformPlaying(0);
        progressTimer = setInterval(function() {
            elapsedSeconds++;
            updateProgressUI(elapsedSeconds, totalSeconds);
            if (elapsedSeconds >= totalSeconds) ttsStop();
        }, 1000);
        speakChunk(0);
    }

    function ttsPause() {
        clearTimeout(chunkTimeout); window.speechSynthesis.cancel();
        isPlayingTts = false; clearInterval(progressTimer); stopKeepAlive();
        playIcon.className = 'fas fa-play'; playBtn.classList.remove('playing'); setWaveformIdle();
    }

    function ttsResume() {
        isPlayingTts = true; playIcon.className = 'fas fa-pause'; playBtn.classList.add('playing');
        startKeepAlive(); setWaveformPlaying(elapsedSeconds / totalSeconds);
        progressTimer = setInterval(function() {
            elapsedSeconds++;
            updateProgressUI(elapsedSeconds, totalSeconds);
            if (elapsedSeconds >= totalSeconds) ttsStop();
        }, 1000);
        speakChunk(currentChunkIndex);
    }

    function ttsStop() {
        window.speechSynthesis.cancel(); isPlayingTts = false; elapsedSeconds = 0; currentChunkIndex = 0;
        clearInterval(progressTimer); stopKeepAlive();
        playIcon.className = 'fas fa-play'; playBtn.classList.remove('playing'); setWaveformIdle();
        if (progressFill)  progressFill.style.width = '0%';
        if (progressThumb) progressThumb.style.left  = '0%';
        if (timeCurrent)   timeCurrent.textContent   = '0:00';
    }

    // ════════════════════════════════════════════════════════════════════════
    // Play button handler (unifica ambos modos)
    // ════════════════════════════════════════════════════════════════════════
    if (playBtn) {
        playBtn.addEventListener('click', function() {
            if (mode === 'native') {
                if (audioEl.paused) { nativePlay(); }
                else { nativePause(); }
            } else {
                if (!window.speechSynthesis) { alert('Tu navegador no soporta síntesis de voz. Prueba con Chrome o Edge.'); return; }
                if (!isPlayingTts) {
                    if (elapsedSeconds > 0 && elapsedSeconds < totalSeconds) ttsResume();
                    else { elapsedSeconds = 0; ttsPlay(); }
                } else { ttsPause(); }
            }
        });
    }

    // Headlines toggle
    if (headlinesBtn) {
        headlinesBtn.addEventListener('click', function() {
            headlinesVisible = !headlinesVisible;
            headlinesEl.style.display = headlinesVisible ? 'block' : 'none';
            headlinesBtn.querySelector('i').className = headlinesVisible ? 'fas fa-chevron-up me-1' : 'fas fa-list-ul me-1';
        });
    }

    // ════════════════════════════════════════════════════════════════════════
    // Fetch briefing al cargar la página
    // ════════════════════════════════════════════════════════════════════════
    const TYPE_ICONS = {
        paper:    { icon: 'fas fa-file-alt',  color: '#a78bfa' },
        concepto: { icon: 'fas fa-book-open', color: '#00c896' },
        news:     { icon: null,               color: null },
    };

    function renderHeadlines(headlines) {
        if (!headlinesList || !headlines) return;
        headlinesList.innerHTML = '';
        headlines.forEach(function(h) {
            const ti = TYPE_ICONS[h.type] || TYPE_ICONS.news;
            const dotColor = ti.color || h.color || '#38b6ff';
            const iconHtml = ti.icon
                ? `<i class="${escHtml(ti.icon)}" style="color:${escHtml(dotColor)};font-size:.65rem;flex-shrink:0;"></i>`
                : `<div style="width:6px;height:6px;border-radius:50%;background:${escHtml(dotColor)};flex-shrink:0;"></div>`;
            const item = document.createElement('a');
            item.href = h.url || '#';
            item.className = 'briefing-headline-chip text-decoration-none';
            item.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;background:rgba(255,255,255,.04);border:1px solid #2a2a4a;transition:background .15s;';
            item.innerHTML = `${iconHtml}<span style="color:#bbb;font-size:.75rem;line-height:1.3;">${escHtml(h.title.length>55?h.title.slice(0,55)+'…':h.title)}</span>`;
            item.addEventListener('mouseenter', function(){ this.style.background='rgba(255,255,255,.08)'; });
            item.addEventListener('mouseleave', function(){ this.style.background='rgba(255,255,255,.04)'; });
            headlinesList.appendChild(item);
        });
    }

    fetch(BRIEFING_URL)
        .then(r => r.json())
        .then(function(data) {
            hide(loading);
            if (!data.available) { show(unavailable); return; }

            totalSeconds  = data.duration_seconds || 120;
            briefingScript = data.script || '';

            if (dateLabel) {
                dateLabel.innerHTML = data.is_today
                    ? (data.date_label || '')
                    : '<span style="color:#f59e0b;font-size:.65rem;margin-right:4px;">● Último disponible</span>' + (data.date_label || '');
            }
            if (timeTotal) timeTotal.textContent = fmtTime(totalSeconds);
            if (newsCount) newsCount.textContent = (data.news_count || 0) + ' contenidos';

            renderHeadlines(data.headlines || []);

            // Idle bar heights
            bars.forEach(function(bar) {
                const h = [8,12,18,24,14,20,28,16,10,22,30,18,12,26,20];
                bar.style.height    = h[Math.floor(Math.random()*h.length)] + 'px';
                bar.style.background = '#2a3040';
            });

            // Seleccionar modo de reproducción
            if (data.audio_url) {
                initNativeMode(data.audio_url);
            }
            // Si no hay audio_url, el modo queda en 'tts' (Web Speech API)

            show(ready);
        })
        .catch(function() { hide(loading); show(unavailable); });

    window.addEventListener('beforeunload', function() {
        if (mode === 'native') { audioEl.pause(); }
        else if (window.speechSynthesis) { window.speechSynthesis.cancel(); }
    });
})();
</script>
@endpush
