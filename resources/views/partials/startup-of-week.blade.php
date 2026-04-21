@if(isset($startupOfWeek) && $startupOfWeek)
@php $s = $startupOfWeek; @endphp
<div style="background:linear-gradient(90deg,#0a1f16 0%,#091a26 100%);border-bottom:1px solid rgba(0,200,150,.25);">
    <div class="container">
        <a href="{{ route('startups.show', $s) }}" class="text-decoration-none d-flex align-items-center gap-3 py-3"
           style="transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">

            {{-- Pill etiqueta --}}
            <span class="flex-shrink-0 fw-bold rounded-pill px-3 py-1 d-none d-sm-inline text-nowrap"
                  style="background:#00c896;color:#000;font-size:.68rem;letter-spacing:.04em;">
                <i class="fas fa-rocket me-1"></i>Startup de la semana
            </span>
            <span class="flex-shrink-0 fw-bold rounded-pill px-2 py-1 d-sm-none"
                  style="background:#00c896;color:#000;font-size:.68rem;">
                <i class="fas fa-rocket"></i>
            </span>

            {{-- Logo --}}
            @if($s->logo)
            <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                 style="width:26px;height:26px;object-fit:contain;border-radius:5px;flex-shrink:0;">
            @endif

            {{-- Nombre + info --}}
            <div class="flex-grow-1 min-width-0">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-white" style="font-size:.92rem;">{{ $s->name }}</span>
                    @if($s->stage)
                    <span class="badge" style="background:{{ $s->stage_color }};font-size:.65rem;">{{ $s->stage_label }}</span>
                    @endif
                    @if($s->total_funding_usd)
                    <span class="d-none d-lg-inline" style="color:#6ee7b7;font-size:.78rem;">{{ $s->funding_label }}</span>
                    @endif
                </div>
                @if($s->tagline)
                <div class="text-truncate d-none d-md-block" style="color:#6ee7b7;font-size:.8rem;margin-top:1px;">{{ $s->tagline }}</div>
                @endif
            </div>

            {{-- CTA --}}
            <span class="flex-shrink-0 d-none d-sm-inline fw-semibold" style="color:#00c896;font-size:.8rem;white-space:nowrap;">
                Leer perfil <i class="fas fa-arrow-right ms-1" style="font-size:.65rem;"></i>
            </span>

        </a>
    </div>
</div>
@endif
