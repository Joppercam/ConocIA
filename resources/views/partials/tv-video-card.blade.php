@php
    $platCode  = $video->platform->code ?? 'unknown';
    $platColor = match($platCode) { 'youtube' => '#ff0000', 'vimeo' => '#1ab7ea', default => '#38b6ff' };
    $platIcon  = match($platCode) { 'youtube' => 'fa-youtube', 'vimeo' => 'fa-vimeo-v', default => 'fa-play-circle' };
    $dur = $video->duration_seconds ?? 0;
    $durStr = ($dur >= 3600)
        ? floor($dur/3600).':'.str_pad(floor(($dur%3600)/60),2,'0',STR_PAD_LEFT).':'.str_pad($dur%60,2,'0',STR_PAD_LEFT)
        : floor($dur/60).':'.str_pad($dur%60,2,'0',STR_PAD_LEFT);
    $keywords = $video->ai_keywords ?? [];
@endphp

<a href="{{ route('videos.show', $video->routeParameters()) }}" class="tv-card" title="{{ $video->title }}">

    {{-- Thumbnail --}}
    <div class="tv-card-thumb">
        <img src="{{ $video->thumbnail_url }}"
             alt="{{ $video->title }}"
             loading="lazy"
             onerror="this.src='https://placehold.co/480x270/111/333?text=Video';">
        <div class="tv-card-play">
            <div class="tv-card-play-icon"><i class="fas fa-play ms-1"></i></div>
        </div>
        <div class="tv-card-platform" style="background:{{ $platColor }};">
            <i class="fab {{ $platIcon }}"></i>
        </div>
        @if($dur)
        <div class="tv-card-dur">{{ $durStr }}</div>
        @endif
    </div>

    {{-- Body --}}
    <div class="tv-card-body">
        @if($video->hasAiSummary())
        <div class="tv-card-ai-badge">
            <i class="fas fa-robot" style="font-size:.6rem;"></i> Resumen IA
        </div>
        @endif

        <div class="tv-card-title">{{ $video->title }}</div>

        <div class="tv-card-meta">
            <span><i class="far fa-eye me-1"></i>{{ number_format($video->view_count ?? 0) }}</span>
            <span>{{ $video->published_at?->locale('es')->diffForHumans() }}</span>
        </div>

        @if(count($keywords))
        <div class="tv-card-keywords">
            @foreach(array_slice($keywords, 0, 3) as $kw)
            <span class="tv-card-kw">{{ $kw }}</span>
            @endforeach
        </div>
        @endif
    </div>

</a>
