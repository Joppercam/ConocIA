@if(isset($episode) && $episode && $episode->isReady())
<div class="podcast-player mb-4 rounded-3 p-3 p-md-4"
     style="background:linear-gradient(135deg,#1a1a2e,#0f3460);color:#fff;">
    <div class="d-flex align-items-center gap-2 mb-2">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#f59e0b">
            <path d="M12 1a11 11 0 1 0 0 22A11 11 0 0 0 12 1zm-1 6v10l7-5-7-5z"/>
        </svg>
        <span class="fw-semibold small" style="color:#f59e0b;letter-spacing:.04em;">ESCUCHAR ESTE ARTÍCULO</span>
    </div>

    <audio id="podcast-audio-{{ $episode->id }}" preload="none" style="display:none;">
        <source src="{{ $episode->audio_url }}" type="audio/mpeg">
    </audio>

    <div class="d-flex align-items-center gap-3">
        <button onclick="togglePodcast({{ $episode->id }})"
                id="podcast-btn-{{ $episode->id }}"
                class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                style="width:42px;height:42px;background:#f59e0b;border:none;color:#1a1a2e;">
            <svg id="podcast-icon-play-{{ $episode->id }}" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <svg id="podcast-icon-pause-{{ $episode->id }}" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
            </svg>
        </button>

        <div class="flex-grow-1">
            <div class="d-flex justify-content-between mb-1">
                <span id="podcast-current-{{ $episode->id }}" class="small" style="color:rgba(255,255,255,.7);">0:00</span>
                <span class="small" style="color:rgba(255,255,255,.5);">{{ $episode->duration_formatted }}</span>
            </div>
            <div class="progress" style="height:4px;background:rgba(255,255,255,.2);cursor:pointer;"
                 onclick="seekPodcast({{ $episode->id }}, event, this)">
                <div id="podcast-progress-{{ $episode->id }}"
                     class="progress-bar" role="progressbar"
                     style="width:0%;background:#f59e0b;transition:width .2s linear;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePodcast(id) {
    var audio = document.getElementById('podcast-audio-' + id);
    var iconPlay = document.getElementById('podcast-icon-play-' + id);
    var iconPause = document.getElementById('podcast-icon-pause-' + id);
    if (audio.paused) {
        audio.play();
        iconPlay.style.display = 'none';
        iconPause.style.display = '';
    } else {
        audio.pause();
        iconPlay.style.display = '';
        iconPause.style.display = 'none';
    }
}
function seekPodcast(id, e, bar) {
    var audio = document.getElementById('podcast-audio-' + id);
    if (!audio.duration) return;
    var rect = bar.getBoundingClientRect();
    audio.currentTime = ((e.clientX - rect.left) / rect.width) * audio.duration;
}
(function() {
    var id = {{ $episode->id }};
    var audio = document.getElementById('podcast-audio-' + id);
    audio.addEventListener('timeupdate', function() {
        if (!audio.duration) return;
        var pct = (audio.currentTime / audio.duration) * 100;
        document.getElementById('podcast-progress-' + id).style.width = pct + '%';
        var m = Math.floor(audio.currentTime / 60);
        var s = Math.floor(audio.currentTime % 60);
        document.getElementById('podcast-current-' + id).textContent = m + ':' + (s < 10 ? '0' : '') + s;
    });
    audio.addEventListener('ended', function() {
        document.getElementById('podcast-icon-play-' + id).style.display = '';
        document.getElementById('podcast-icon-pause-' + id).style.display = 'none';
    });
})();
</script>
@endif
