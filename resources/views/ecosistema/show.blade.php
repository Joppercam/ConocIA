@extends('layouts.app')

@section('title', $actor->name . ' — Ecosistema IA Chile | ConocIA')
@section('meta_description', $actor->excerpt)

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-4">
    <div class="container py-2">
        {{-- Breadcrumb --}}
        <nav style="font-size:.82rem;color:#64748b;margin-bottom:1.25rem;">
            <a href="{{ route('ecosistema.index') }}" style="color:#7dd3f0;text-decoration:none;">
                <i class="fas fa-map-marked-alt me-1"></i>Ecosistema IA Chile
            </a>
            <span class="mx-2">›</span>
            <span style="color:#94a3b8;">{{ $actor->name }}</span>
        </nav>

        <div class="d-flex flex-wrap align-items-start gap-3">
            <div class="flex-grow-1">
                <span class="badge mb-2 d-inline-block" style="background:{{ $actor->type_color }}22;color:{{ $actor->type_color }};border:1px solid {{ $actor->type_color }}44;font-size:.78rem;">
                    {{ $actor->type_label }}
                </span>
                <h1 class="fw-bold text-white mb-2" style="font-size:1.9rem;line-height:1.2;">{{ $actor->name }}</h1>
                <div style="color:#64748b;font-size:.85rem;">
                    <i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>{{ $actor->location }}
                    @if($actor->region !== 'Metropolitana'), {{ $actor->region }}@endif
                    @if($actor->founded)
                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>Fundado: {{ $actor->founded }}</span>
                    @endif
                    @if($actor->director)
                        <span class="ms-3"><i class="fas fa-user me-1"></i>{{ $actor->director }}</span>
                    @endif
                </div>
            </div>
            @if($actor->url)
                <a href="{{ $actor->url }}" target="_blank" rel="noopener"
                   class="btn btn-outline-light btn-sm align-self-start" style="font-size:.85rem;white-space:nowrap;">
                    <i class="fas fa-external-link-alt me-1"></i>Visitar sitio web
                </a>
            @endif
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- Descripción --}}
        <div class="col-lg-8">
            <div class="profundiza-card p-4 p-md-5 mb-4">
                <h2 class="fw-bold mb-4" style="color:#0f172a;font-size:1.15rem;border-bottom:2px solid rgba(56,182,255,.2);padding-bottom:.75rem;">
                    Sobre {{ explode('—', $actor->name)[0] }}
                </h2>
                <div style="color:#475569;font-size:.97rem;line-height:1.9;">
                    @foreach(explode("\n\n", $actor->description) as $paragraph)
                        @if(trim($paragraph))
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Áreas de foco --}}
            @if($actor->focus_areas)
            <div class="profundiza-card p-4 mb-4">
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1rem;">Áreas de foco</h3>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($actor->focus_areas as $area)
                        <span class="badge px-3 py-2" style="background:#f1f5f9;color:#334155;font-size:.85rem;font-weight:500;border:1px solid #e2e8f0;">
                            {{ $area }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actores relacionados --}}
            @if($related->isNotEmpty())
            <div>
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1rem;">Otros {{ $actor->type_label }}s del ecosistema</h3>
                <div class="row g-3">
                    @foreach($related as $rel)
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('ecosistema.show', $rel->slug) }}" style="text-decoration:none;">
                            <div class="profundiza-card p-3 h-100" style="transition:border-color .2s;">
                                <div class="fw-bold mb-1" style="color:#0f172a;font-size:.85rem;line-height:1.35;">{{ $rel->name }}</div>
                                <div style="color:#64748b;font-size:.75rem;">
                                    <i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>{{ $rel->location }}
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar: Datos clave --}}
        <div class="col-lg-4">
            <div class="profundiza-card p-4 sticky-top" style="top:80px;">
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:.97rem;border-bottom:2px solid rgba(56,182,255,.2);padding-bottom:.6rem;">
                    Datos clave
                </h3>

                @if($actor->key_facts)
                <ul class="list-unstyled mb-4" style="margin:0;">
                    @foreach($actor->key_facts as $fact)
                    <li class="d-flex align-items-start gap-2 mb-2" style="font-size:.88rem;color:#475569;">
                        <i class="fas fa-check-circle mt-1 flex-shrink-0" style="color:#00c896;font-size:.8rem;"></i>
                        <span>{{ $fact }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif

                {{-- Ficha técnica --}}
                <div style="background:#f8fafc;border-radius:.5rem;padding:1rem;font-size:.82rem;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:#64748b;">Tipo</span>
                        <span class="fw-semibold" style="color:#0f172a;">{{ $actor->type_label }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:#64748b;">Ubicación</span>
                        <span class="fw-semibold" style="color:#0f172a;">{{ $actor->location }}</span>
                    </div>
                    @if($actor->region !== 'Metropolitana')
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:#64748b;">Región</span>
                        <span class="fw-semibold" style="color:#0f172a;">{{ $actor->region }}</span>
                    </div>
                    @endif
                    @if($actor->founded)
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:#64748b;">Fundación</span>
                        <span class="fw-semibold" style="color:#0f172a;">{{ $actor->founded }}</span>
                    </div>
                    @endif
                    @if($actor->director)
                    <div class="d-flex justify-content-between mb-2">
                        <span style="color:#64748b;">Director/a</span>
                        <span class="fw-semibold" style="color:#0f172a;">{{ $actor->director }}</span>
                    </div>
                    @endif
                    @if($actor->url)
                    <div class="d-flex justify-content-between">
                        <span style="color:#64748b;">Sitio web</span>
                        <a href="{{ $actor->url }}" target="_blank" rel="noopener"
                           style="color:var(--primary-color);font-size:.8rem;word-break:break-all;">
                            {{ parse_url($actor->url, PHP_URL_HOST) }}
                        </a>
                    </div>
                    @endif
                </div>

                <a href="{{ route('ecosistema.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-3" style="font-size:.82rem;">
                    <i class="fas fa-arrow-left me-1"></i>Volver al ecosistema
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
