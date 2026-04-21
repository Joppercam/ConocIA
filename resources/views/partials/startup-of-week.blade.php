@if(isset($startupOfWeek) && $startupOfWeek)
@php $s = $startupOfWeek; @endphp
<div style="background:#0d1117;border-bottom:1px solid #1e2430;">
    <div class="container">
        <a href="{{ route('startups.show', $s) }}" class="text-decoration-none d-flex align-items-center gap-3 px-0 py-3"
           style="transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">

            {{-- Etiqueta --}}
            <div class="flex-shrink-0 d-none d-md-flex align-items-center justify-content-center rounded-2"
                 style="width:40px;height:40px;background:rgba(56,182,255,.12);">
                <i class="fas fa-rocket" style="color:var(--primary-color);font-size:.85rem;"></i>
            </div>

            {{-- Label semana --}}
            <div class="flex-shrink-0 d-none d-lg-block" style="min-width:130px;">
                <div class="fw-bold text-white" style="font-size:.78rem;line-height:1.2;">Startup de la semana</div>
                <div style="font-size:.68rem;color:#475569;">{{ now()->startOfWeek()->format('d M') }} – {{ now()->endOfWeek()->format('d M') }}</div>
            </div>

            {{-- Separador --}}
            <div class="d-none d-lg-block flex-shrink-0" style="width:1px;height:28px;background:#1e2430;"></div>

            {{-- Logo --}}
            @if($s->logo)
            <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                 style="width:28px;height:28px;object-fit:contain;border-radius:6px;flex-shrink:0;">
            @endif

            {{-- Nombre + tagline --}}
            <div class="flex-grow-1 min-width-0">
                <span class="fw-semibold text-white me-2" style="font-size:.88rem;">{{ $s->name }}</span>
                @if($s->stage)
                <span class="badge me-1" style="background:{{ $s->stage_color }};font-size:.65rem;">{{ $s->stage_label }}</span>
                @endif
                @if($s->tagline)
                <span class="d-none d-md-inline" style="color:#64748b;font-size:.82rem;">— {{ Str::limit($s->tagline, 80) }}</span>
                @endif
            </div>

            {{-- CTA --}}
            <span class="flex-shrink-0 btn btn-sm rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1"
                  style="background:rgba(56,182,255,.12);color:var(--primary-color);font-size:.75rem;border:1px solid rgba(56,182,255,.2);">
                Ver perfil <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
            </span>

        </a>
    </div>
</div>
@endif
