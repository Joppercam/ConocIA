@extends('layouts.app')

@section('reading_progress', true)

@php use Illuminate\Support\Str; @endphp

@section('title', $column->title . ' - ConocIA')

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
                            · <i class="far fa-clock me-1"></i>{{ $column->reading_time }} min de lectura
                            @endif
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
</script>
@endpush

@endsection
