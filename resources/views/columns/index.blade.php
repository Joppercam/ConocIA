{{-- resources/views/columns/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Columnas de Opinión - ConocIA')

@php use Illuminate\Support\Str; @endphp

@section('content')

{{-- Page header --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active text-light">Columnas</li>
            </ol>
        </nav>
        <div class="row align-items-end g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width:4px;height:36px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
                    <div>
                        <div class="mb-1">
                            <span style="font-size:.7rem;font-weight:700;letter-spacing:.12em;color:var(--primary-color);text-transform:uppercase;">Editorial</span>
                        </div>
                        <h1 class="mb-0 text-white fw-bold" style="font-size:2rem;line-height:1.15;">Columnas de Opinión</h1>
                    </div>
                </div>
                <p class="mb-0 ms-4 ps-2" style="color:#888;font-size:.9rem;max-width:520px;">
                    Análisis, reflexiones y perspectivas de expertos sobre inteligencia artificial y el futuro tecnológico.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex gap-4 justify-content-lg-end">
                    <div class="text-center">
                        <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $columns->total() }}</div>
                        <div style="color:#666;font-size:.75rem;">columnas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $columnists->count() }}</div>
                        <div style="color:#666;font-size:.75rem;">columnistas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $categories->count() }}</div>
                        <div style="color:#666;font-size:.75rem;">categorías</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Columnists strip --}}
@if($columnists->count())
<div style="background:#0d0d0d;border-bottom:1px solid #222;" class="py-4">
    <div class="container">
        <div class="mb-3">
            <span style="font-size:.7rem;font-weight:700;letter-spacing:.1em;color:#666;text-transform:uppercase;">Voces editoriales</span>
        </div>
        <div class="d-flex gap-4 flex-wrap">
            @foreach($columnists as $columnist)
            <a href="{{ route('columns.author', $columnist->id) }}" class="columnist-chip text-decoration-none d-flex align-items-center gap-2">
                <img src="{{ asset($columnist->avatar ?? 'storage/images/defaults/user-profile.jpg') }}"
                     class="rounded-circle columnist-avatar"
                     width="44" height="44"
                     alt="{{ $columnist->name }}"
                     onerror="this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                <div>
                    <div class="text-white fw-semibold" style="font-size:.82rem;line-height:1.2;">{{ $columnist->name }}</div>
                    <div style="color:#666;font-size:.72rem;">{{ $columnist->columns_count ?? 0 }} columnas</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Featured column hero --}}
@if($featuredColumns->count())
@php
    $hero     = $featuredColumns->first();
    $heroColor = $hero->category ? ($hero->category->color ?? '#38b6ff') : '#38b6ff';
@endphp
<div style="background:#111;" class="py-5">
    <div class="container">
        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="fas fa-star" style="color:#38b6ff;font-size:.75rem;"></i>
            <span style="font-size:.7rem;font-weight:700;letter-spacing:.1em;color:#38b6ff;text-transform:uppercase;">Columna destacada</span>
        </div>
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                @if($hero->category)
                <a href="{{ route('columns.category', $hero->category->slug) }}"
                   class="badge text-decoration-none mb-3 d-inline-block"
                   style="background:{{ $heroColor }};font-size:.72rem;letter-spacing:.05em;">
                    {{ $hero->category->name }}
                </a>
                @endif
                <h2 class="text-white fw-bold mb-3" style="font-size:1.85rem;line-height:1.2;">
                    <a href="{{ route('columns.show', $hero->slug) }}" class="text-white text-decoration-none col-hero-link">
                        {{ $hero->title }}
                    </a>
                </h2>
                @if($hero->excerpt)
                <p style="color:#aaa;font-size:1rem;line-height:1.7;max-width:540px;" class="mb-4">
                    {{ Str::limit($hero->excerpt, 180) }}
                </p>
                @endif
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="{{ asset($hero->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}"
                         class="rounded-circle flex-shrink-0"
                         width="42" height="42"
                         alt="{{ $hero->author->name }}"
                         onerror="this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                    <div>
                        <div class="text-white fw-semibold" style="font-size:.85rem;">{{ $hero->author->name ?? 'Redacción' }}</div>
                        <div style="color:#666;font-size:.78rem;">
                            {{ $hero->published_at?->locale('es')->isoFormat('D MMMM, YYYY') }}
                            @if($hero->reading_time)
                            &middot; <i class="far fa-clock me-1"></i>{{ $hero->reading_time }} min
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('columns.show', $hero->slug) }}"
                   class="btn btn-primary rounded-pill px-4"
                   style="font-size:.85rem;">
                    Leer columna <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="d-flex flex-column gap-3">
                    @foreach($featuredColumns->skip(1) as $fc)
                    @php $fcColor = $fc->category ? ($fc->category->color ?? '#38b6ff') : '#38b6ff'; @endphp
                    <a href="{{ route('columns.show', $fc->slug) }}" class="text-decoration-none col-feat-card d-flex gap-3 align-items-start p-3 rounded-3">
                        <img src="{{ asset($fc->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}"
                             class="rounded-circle flex-shrink-0 mt-1"
                             width="36" height="36"
                             alt="{{ $fc->author->name }}"
                             onerror="this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                        <div>
                            @if($fc->category)
                            <span class="badge mb-1" style="background:{{ $fcColor }};font-size:.65rem;">{{ $fc->category->name }}</span>
                            @endif
                            <div class="text-white fw-semibold" style="font-size:.88rem;line-height:1.3;">{{ $fc->title }}</div>
                            <div style="color:#666;font-size:.75rem;margin-top:.3rem;">{{ $fc->author->name ?? '' }} &middot; {{ $fc->published_at?->locale('es')->diffForHumans() }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Category filters --}}
@if($categories->count())
<div style="background:#0d0d0d;border-bottom:1px solid #1e1e1e;position:sticky;top:56px;z-index:99;" class="py-2">
    <div class="container">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('columns.index') }}" class="col-pill col-pill--active">Todas</a>
            @foreach($categories as $cat)
            <a href="{{ route('columns.category', $cat->slug) }}" class="col-pill">{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Columns grid --}}
<div style="background:#0a0a0a;" class="py-5">
    <div class="container">
        @if($columns->count())
        <div class="row g-4">
            @foreach($columns as $column)
            @php $colCat = $column->category ? ($column->category->color ?? '#38b6ff') : '#38b6ff'; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="col-card h-100 d-flex flex-column">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset($column->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}"
                             class="rounded-circle flex-shrink-0"
                             width="38" height="38"
                             alt="{{ $column->author->name ?? 'Autor' }}"
                             onerror="this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                        <div>
                            <div class="text-white fw-semibold" style="font-size:.82rem;line-height:1.2;">{{ $column->author->name ?? 'Redacción' }}</div>
                            <div style="color:#666;font-size:.72rem;">{{ $column->published_at?->locale('es')->isoFormat('D MMM, YYYY') }}</div>
                        </div>
                    </div>

                    @if($column->category)
                    <a href="{{ route('columns.category', $column->category->slug) }}"
                       class="badge text-decoration-none mb-2 d-inline-block align-self-start col-cat-badge"
                       style="--cat-color:{{ $colCat }};">
                        {{ $column->category->name }}
                    </a>
                    @endif

                    <h5 class="text-white fw-bold mb-2 flex-grow-1" style="font-size:.95rem;line-height:1.4;">
                        <a href="{{ route('columns.show', $column->slug) }}" class="text-white text-decoration-none col-title-link">
                            {{ $column->title }}
                        </a>
                    </h5>

                    @if($column->excerpt)
                    <p style="color:#777;font-size:.82rem;line-height:1.6;" class="mb-3">
                        {{ Str::limit($column->excerpt, 110) }}
                    </p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 col-card-footer">
                        <span style="color:#555;font-size:.75rem;">
                            <i class="far fa-clock me-1"></i>{{ $column->reading_time ?? '?' }} min
                        </span>
                        <a href="{{ route('columns.show', $column->slug) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           style="font-size:.75rem;">
                            Leer <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-4" style="font-size:4rem;opacity:.15;"><i class="fas fa-pen-nib"></i></div>
            <h3 class="text-white fw-bold mb-2" style="font-size:1.3rem;">Las columnas están en camino</h3>
            <p style="color:#666;font-size:.9rem;max-width:380px;margin:0 auto 2rem;">
                Pronto nuestros columnistas comenzarán a publicar análisis y perspectivas sobre inteligencia artificial.
            </p>
            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4" style="font-size:.85rem;">
                <i class="fas fa-home me-2"></i>Volver al inicio
            </a>
        </div>
        @endif

        @if($columns->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $columns->links() }}
        </div>
        @endif

    </div>
</div>

@push('styles')
<style>
.col-card {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 12px;
    padding: 1.25rem;
    transition: border-color .2s, transform .2s, box-shadow .2s;
}
.col-card:hover {
    border-color: rgba(56,182,255,.25);
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(0,0,0,.4);
}
.col-card-footer { border-top: 1px solid #1e1e1e; }
.col-title-link:hover { color: #38b6ff !important; }
.col-hero-link:hover  { color: #38b6ff !important; }

.col-cat-badge {
    background: color-mix(in srgb, var(--cat-color) 15%, transparent) !important;
    color: var(--cat-color) !important;
    border: 1px solid color-mix(in srgb, var(--cat-color) 40%, transparent);
    font-size: .68rem;
    letter-spacing: .04em;
}

.col-pill {
    display: inline-block;
    padding: .28rem .85rem;
    border-radius: 999px;
    border: 1px solid #2a2a2a;
    color: #aaa;
    font-size: .78rem;
    text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
}
.col-pill:hover       { border-color: #38b6ff; color: #38b6ff; }
.col-pill--active     { background: #38b6ff; border-color: #38b6ff; color: #fff !important; }

.columnist-chip { transition: opacity .18s; }
.columnist-chip:hover { opacity: .8; }
.columnist-avatar {
    border: 2px solid #2a2a2a;
    transition: border-color .18s;
    object-fit: cover;
}
.columnist-chip:hover .columnist-avatar { border-color: #38b6ff; }

.col-feat-card {
    background: #161616;
    border: 1px solid #222;
    transition: border-color .18s, background .18s;
}
.col-feat-card:hover {
    background: #1a1a1a;
    border-color: rgba(56,182,255,.2);
}
</style>
@endpush

@endsection
