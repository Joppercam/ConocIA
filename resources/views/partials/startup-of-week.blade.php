@if(isset($startupOfWeek) && $startupOfWeek)
@php $s = $startupOfWeek; @endphp
<div style="background:linear-gradient(90deg,#0f2a1e 0%,#0a1f2e 100%);border-bottom:1px solid rgba(0,200,150,.2);">
    <div class="container">
        <a href="{{ route('startups.show', $s) }}" class="text-decoration-none d-flex align-items-center gap-3 py-2"
           style="transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">

            {{-- Pill etiqueta --}}
            <span class="flex-shrink-0 fw-bold text-uppercase rounded-pill px-2 py-1 d-none d-sm-inline"
                  style="background:#00c896;color:#000;font-size:.62rem;letter-spacing:.06em;line-height:1;">
                <i class="fas fa-rocket me-1"></i>Startup semana
            </span>
            <span class="flex-shrink-0 fw-bold text-uppercase rounded-pill px-2 py-1 d-sm-none"
                  style="background:#00c896;color:#000;font-size:.62rem;letter-spacing:.06em;line-height:1;">
                <i class="fas fa-rocket"></i>
            </span>

            {{-- Logo --}}
            @if($s->logo)
            <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                 style="width:22px;height:22px;object-fit:contain;border-radius:4px;flex-shrink:0;opacity:.9;">
            @endif

            {{-- Nombre + tagline --}}
            <div class="flex-grow-1 min-width-0 d-flex align-items-center gap-2 overflow-hidden">
                <span class="fw-semibold text-white text-nowrap" style="font-size:.85rem;">{{ $s->name }}</span>
                @if($s->stage)
                <span class="badge flex-shrink-0" style="background:{{ $s->stage_color }};font-size:.62rem;">{{ $s->stage_label }}</span>
                @endif
                @if($s->tagline)
                <span class="text-truncate d-none d-md-inline" style="color:#6ee7b7;font-size:.8rem;">— {{ $s->tagline }}</span>
                @endif
            </div>

            {{-- CTA --}}
            <span class="flex-shrink-0 d-none d-sm-inline" style="color:#00c896;font-size:.78rem;white-space:nowrap;">
                Ver perfil <i class="fas fa-arrow-right ms-1" style="font-size:.65rem;"></i>
            </span>

        </a>
    </div>
</div>
@endif
