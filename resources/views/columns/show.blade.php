@extends('layouts.app')

@section('reading_progress', true)

@php use Illuminate\Support\Str; @endphp

@section('title', $column->title . ' - ConocIA')

@php
    $colMetaDesc      = $column->excerpt ?? Str::limit(strip_tags($column->content ?? ''), 160);
    $colMetaUrl       = route('columns.show', $column->slug);
    $colMetaPublished = ($column->published_at ?? $column->created_at)?->toIso8601String();
    $colMetaModified  = $column->updated_at?->toIso8601String();
    $colMetaAuthor    = $column->author?->name ?? 'ConocIA';
    $colMetaKeywords  = 'columnas, opinión, inteligencia artificial' . ($column->category ? ', ' . $column->category->name : '');
@endphp

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle'       => $column->title . ' - ConocIA',
        'metaDescription' => $colMetaDesc,
        'metaKeywords'    => $colMetaKeywords,
        'metaImage'       => asset('images/defaults/social-share.jpg'),
        'metaType'        => 'article',
        'metaUrl'         => $colMetaUrl,
        'metaAuthor'      => $colMetaAuthor,
        'metaPublished'   => $colMetaPublished,
        'metaModified'    => $colMetaModified,
    ])
    @include('partials.schema-article', [
        'item'      => $column,
        'routeName' => 'columns.show',
        'type'      => 'Article',
        'section'   => 'Columnas de Opinión',
    ])
    @php
        $breadcrumbs = [
            ['name' => 'Inicio',   'url' => url('/')],
            ['name' => 'Columnas', 'url' => route('columns.index')],
        ];
        if ($column->category) {
            $breadcrumbs[] = ['name' => $column->category->name, 'url' => route('columns.category', $column->category->slug)];
        }
        $breadcrumbs[] = ['name' => $column->title];
    @endphp
    @include('partials.schema-breadcrumb', ['crumbs' => $breadcrumbs])
@endsection

@section('content')

{{-- Dark header --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('columns.index') }}" class="text-secondary text-decoration-none">Columnas</a></li>
                @if($column->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('columns.category', $column->category->slug) }}"
                       class="text-decoration-none"
                       style="color:{{ $column->category->color ?? 'var(--primary-color)' }};">
                        {{ $column->category->name }}
                    </a>
                </li>
                @endif
                <li class="breadcrumb-item active text-light" aria-current="page">{{ Str::limit($column->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

{{-- Article hero --}}
<div style="background:#0d0d0d;border-bottom:1px solid #1e1e1e;" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($column->category)
                <a href="{{ route('columns.category', $column->category->slug) }}"
                   class="badge text-decoration-none mb-3 d-inline-block"
                   style="background:{{ $column->category->color ?? 'var(--primary-color)' }}1a;color:{{ $column->category->color ?? 'var(--primary-color)' }};border:1px solid {{ $column->category->color ?? 'var(--primary-color)' }}55;font-size:.72rem;letter-spacing:.05em;">
                    {{ $column->category->name }}
                </a>
                @endif

                <h1 class="text-white fw-bold mb-4" style="font-size:2rem;line-height:1.22;">{{ $column->title }}</h1>

                @if($column->excerpt)
                <p style="color:#aaa;font-size:1.05rem;line-height:1.7;border-left:3px solid var(--primary-color);padding-left:1rem;margin-bottom:1.5rem;">
                    {{ $column->excerpt }}
                </p>
                @endif

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <img src="{{ asset($column->author->avatar ?? 'images/defaults/user-profile.jpg') }}"
                         class="rounded-circle flex-shrink-0"
                         width="48" height="48"
                         alt="{{ $column->author->name }}"
                         onerror="this.onerror=null; this.src='{{ asset('images/defaults/user-profile.jpg') }}';">
                    <div>
                        <a href="{{ route('columns.author', $column->author->id) }}" class="text-white fw-semibold text-decoration-none" style="font-size:.9rem;">
                            {{ $column->author->name }}
                        </a>
                        <div style="color:#666;font-size:.8rem;margin-top:.1rem;">
                            {{ $column->published_at?->locale('es')->isoFormat('D MMMM, YYYY') }}
                            @if($column->reading_time)
                            &middot; <i class="far fa-clock me-1"></i>{{ $column->reading_time }} min de lectura
                            @endif
                            &middot; <i class="fas fa-eye me-1"></i>{{ number_format($column->views ?? 0) }} lecturas
                        </div>
                    </div>
                    {{-- Inline share (desktop) --}}
                    <div class="ms-auto d-none d-md-flex align-items-center gap-2">
                        <span style="color:#555;font-size:.75rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;">Compartir</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('columns.show', $column->slug)) }}&text={{ urlencode($column->title) }}"
                           class="col-share-btn" target="_blank" rel="noopener" title="Twitter/X">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('columns.show', $column->slug)) }}&title={{ urlencode($column->title) }}"
                           class="col-share-btn" target="_blank" rel="noopener" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($column->title . ' ' . route('columns.show', $column->slug)) }}"
                           class="col-share-btn" target="_blank" rel="noopener" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button onclick="copyLink('{{ route('columns.show', $column->slug) }}')"
                                class="col-share-btn copy-link-btn" title="Copiar enlace" id="copyBtn">
                            <i class="fas fa-link" id="copyIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main content --}}
<div style="background:#0a0a0a;" class="py-5">
    <div class="container">
        <div class="row justify-content-center g-5">

            {{-- Article --}}
            <div class="col-lg-8">

                {{-- Audio player --}}
                @if($column->audio_path)
                <div class="col-audio-player mb-4" id="colAudioPlayer">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div style="width:3px;height:16px;background:var(--primary-color);border-radius:2px;"></div>
                        <span style="font-size:.75rem;font-weight:700;letter-spacing:.08em;color:var(--primary-color);text-transform:uppercase;">Escuchar columna</span>
                    </div>
                    <div class="col-audio-wrap d-flex align-items-center gap-3">
                        <button id="colAudioPlayBtn" onclick="toggleColAudio()" class="col-audio-play-btn" aria-label="Reproducir">
                            <i class="fas fa-play" id="colAudioPlayIcon"></i>
                        </button>
                        <div class="flex-grow-1">
                            <div class="col-audio-progress-wrap" onclick="seekColAudio(event)" id="colAudioProgressWrap">
                                <div class="col-audio-progress-bar" id="colAudioProgressBar"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="col-audio-time" id="colAudioCurrent">0:00</span>
                                <span class="col-audio-time" id="colAudioDuration">--:--</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button onclick="changeColSpeed()" class="col-audio-speed-btn" id="colAudioSpeedBtn" title="Velocidad">1×</button>
                        </div>
                    </div>
                    <audio id="colAudio" preload="none">
                        <source src="{{ $column->audio_path }}" type="audio/mpeg">
                    </audio>
                </div>
                @endif

                {{-- Content --}}
                <div class="col-article-content mb-5">
                    {!! $column->content !!}
                </div>

                {{-- Mobile share --}}
                <div class="mb-5 d-block d-md-none">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:3px;height:16px;background:var(--primary-color);border-radius:2px;"></div>
                        <span class="text-white fw-semibold" style="font-size:.85rem;">Compartir</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('columns.show', $column->slug)) }}&text={{ urlencode($column->title) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" rel="noopener" style="font-size:.78rem;">
                            <i class="fab fa-twitter me-1"></i>Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('columns.show', $column->slug)) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" rel="noopener" style="font-size:.78rem;">
                            <i class="fab fa-facebook-f me-1"></i>Facebook
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('columns.show', $column->slug)) }}&title={{ urlencode($column->title) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" rel="noopener" style="font-size:.78rem;">
                            <i class="fab fa-linkedin-in me-1"></i>LinkedIn
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($column->title . ' ' . route('columns.show', $column->slug)) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" rel="noopener" style="font-size:.78rem;">
                            <i class="fab fa-whatsapp me-1"></i>WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(route('columns.show', $column->slug)) }}&text={{ urlencode($column->title) }}"
                           class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" rel="noopener" style="font-size:.78rem;">
                            <i class="fab fa-telegram-plane me-1"></i>Telegram
                        </a>
                    </div>
                </div>

                {{-- Author bio card --}}
                <div class="col-author-card d-flex gap-4 align-items-start mb-5">
                    <img src="{{ asset($column->author->avatar ?? 'images/defaults/user-profile.jpg') }}"
                         class="rounded-circle flex-shrink-0"
                         width="72" height="72"
                         alt="{{ $column->author->name }}"
                         onerror="this.onerror=null; this.src='{{ asset('images/defaults/user-profile.jpg') }}';"
                         style="object-fit:cover;border:2px solid #2a2a2a;">
                    <div>
                        <div style="font-size:.7rem;font-weight:700;letter-spacing:.1em;color:var(--primary-color);text-transform:uppercase;margin-bottom:.3rem;">Sobre el autor</div>
                        <a href="{{ route('columns.author', $column->author->id) }}" class="text-white fw-bold text-decoration-none" style="font-size:1rem;">
                            {{ $column->author->name }}
                        </a>
                        <p style="color:#888;font-size:.85rem;line-height:1.6;margin-top:.4rem;margin-bottom:.75rem;">
                            {{ $column->author->bio ?? 'Columnista de ConocIA, especializado en inteligencia artificial y nuevas tecnologías.' }}
                        </p>
                        <a href="{{ route('columns.author', $column->author->id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           style="font-size:.78rem;">
                            Ver más columnas
                        </a>
                    </div>
                </div>

                {{-- Comments --}}
                @include('components.comments', [
                    'comments' => $column->comments ?? [],
                    'commentableType' => 'App\\Models\\Column',
                    'commentableId' => $column->id
                ])

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 d-none d-lg-block">
                <div style="position:sticky;top:80px;">

                    @include('partials.table-of-contents', ['contentSelector' => '.col-article-content'])

                    {{-- More from author --}}
                    @if($authorColumns->count())
                    <div class="col-sidebar-block mb-4">
                        <div class="col-sidebar-head">
                            <div style="width:3px;height:16px;background:var(--primary-color);border-radius:2px;"></div>
                            <span class="text-white fw-semibold" style="font-size:.85rem;">Más de {{ $column->author->name }}</span>
                        </div>
                        @foreach($authorColumns as $ac)
                        <a href="{{ route('columns.show', $ac->slug) }}" class="col-sidebar-item text-decoration-none d-block {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="text-white" style="font-size:.82rem;line-height:1.35;margin-bottom:.3rem;">{{ $ac->title }}</div>
                            <div style="color:#555;font-size:.73rem;">
                                {{ $ac->published_at?->locale('es')->isoFormat('D MMM, YYYY') }}
                                @if($ac->reading_time) · {{ $ac->reading_time }} min @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif

                    {{-- Related columns --}}
                    @if($relatedColumns->count())
                    <div class="col-sidebar-block">
                        <div class="col-sidebar-head">
                            <div style="width:3px;height:16px;background:var(--primary-color);border-radius:2px;"></div>
                            <span class="text-white fw-semibold" style="font-size:.85rem;">Columnas relacionadas</span>
                        </div>
                        @foreach($relatedColumns as $rc)
                        <a href="{{ route('columns.show', $rc->slug) }}" class="col-sidebar-item text-decoration-none d-block {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="text-white" style="font-size:.82rem;line-height:1.35;margin-bottom:.3rem;">{{ $rc->title }}</div>
                            <div style="color:#555;font-size:.73rem;">{{ $rc->author->name ?? '' }} · {{ $rc->published_at?->locale('es')->isoFormat('D MMM, YYYY') }}</div>
                        </a>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
/* Article content */
.col-article-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #ccc;
}
.col-article-content p { margin-bottom: 1.5rem; }
.col-article-content h2 {
    font-size: 1.3rem; font-weight: 700;
    margin-top: 2.5rem; margin-bottom: 1rem;
    color: #fff;
    padding-bottom: .5rem;
    border-bottom: 1px solid #1e1e1e;
}
.col-article-content h3 {
    font-size: 1.1rem; font-weight: 600;
    margin-top: 2rem; margin-bottom: .75rem;
    color: #eee;
}
.col-article-content ul, .col-article-content ol {
    margin-bottom: 1.5rem; padding-left: 1.75rem;
}
.col-article-content li { margin-bottom: .5rem; }
.col-article-content blockquote {
    border-left: 3px solid var(--primary-color);
    padding: .9rem 1.25rem;
    margin: 2rem 0;
    background: rgba(56,182,255,.05);
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #aaa;
    font-size: 1.05rem;
}
.col-article-content img {
    max-width: 100%; height: auto;
    border-radius: 8px; margin: 2rem 0;
    border: 1px solid #1e1e1e;
}
.col-article-content a { color: var(--primary-color); }
.col-article-content a:hover { text-decoration: underline; }
.col-article-content pre {
    background: #111; color: #d4d4d4;
    padding: 1.25rem; border-radius: 8px;
    overflow-x: auto; margin: 1.5rem 0;
    border: 1px solid #1e1e1e;
    font-size: .9rem;
}
.col-article-content code {
    background: #1a1a1a;
    color: var(--primary-color);
    padding: .15em .4em;
    border-radius: 4px;
    font-size: .88em;
}
.col-article-content table {
    width: 100%; margin: 1.5rem 0;
    border-collapse: collapse; font-size: .9rem;
}
.col-article-content table th,
.col-article-content table td {
    padding: .6rem .9rem;
    border: 1px solid #222;
}
.col-article-content table th {
    background: #111; color: #fff; font-weight: 600;
}
.col-article-content table td { color: #bbb; }

/* Author card */
.col-author-card {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 12px;
    padding: 1.5rem;
}

/* Share buttons */
.col-share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid #2a2a2a;
    color: #888;
    text-decoration: none;
    font-size: .82rem;
    transition: all .18s;
    background: transparent;
    cursor: pointer;
}
.col-share-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    background: rgba(56,182,255,.08);
}

/* Audio player */
.col-audio-player {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
}
.col-audio-wrap { gap: .75rem; }
.col-audio-play-btn {
    width: 40px; height: 40px;
    border-radius: 50%;
    border: none;
    background: var(--primary-color);
    color: #000;
    font-size: .9rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: opacity .15s;
}
.col-audio-play-btn:hover { opacity: .85; }
.col-audio-progress-wrap {
    width: 100%;
    height: 4px;
    background: #2a2a2a;
    border-radius: 4px;
    cursor: pointer;
    position: relative;
}
.col-audio-progress-bar {
    height: 100%;
    width: 0%;
    background: var(--primary-color);
    border-radius: 4px;
    pointer-events: none;
    transition: width .1s linear;
}
.col-audio-time {
    color: #555;
    font-size: .73rem;
    font-variant-numeric: tabular-nums;
}
.col-audio-speed-btn {
    background: transparent;
    border: 1px solid #2a2a2a;
    color: #888;
    border-radius: 6px;
    font-size: .75rem;
    padding: .2rem .5rem;
    cursor: pointer;
    transition: all .15s;
}
.col-audio-speed-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Sidebar */
.col-sidebar-block {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 10px;
    overflow: hidden;
}
.col-sidebar-head {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .85rem 1rem;
    border-bottom: 1px solid #1e1e1e;
}
.col-sidebar-item {
    padding: .75rem 1rem;
    transition: background .15s;
}
.col-sidebar-item:hover { background: #161616; }
.col-sidebar-item.border-bottom { border-bottom: 1px solid #1a1a1a !important; }
</style>
@endpush

@push('scripts')
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const icon = document.getElementById('copyIcon');
        const btn = document.getElementById('copyBtn');
        icon.className = 'fas fa-check';
        btn.style.borderColor = '#22c55e';
        btn.style.color = '#22c55e';
        setTimeout(() => {
            icon.className = 'fas fa-link';
            btn.style.borderColor = '';
            btn.style.color = '';
        }, 2000);
    });
}

@if($column->audio_path)
(function () {
    const audio     = document.getElementById('colAudio');
    const playBtn   = document.getElementById('colAudioPlayBtn');
    const playIcon  = document.getElementById('colAudioPlayIcon');
    const bar       = document.getElementById('colAudioProgressBar');
    const current   = document.getElementById('colAudioCurrent');
    const duration  = document.getElementById('colAudioDuration');
    const speedBtn  = document.getElementById('colAudioSpeedBtn');
    const speeds    = [1, 1.25, 1.5, 0.75];
    let speedIdx    = 0;

    if (!audio) return;

    function fmt(s) {
        const m = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        return m + ':' + String(sec).padStart(2, '0');
    }

    audio.addEventListener('loadedmetadata', () => {
        duration.textContent = fmt(audio.duration);
    });

    audio.addEventListener('timeupdate', () => {
        if (!audio.duration) return;
        const pct = (audio.currentTime / audio.duration) * 100;
        bar.style.width = pct + '%';
        current.textContent = fmt(audio.currentTime);
    });

    audio.addEventListener('ended', () => {
        playIcon.className = 'fas fa-play';
        bar.style.width = '0%';
        current.textContent = '0:00';
        audio.currentTime = 0;
    });

    window.toggleColAudio = function () {
        if (audio.paused) {
            audio.play();
            playIcon.className = 'fas fa-pause';
        } else {
            audio.pause();
            playIcon.className = 'fas fa-play';
        }
    };

    window.seekColAudio = function (e) {
        if (!audio.duration) return;
        const rect = e.currentTarget.getBoundingClientRect();
        audio.currentTime = ((e.clientX - rect.left) / rect.width) * audio.duration;
    };

    window.changeColSpeed = function () {
        speedIdx = (speedIdx + 1) % speeds.length;
        audio.playbackRate = speeds[speedIdx];
        speedBtn.textContent = speeds[speedIdx] + '×';
    };
})();
@endif
</script>
@endpush

@endsection
