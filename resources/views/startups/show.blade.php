@extends('layouts.app')

@section('title', $startup->name . ' — Startups IA · ConocIA')
@section('meta_description', $startup->tagline ?? 'Perfil de startup de IA en ConocIA')

@php
    $startupUrl = route('startups.show', $startup);
    $shareTitle = $startup->name . ': ' . ($startup->tagline ?: 'startup de IA destacada en ConocIA');
@endphp

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">

            {{-- Encabezado --}}
            <div class="d-flex align-items-start gap-4 mb-4">
                @if($startup->logo)
                    <img src="{{ $startup->logo }}" alt="{{ $startup->name }}" style="width:72px;height:72px;object-fit:contain;border-radius:12px;border:1px solid #e5e7eb;">
                @else
                    <div style="width:72px;height:72px;background:var(--primary-color);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-rocket fa-2x text-white"></i>
                    </div>
                @endif
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:1.8rem;">{{ $startup->name }}</h1>
                    @if($startup->tagline)
                        <p class="text-muted mb-2">{{ $startup->tagline }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        @if($startup->stage)
                        <span class="badge" style="background:{{ $startup->stage_color }};">{{ $startup->stage_label }}</span>
                        @endif
                        @if($startup->sector)
                        <span class="badge bg-secondary">{{ \App\Models\Startup::sectorLabels()[$startup->sector] ?? $startup->sector }}</span>
                        @endif
                        @if($startup->country)
                        <span class="badge bg-light text-dark border"><i class="fas fa-map-marker-alt me-1"></i>{{ $startup->city ? $startup->city.', ' : '' }}{{ $startup->country }}</span>
                        @endif
                        @if($startup->founded_year)
                        <span class="badge bg-light text-dark border"><i class="fas fa-calendar me-1"></i>{{ $startup->founded_year }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Perfil profundo (si existe) o descripción básica --}}
            @if($startup->profile_content)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4 startup-profile-content" style="line-height:1.8;">
                    {!! $startup->profile_content !!}
                </div>
            </div>
            @elseif($startup->description)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3">Sobre la empresa</h5>
                    <p class="mb-0" style="line-height:1.7;">{{ $startup->description }}</p>
                </div>
            </div>
            @endif

            {{-- Productos --}}
            @if(!empty($startup->products))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-box me-2" style="color:var(--primary-color);"></i>Productos</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach($startup->products as $product)
                        <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>{{ $product }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Inversores --}}
            @if(!empty($startup->investors))
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="fas fa-handshake me-2" style="color:var(--primary-color);"></i>Inversores</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($startup->investors as $investor)
                        <span class="badge bg-light text-dark border px-3 py-2" style="font-size:.85rem;">{{ $investor }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">Datos clave</h6>
                    <dl class="mb-0">
                        @if($startup->total_funding_usd)
                        <dt class="text-muted" style="font-size:.8rem;">Financiamiento total</dt>
                        <dd class="fw-bold mb-2" style="font-size:1.1rem;">{{ $startup->funding_label }}</dd>
                        @endif
                        @if($startup->last_funding_date)
                        <dt class="text-muted" style="font-size:.8rem;">Última ronda</dt>
                        <dd class="mb-2">{{ $startup->last_funding_date->locale('es')->isoFormat('MMM YYYY') }}</dd>
                        @endif
                        @if($startup->founded_year)
                        <dt class="text-muted" style="font-size:.8rem;">Fundada en</dt>
                        <dd class="mb-2">{{ $startup->founded_year }}</dd>
                        @endif
                        @if($startup->country)
                        <dt class="text-muted" style="font-size:.8rem;">Sede</dt>
                        <dd class="mb-0">{{ $startup->city ? $startup->city.', ' : '' }}{{ $startup->country }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em;">
                        <i class="fas fa-share-alt me-2"></i>Compartir startup
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($startupUrl) }}&text={{ urlencode($shareTitle) }}"
                           target="_blank" rel="noopener"
                           class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                           title="Compartir en X">
                            <i class="fab fa-twitter me-1"></i>X
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode($startupUrl) }}&title={{ urlencode($shareTitle) }}"
                           target="_blank" rel="noopener"
                           class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                           title="Compartir en LinkedIn">
                            <i class="fab fa-linkedin-in me-1"></i>LinkedIn
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($shareTitle . ' ' . $startupUrl) }}"
                           target="_blank" rel="noopener"
                           class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                           title="Compartir en WhatsApp">
                            <i class="fab fa-whatsapp me-1"></i>WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($startupUrl) }}"
                           target="_blank" rel="noopener"
                           class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                           title="Compartir en Facebook">
                            <i class="fab fa-facebook-f me-1"></i>Facebook
                        </a>
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                onclick='copyStartupLink(@json($startupUrl), this)'
                                title="Copiar enlace">
                            <i class="fas fa-link me-1"></i><span>Copiar</span>
                        </button>
                    </div>
                </div>
            </div>

            @if($startup->website_url)
            <a href="{{ $startup->website_url }}" target="_blank" rel="noopener noreferrer"
               class="btn btn-primary w-100 mb-3">
                <i class="fas fa-external-link-alt me-2"></i>Visitar sitio web
            </a>
            @endif

            @if($startup->source_url)
            <a href="{{ $startup->source_url }}" target="_blank" rel="noopener noreferrer"
               class="btn btn-outline-secondary w-100 btn-sm mb-4">
                <i class="fas fa-newspaper me-2"></i>Ver noticia fuente
            </a>
            @endif

            {{-- Relacionadas --}}
            @if($related->isNotEmpty())
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Startups similares</h6>
                    @foreach($related as $rel)
                    <a href="{{ route('startups.show', $rel) }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark mb-3">
                        @if($rel->logo)
                            <img src="{{ $rel->logo }}" alt="{{ $rel->name }}" style="width:36px;height:36px;object-fit:contain;border-radius:6px;">
                        @else
                            <div style="width:36px;height:36px;background:var(--primary-color);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-rocket text-white" style="font-size:.7rem;"></i>
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
        <a href="{{ route('startups.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver al directorio
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyStartupLink(url, button) {
    const done = function () {
        const icon = button.querySelector('i');
        const label = button.querySelector('span');
        const originalIcon = icon ? icon.className : '';
        const originalLabel = label ? label.textContent : '';

        if (icon) icon.className = 'fas fa-check me-1';
        if (label) label.textContent = 'Copiado';

        window.setTimeout(function () {
            if (icon) icon.className = originalIcon;
            if (label) label.textContent = originalLabel;
        }, 1600);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(done);
        return;
    }

    const input = document.createElement('textarea');
    input.value = url;
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.focus();
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    done();
}
</script>
@endpush
