@extends('layouts.app')

@section('reading_progress', true)

@section('title', $concepto->title . ' — Conceptos IA | ConocIA')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('conceptos.index') }}" class="text-muted">Conceptos IA</a></li>
                    <li class="breadcrumb-item active" style="color:#334155;">{{ Str::limit($concepto->title, 40) }}</li>
                </ol>
            </nav>

            @if($concepto->category)
            <span class="badge mb-3" style="background:rgba(56,182,255,.15);color:#0369a1;font-size:.75rem;letter-spacing:.04em;">{{ strtoupper($concepto->category) }}</span>
            @endif
            <h1 class="fw-bold mb-3" style="color:#0f172a;font-size:2rem;line-height:1.25;">{{ $concepto->title }}</h1>

            <div class="d-flex flex-wrap align-items-center gap-3 mb-4" style="font-size:.82rem;color:#64748b;">
                <span><i class="fas fa-clock me-1"></i>{{ $concepto->reading_time ?? 5 }} min de lectura</span>
                <span><i class="fas fa-eye me-1"></i>{{ number_format($concepto->views) }} lecturas</span>
                @if($concepto->published_at)<span><i class="fas fa-calendar me-1"></i>{{ $concepto->published_at->locale('es')->isoFormat('D MMM YYYY') }}</span>@endif
            </div>

            @if($concepto->definition)
            <div class="rounded-3 mb-5 p-4" style="background:#f0f9ff;border-left:4px solid var(--primary-color);">
                <div class="d-flex gap-3">
                    <i class="fas fa-lightbulb mt-1 flex-shrink-0" style="color:var(--primary-color);font-size:1rem;"></i>
                    <p class="mb-0 fw-semibold" style="color:#1e40af;font-size:1.02rem;line-height:1.75;">{{ $concepto->definition }}</p>
                </div>
            </div>
            @endif

            <div class="article-content mb-5">
                {!! $concepto->content !!}
            </div>

            @if(!empty($concepto->key_players))
            <div class="profundiza-card p-4 mb-4">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-users me-2" style="color:var(--primary-color);"></i>Actores clave
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($concepto->key_players as $player)
                    <span class="badge px-3 py-2" style="background:#f0f9ff;color:#0369a1;font-size:.82rem;font-weight:500;border:1px solid #bae6fd;">
                        {{ is_array($player) ? ($player['name'] ?? '') : $player }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($concepto->further_reading))
            <div class="profundiza-card p-4 mb-5">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-external-link-alt me-2" style="color:var(--primary-color);"></i>Para profundizar
                </h5>
                <ul class="mb-0" style="list-style:none;padding:0;">
                    @foreach($concepto->further_reading as $link)
                    <li class="mb-2 d-flex align-items-start gap-2">
                        <i class="fas fa-angle-right mt-1 flex-shrink-0" style="color:var(--primary-color);font-size:.8rem;"></i>
                        <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" style="color:#0369a1;font-size:.9rem;text-decoration:underline;text-underline-offset:3px;">
                            {{ $link['title'] ?? $link }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px;">

                @if($related->isNotEmpty())
                <div class="profundiza-card p-4 mb-4">
                    <p class="profundiza-section-label">Conceptos relacionados</p>
                    @foreach($related as $r)
                    <a href="{{ route('conceptos.show', $r->slug) }}" class="d-flex gap-3 mb-3 pb-3 text-decoration-none" style="border-bottom:1px solid #f1f5f9;">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-2" style="width:32px;height:32px;background:rgba(56,182,255,.1);margin-top:2px;">
                            <i class="fas fa-brain" style="color:var(--primary-color);font-size:.7rem;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            @if($r->category)<span class="badge mb-1" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.6rem;">{{ $r->category }}</span>@endif
                            <div class="fw-semibold" style="color:#1e293b;font-size:.84rem;line-height:1.3;">{{ $r->title }}</div>
                            <div style="color:#94a3b8;font-size:.74rem;margin-top:2px;line-height:1.4;">{{ Str::limit($r->definition ?? '', 65) }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                <div class="profundiza-card p-4 text-center mb-3">
                    <i class="fas fa-book-open fa-2x mb-2" style="color:var(--primary-color);"></i>
                    <p style="color:#64748b;font-size:.84rem;margin:.5rem 0 1rem;">Explora toda la enciclopedia de IA</p>
                    <a href="{{ route('conceptos.index') }}" class="btn btn-sm w-100" style="background:var(--primary-color);color:#fff;">Ver todos los conceptos</a>
                </div>
                <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="fas fa-microscope me-2"></i>Análisis de Fondo
                </a>
            </div>
        </div>
    </div>
</div>
@include('partials.schema-breadcrumb', ['crumbs' => [['name' => 'Inicio', 'url' => url('/')'], ['name' => 'Conceptos IA', 'url' => route('conceptos.index')'], ['name' => $concepto->title]]])
@include('partials.schema-article', ['item' => $concepto, 'routeName' => 'conceptos.show', 'type' => 'TechArticle', 'section' => 'Conceptos IA'])
@endsection
