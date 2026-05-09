@if($episode && $episode->isReady())
<div class="podcast-player card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center gap-3">
            <!-- Ícono podcast -->
            <div class="flex-shrink-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;background:rgba(255,255,255,0.1);">
                    <i class="fas fa-podcast text-white fs-5"></i>
                </div>
            </div>

            <!-- Info + controles -->
            <div class="flex-grow-1 min-w-0">
                <p class="text-white-50 small mb-1 text-uppercase fw-semibold" style="font-size:0.7rem;letter-spacing:.05em;">
                    <i class="fas fa-headphones me-1"></i> Escuchar artículo
                </p>

                <audio id="podcast-audio-{{ $episode->id }}"
                       src="{{ $episode->audio_url }}"
                       preload="none"
                       class="d-none"></audio>

                <!-- Barra de progreso -->
                <div class="d-flex align-items-center gap-2 mb-1">
                    <button id="podcast-btn-{{ $episode->id }}"
                            class="btn btn-sm p-0 border-0"
                            style="width:32px;height:32px;background:rgba(255,255,255,0.15);border-radius:50%;"
                            onclick="togglePodcast({{ $episode->id }})">
                        <i id="podcast-icon-{{ $episode->id }}" class="fas fa-play text-white" style="font-size:.8rem;"></i>
                    </button>

                    <div class="flex-grow-1 position-relative" style="height:4px;background:rgba(255,255,255,0.2);border-radius:2px;cursor:pointer;"
                         onclick="seekPodcast(event, {{ $episode->id }})">
                        <div id="podcast-progress-{{ $episode->id }}"
                             style="height:100%;width:0%;background:#e94560;border-radius:2px;transition:width .1s linear;"></div>
                    </div>

                    <span id="podcast-time-{{ $episode->id }}" class="text-white-50" style="font-size:.7rem;min-width:70px;text-align:right;">
                        0:00 / {{ $episode->getDurationFormatted() }}
                    </span>
                </div>
            </div>

            <!-- Link al RSS -->
            <div class="flex-shrink-0 d-none d-sm-block">
                <a href="{{ url('/podcast.rss') }}" target="_blank"
                   class="btn btn-sm border-0 text-white-50 p-1"
                   title="Feed RSS del podcast"
                   style="font-size:.75rem;">
                    <i class="fas fa-rss"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function togglePodcast(id) {
        const audio    = document.getElementById('podcast-audio-' + id);
        const icon     = document.getElementById('podcast-icon-' + id);
        if (!audio) return;

        if (audio.paused) {
            document.querySelectorAll('audio').forEach(a => { if (a !== audio) a.pause(); });
            audio.play();
            icon.className = 'fas fa-pause text-white';
            icon.style.fontSize = '.8rem';
        } else {
            audio.pause();
            icon.className = 'fas fa-play text-white';
            icon.style.fontSize = '.8rem';
        }
    }

    function seekPodcast(e, id) {
        const audio = document.getElementById('podcast-audio-' + id);
        const bar   = e.currentTarget;
        if (!audio || !audio.duration) return;
        const ratio = e.offsetX / bar.offsetWidth;
        audio.currentTime = ratio * audio.duration;
    }

    function fmtTime(s) {
        const m = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        return m + ':' + (sec < 10 ? '0' : '') + sec;
    }

    // Wire up each player on the page
    document.querySelectorAll('[id^="podcast-audio-"]').forEach(function (audio) {
        const id = audio.id.replace('podcast-audio-', '');

        audio.addEventListener('timeupdate', function () {
            const pct  = audio.duration ? (audio.currentTime / audio.duration) * 100 : 0;
            const bar  = document.getElementById('podcast-progress-' + id);
            const time = document.getElementById('podcast-time-' + id);
            if (bar)  bar.style.width = pct + '%';
            if (time) {
                const total = audio.duration ? fmtTime(audio.duration) : '--:--';
                time.textContent = fmtTime(audio.currentTime) + ' / ' + total;
            }
        });

        audio.addEventListener('ended', function () {
            const icon = document.getElementById('podcast-icon-' + id);
            if (icon) { icon.className = 'fas fa-play text-white'; icon.style.fontSize = '.8rem'; }
            const bar = document.getElementById('podcast-progress-' + id);
            if (bar) bar.style.width = '0%';
        });
    });

    window.togglePodcast = togglePodcast;
    window.seekPodcast   = seekPodcast;
})();
</script>
@endif
