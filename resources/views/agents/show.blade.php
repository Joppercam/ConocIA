@extends('layouts.app')

@section('title', $agent->name . ' — Agentes IA · ConocIA')
@section('meta_description', $agent->tagline ?? 'Perfil de agente de IA en ConocIA')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">

            {{-- Encabezado --}}
            <div class="d-flex align-items-start gap-4 mb-4">
                @if($agent->logo)
                    <img src="{{ $agent->logo }}" alt="{{ $agent->name }}" style="width:72px;height:72px;object-fit:contain;border-radius:12px;border:1px solid #e5e7eb;">
                @else
                    <div style="width:72px;height:72px;background:var(--primary-color);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-robot fa-2x text-white"></i>
                    </div>
                @endif
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:1.8rem;">{{ $agent->name }}</h1>
                    @if($agent->tagline)
                        <p class="text-muted mb-2">{{ $agent->tagline }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        @if($agent->category)
                        <span class="badge bg-secondary">{{ $agent->category_label }}</span>
                        @endif
                        <span class="badge" style="background:{{ $agent->pricing_color }};">{{ $agent->pricing_label }}</span>
                        @if($agent->type === 'open-source')
                        <span class="badge bg-success">Open Source</span>
                        @endif
                        @if($agent->stars_github)
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-star me-1" style="color:#fbbf24;"></i>{{ $agent->formatted_stars }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            @if($agent->description)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">¿Qué es {{ $agent->name }}?</h5>
                    <p class="mb-0" style="line-height:1.7;">{{ $agent->description }}</p>
                </div>
            </div>
            @endif

            {{-- Capacidades --}}
            @if(!empty($agent->capabilities))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-bolt me-2" style="color:var(--primary-color);"></i>Capacidades</h5>
                    <ul class="list-unstyled mb-0 row g-2">
                        @foreach($agent->capabilities as $cap)
                        <li class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success"></i>
                                <span style="font-size:.9rem;">{{ $cap }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Casos de uso --}}
            @if(!empty($agent->use_cases))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-lightbulb me-2" style="color:var(--primary-color);"></i>Casos de uso</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach($agent->use_cases as $uc)
                        <li class="mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-arrow-right text-primary mt-1" style="font-size:.75rem;"></i>
                                <span style="font-size:.9rem;">{{ $uc }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">Datos técnicos</h6>
                    <dl class="mb-0">
                        <dt class="text-muted" style="font-size:.8rem;">Tipo</dt>
                        <dd class="mb-2">{{ match($agent->type) { 'open-source' => 'Open Source', 'closed' => 'Propietario', 'api' => 'Solo API', default => $agent->type } }}</dd>

                        <dt class="text-muted" style="font-size:.8rem;">Precio</dt>
                        <dd class="mb-2">{{ $agent->pricing_label }}</dd>

                        @if($agent->framework && $agent->framework !== 'none')
                        <dt class="text-muted" style="font-size:.8rem;">Framework base</dt>
                        <dd class="mb-2">{{ ucfirst($agent->framework) }}</dd>
                        @endif

                        <dt class="text-muted" style="font-size:.8rem;">Requiere API key</dt>
                        <dd class="mb-2">{{ $agent->requires_api_key ? 'Sí' : 'No' }}</dd>

                        <dt class="text-muted" style="font-size:.8rem;">Capa gratuita</dt>
                        <dd class="mb-0">{{ $agent->has_free_tier ? 'Sí' : 'No' }}</dd>
                    </dl>
                </div>
            </div>

            @if($agent->website_url)
            <a href="{{ $agent->website_url }}" target="_blank" rel="noopener noreferrer"
               class="btn btn-primary w-100 mb-2">
                <i class="fas fa-external-link-alt me-2"></i>Sitio oficial
            </a>
            @endif

            @if($agent->github_url)
            <a href="{{ $agent->github_url }}" target="_blank" rel="noopener noreferrer"
               class="btn btn-outline-secondary w-100 mb-4">
                <i class="fab fa-github me-2"></i>Ver en GitHub
                @if($agent->stars_github)
                    <span class="badge bg-secondary ms-1">{{ $agent->formatted_stars }}</span>
                @endif
            </a>
            @endif

            {{-- Relacionados --}}
            @if($related->isNotEmpty())
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Agentes similares</h6>
                    @foreach($related as $rel)
                    <a href="{{ route('agents.show', $rel) }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark mb-3">
                        @if($rel->logo)
                            <img src="{{ $rel->logo }}" alt="{{ $rel->name }}" style="width:36px;height:36px;object-fit:contain;border-radius:6px;">
                        @else
                            <div style="width:36px;height:36px;background:var(--primary-color);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-robot text-white" style="font-size:.7rem;"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-semibold" style="font-size:.88rem;">{{ $rel->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($rel->tagline, 50) }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver al directorio
        </a>
    </div>
</div>
@endsection
