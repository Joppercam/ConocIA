@extends('layouts.app')

@section('reading_progress', true)

@section('title', $analysis->title . ' — Análisis de Fondo | ConocIA')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('analisis.index') }}" class="text-muted">Análisis de Fondo</a></li>
                    <li class="breadcrumb-item active" style="color:#334155;">{{ Str::limit($analysis->title, 40) }}</li>
                </ol>
            </nav>

            <span class="badge mb-3" style="background:rgba(56,182,255,.15);color:#0369a1;font-size:.75rem;letter-spacing:.04em;">{{ strtoupper($analysis->category ?? 'ANÁLISIS') }}</span>
            <h1 class="fw-bold mb-3" style="color:#0f172a;font-size:2rem;line-height:1.25;">{{ $analysis->title }}</h1>

            @if($analysis->excerpt)
            <p style="color:#475569;font-size:1.05rem;line-height:1.7;margin-bottom:1.5rem;">{{ $analysis->excerpt }}</p>
            @endif

            <div class="d-flex flex-wrap align-items-center gap-3 py-3 mb-5" style="border-top:2px solid #f1f5f9;border-bottom:2px solid #f1f5f9;font-size:.82rem;color:#64748b;">
                @if($analysis->author)<span><i class="fas fa-user me-1"></i>{{ $analysis->author->name }}</span>@endif
                <span><i class="fas fa-clock me-1"></i>{{ $analysis->reading_time ?? 10 }} min de lectura</span>
                @if($analysis->published_at)<span><i class="fas fa-calendar me-1"></i>{{ $analysis->published_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>@endif
                <span><i class="fas fa-eye me-1"></i>{{ number_format($analysis->views) }} lecturas</span>
            </div>

            <div class="article-content mb-5">
                {!! $analysis->content !!}
            </div>

            @if(!empty($analysis->key_players))
            <div class="profundiza-card p-4 mb-5">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-users me-2" style="color:var(--primary-color);"></i>Actores mencionados
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($analysis->key_players as $player)
                    <span class="badge px-3 py-2" style="background:#f0f9ff;color:#0369a1;font-size:.82rem;font-weight:500;border:1px solid #bae6fd;">
                        {{ is_array($player) ? ($player['name'] ?? '') : $player }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($related->isNotEmpty())
            <p class="profundiza-section-label mt-5">Más análisis</p>
            <div class="row g-3">
                @foreach($related as $r)
                <div class="col-md-6">
                    <a href="{{ route('analisis.show', $r->slug) }}" class="text-decoration-none d-block">
                        <div class="profundiza-card p-3">
                            <span class="badge mb-2" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.65rem;">{{ strtoupper($r->category ?? 'ANÁLISIS') }}</span>
                            <h6 class="mb-1" style="color:#1e293b;font-size:.87rem;line-height:1.35;">{{ $r->title }}</h6>
                            <small style="color:#94a3b8;"><i class="fas fa-clock me-1"></i>{{ $r->reading_time ?? 10 }} min</small>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px;">
                <div class="profundiza-card p-4 mb-4">
                    <p class="profundiza-section-label">Sobre este análisis</p>
                    <p style="color:#475569;font-size:.85rem;line-height:1.7;margin:0;">
                        Los análisis de fondo de ConocIA son piezas editoriales de largo aliento que examinan los temas de IA en toda su complejidad. Publicamos 1–2 piezas por semana.
                    </p>
                </div>
                <a href="{{ route('analisis.index') }}" class="btn w-100 mb-2" style="background:var(--primary-color);color:#fff;">
                    <i class="fas fa-arrow-left me-2"></i>Ver todos los análisis
                </a>
                <a href="{{ route('conceptos.index') }}" class="btn w-100 btn-outline-secondary">
                    <i class="fas fa-book-open me-2"></i>Conceptos IA
                </a>
            </div>
        </div>
    </div>
</div>
@include('partials.schema-breadcrumb', ['crumbs' => [['name' => 'Inicio', 'url' => url('/')'], ['name' => 'Análisis de Fondo', 'url' => route('analisis.index')'], ['name' => $analysis->title]]])
@include('partials.schema-article', ['item' => $analysis, 'routeName' => 'analisis.show', 'type' => 'TechArticle', 'section' => 'Análisis de Fondo'])
@endsection
