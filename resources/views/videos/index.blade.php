@extends('layouts.app')

@php
use Illuminate\Support\Str;
function fmtDur(int $s): string {
    $m = floor($s / 60); $sec = $s % 60;
    return ($m >= 60 ? floor($m/60).':'.str_pad($m%60,2,'0',STR_PAD_LEFT).':' : $m.':') . str_pad($sec,2,'0',STR_PAD_LEFT);
}
function platformColor(string $code): string {
    return match($code) { 'youtube' => '#ff0000', 'vimeo' => '#1ab7ea', default => '#38b6ff' };
}
function platformIcon(string $code): string {
    return match($code) { 'youtube' => 'fa-youtube', 'vimeo' => 'fa-vimeo-v', default => 'fa-play-circle' };
}
@endphp

@section('title', 'ConocIA TV — Video')

@section('content')

{{-- ══════════════════════════════════════════════
     HEADER — ConocIA TV branding
══════════════════════════════════════════════ --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #1e1e1e;" class="py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:var(--primary-color);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-tv text-white" style="font-size:.95rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:1.1rem;letter-spacing:.01em;">
                            ConocIA <span style="color:var(--primary-color);">TV</span>
                        </div>
                        <div style="color:#666;font-size:.7rem;margin-top:-2px;">Contenido audiovisual sobre IA</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 ms-2"
                     style="background:rgba(255,0,0,.12);border:1px solid rgba(255,80,80,.2);border-radius:20px;padding:3px 10px;">
                    <span style="width:6px;height:6px;background:#ff4757;border-radius:50%;display:inline-block;animation:tv-pulse 2s infinite;"></span>
                    <span style="color:#ff6b7a;font-size:.65rem;font-weight:700;letter-spacing:.08em;">EN VIVO</span>
                </div>
            </div>
            <nav style="font-size:.8rem;">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active text-light">ConocIA TV</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     HERO — Video principal
══════════════════════════════════════════════ --}}
@if($featuredVideo)
<section style="background:#000;">
    <div class="container-fluid px-0">
        <div class="tv-hero position-relative overflow-hidden">
            {{-- Background thumbnail blurred --}}
            <div class="tv-hero-bg"
                 style="background-image:url('{{ $featuredVideo->thumbnail_url }}');"></div>
            <div class="tv-hero-overlay"></div>

            <div class="container position-relative py-5" style="z-index:2;">
                <div class="row align-items-center g-4">
                    {{-- Thumbnail + play --}}
                    <div class="col-lg-7">
                        <a href="{{ route('videos.show', $featuredVideo->id) }}"
                           class="d-block position-relative rounded-3 overflow-hidden tv-hero-thumb">
                            <img src="{{ $featuredVideo->thumbnail_url }}"
                                 alt="{{ $featuredVideo->title }}"
                                 class="w-100"
                                 style="aspect-ratio:16/9;object-fit:cover;">
                            <div class="tv-hero-play-overlay">
                                <div class="tv-hero-play-btn">
                                    <i class="fas fa-play ms-1"></i>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <span class="badge" style="background:rgba(0,0,0,.75);font-size:.75rem;">
                                    <i class="fas fa-clock me-1"></i>{{ fmtDur($featuredVideo->duration_seconds ?? 0) }}
                                </span>
                            </div>
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge" style="background:{{ platformColor($featuredVideo->platform->code) }};">
                                    <i class="fab {{ platformIcon($featuredVideo->platform->code) }} me-1"></i>{{ $featuredVideo->platform->name }}
                                </span>
                            </div>
                        </a>
                    </div>

                    {{-- Info --}}
                    <div class="col-lg-5">
                        @if($featuredVideo->categories->count())
                        <div class="mb-2">
                            @foreach($featuredVideo->categories->take(2) as $cat)
                            <span class="badge me-1" style="background:rgba(56,182,255,.2);color:var(--primary-color);border:1px solid rgba(56,182,255,.3);font-size:.7rem;">
                                {{ $cat->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif

                        <h1 class="text-white fw-bold mb-3" style="font-size:clamp(1.2rem,2.5vw,1.8rem);line-height:1.25;">
                            {{ $featuredVideo->title }}
                        </h1>

                        @if($featuredVideo->description)
                        <p style="color:#aaa;font-size:.88rem;line-height:1.6;" class="mb-3">
                            {{ Str::limit($featuredVideo->description, 180) }}
                        </p>
                        @endif

                        {{-- AI Summary --}}
                        @if($featuredVideo->hasAiSummary())
                        <div class="mb-3 rounded-2 p-3" style="background:rgba(56,182,255,.07);border:1px solid rgba(56,182,255,.15);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-robot" style="color:var(--primary-color);font-size:.8rem;"></i>
                                <span class="text-uppercase fw-bold" style="color:var(--primary-color);font-size:.65rem;letter-spacing:.1em;">Resumen IA</span>
                            </div>
                            @foreach(explode('|||', $featuredVideo->ai_summary) as $point)
                            <div class="d-flex align-items-start gap-2 mb-1">
                                <span style="color:var(--primary-color);margin-top:2px;flex-shrink:0;">›</span>
                                <span style="color:#ccc;font-size:.82rem;">{{ trim($point) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="d-flex align-items-center gap-3 mb-4" style="font-size:.8rem;color:#888;">
                            <span><i class="far fa-eye me-1"></i>{{ number_format($featuredVideo->view_count) }}</span>
                            <span><i class="far fa-calendar me-1"></i>{{ $featuredVideo->published_at->locale('es')->diffForHumans() }}</span>
                        </div>

                        <a href="{{ route('videos.show', $featuredVideo->id) }}"
                           class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-play me-2"></i>Ver ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════
     MAIN CONTENT — dark background
══════════════════════════════════════════════ --}}
<div style="background:#0d0d0d;">

    {{-- ── Filtro de categorías pill bar ── --}}
    @if($videoCategories->count())
    <div style="background:#111;border-bottom:1px solid #1a1a1a;position:sticky;top:56px;z-index:100;">
        <div class="container">
            <div class="d-flex gap-2 overflow-auto hide-scrollbar py-2">
                <button class="tv-cat-pill active flex-shrink-0" data-cat="all">Todo</button>
                @foreach($videoCategories as $cat)
                <button class="tv-cat-pill flex-shrink-0" data-cat="{{ $cat->id }}">
                    {{ $cat->name }}
                    <span style="opacity:.5;font-size:.65rem;">{{ $cat->videos_count }}</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Fila: Más vistos ── --}}
    @if($popularVideos->count() > 1)
    <div class="py-4" data-cat-section="all">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-fire" style="color:#ff6b35;font-size:.9rem;"></i>
                    <span class="fw-bold text-white" style="font-size:.95rem;">Más vistos</span>
                </div>
            </div>
            <div class="tv-scroll-row">
                @foreach($popularVideos->skip(1)->take(8) as $v)
                @include('partials.tv-video-card', ['video' => $v])
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Fila: Recientes ── --}}
    @if($latestVideos->count())
    <div class="py-4" style="border-top:1px solid #1a1a1a;" data-cat-section="all">
        <div class="container">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-clock" style="color:var(--primary-color);font-size:.9rem;"></i>
                <span class="fw-bold text-white" style="font-size:.95rem;">Recientes</span>
            </div>
            <div class="tv-scroll-row">
                @foreach($latestVideos->take(8) as $v)
                @include('partials.tv-video-card', ['video' => $v])
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Filas por categoría ── --}}
    @foreach($videosByCategory as $catId => $row)
    @if($row['videos']->count() >= 2)
    <div class="py-4" style="border-top:1px solid #1a1a1a;" data-cat-section="{{ $catId }}">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:3px;height:16px;background:var(--primary-color);border-radius:2px;"></div>
                    <span class="fw-bold text-white" style="font-size:.95rem;">{{ $row['category']->name }}</span>
                </div>
                <a href="{{ route('videos.category', $row['category']->id) }}"
                   class="text-decoration-none" style="color:#666;font-size:.78rem;">
                    Ver todos <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </div>
            <div class="tv-scroll-row">
                @foreach($row['videos'] as $v)
                @include('partials.tv-video-card', ['video' => $v])
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endforeach

    {{-- ── Estado vacío ── --}}
    @if($allVideos->isEmpty())
    <div class="container py-5 text-center">
        <div style="width:80px;height:80px;background:#1a1a1a;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
            <i class="fas fa-tv" style="font-size:2rem;color:#333;"></i>
        </div>
        <h4 class="text-white mb-2">Pronto habrá contenido aquí</h4>
        <p style="color:#666;font-size:.9rem;">Los videos se agregan automáticamente desde YouTube, Vimeo y más.</p>
    </div>
    @endif

    <div style="height:3rem;"></div>
</div>

@push('styles')
<style>
/* ── ConocIA TV global ── */
@keyframes tv-pulse {
    0%,100% { opacity:1; }
    50%      { opacity:.3; }
}

/* Hero */
.tv-hero { min-height: 420px; }
.tv-hero-bg {
    position:absolute; inset:0;
    background-size:cover; background-position:center;
    filter:blur(28px) brightness(.35) saturate(.6);
    transform:scale(1.08);
}
.tv-hero-overlay {
    position:absolute; inset:0;
    background:linear-gradient(to right, rgba(0,0,0,.85) 0%, rgba(0,0,0,.5) 60%, rgba(0,0,0,.2) 100%);
}
.tv-hero-thumb { cursor:pointer; }
.tv-hero-play-overlay {
    position:absolute; inset:0;
    background:rgba(0,0,0,0);
    display:flex; align-items:center; justify-content:center;
    transition:background .2s ease;
}
.tv-hero-thumb:hover .tv-hero-play-overlay { background:rgba(0,0,0,.35); }
.tv-hero-play-btn {
    width:64px; height:64px;
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(4px);
    border:2px solid rgba(255,255,255,.4);
    border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:1.4rem;
    opacity:0; transition:opacity .2s ease, transform .2s ease;
}
.tv-hero-thumb:hover .tv-hero-play-btn {
    opacity:1; transform:scale(1.05);
}

/* Category pills */
.tv-cat-pill {
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.1);
    color:#aaa;
    border-radius:20px;
    padding:4px 14px;
    font-size:.78rem;
    cursor:pointer;
    transition:all .15s ease;
    white-space:nowrap;
}
.tv-cat-pill:hover { background:rgba(255,255,255,.1); color:#fff; }
.tv-cat-pill.active {
    background:var(--primary-color);
    border-color:var(--primary-color);
    color:#fff;
    font-weight:600;
}

/* Scroll rows */
.tv-scroll-row {
    display:flex;
    gap:14px;
    overflow-x:auto;
    padding-bottom:8px;
    scrollbar-width:thin;
    scrollbar-color:#333 transparent;
}
.tv-scroll-row::-webkit-scrollbar { height:4px; }
.tv-scroll-row::-webkit-scrollbar-track { background:transparent; }
.tv-scroll-row::-webkit-scrollbar-thumb { background:#333; border-radius:2px; }

/* Video card */
.tv-card {
    flex-shrink:0;
    width:240px;
    background:#161616;
    border-radius:10px;
    overflow:hidden;
    cursor:pointer;
    transition:transform .2s ease, box-shadow .2s ease;
    text-decoration:none;
    display:block;
}
.tv-card:hover {
    transform:translateY(-4px) scale(1.01);
    box-shadow:0 12px 32px rgba(0,0,0,.5);
}
.tv-card-thumb {
    position:relative;
    aspect-ratio:16/9;
    overflow:hidden;
    background:#111;
}
.tv-card-thumb img {
    width:100%; height:100%; object-fit:cover;
    transition:transform .35s ease;
}
.tv-card:hover .tv-card-thumb img { transform:scale(1.06); }
.tv-card-play {
    position:absolute; inset:0;
    display:flex; align-items:center; justify-content:center;
    background:rgba(0,0,0,0);
    transition:background .2s ease;
}
.tv-card:hover .tv-card-play { background:rgba(0,0,0,.4); }
.tv-card-play-icon {
    width:38px; height:38px;
    background:rgba(255,255,255,.2);
    border:1.5px solid rgba(255,255,255,.5);
    border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.85rem;
    opacity:0; transition:opacity .2s ease;
}
.tv-card:hover .tv-card-play-icon { opacity:1; }
.tv-card-dur {
    position:absolute; bottom:6px; right:6px;
    background:rgba(0,0,0,.8);
    color:#fff; font-size:.65rem;
    padding:2px 6px; border-radius:4px;
}
.tv-card-platform {
    position:absolute; top:6px; left:6px;
    width:22px; height:22px;
    border-radius:4px;
    display:flex; align-items:center; justify-content:center;
    font-size:.65rem; color:#fff;
}
.tv-card-body {
    padding:10px 12px 12px;
}
.tv-card-title {
    color:#e0e0e0;
    font-size:.82rem;
    font-weight:600;
    line-height:1.35;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
    margin-bottom:6px;
}
.tv-card-meta {
    color:#555;
    font-size:.7rem;
    display:flex; gap:10px;
}
.tv-card-keywords {
    display:flex; flex-wrap:wrap; gap:4px;
    margin-top:6px;
    max-height:0; overflow:hidden;
    transition:max-height .2s ease;
}
.tv-card:hover .tv-card-keywords { max-height:60px; }
.tv-card-kw {
    background:rgba(56,182,255,.1);
    color:var(--primary-color);
    border:1px solid rgba(56,182,255,.15);
    border-radius:10px;
    padding:1px 7px;
    font-size:.62rem;
}
.tv-card-ai-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:rgba(56,182,255,.08);
    border:1px solid rgba(56,182,255,.15);
    border-radius:10px;
    padding:1px 7px;
    font-size:.62rem;
    color:var(--primary-color);
    margin-bottom:4px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pills   = document.querySelectorAll('.tv-cat-pill');
    const sections = document.querySelectorAll('[data-cat-section]');

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const cat = this.dataset.cat;
            sections.forEach(function (sec) {
                const secCat = sec.dataset.catSection;
                if (cat === 'all' || secCat === 'all' || secCat === cat) {
                    sec.style.display = '';
                } else {
                    sec.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush

@endsection
