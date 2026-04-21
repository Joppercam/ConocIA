@if(isset($startupOfWeek) && $startupOfWeek)
@php
    $s = $startupOfWeek;
    $sectorLabels = \App\Models\Startup::sectorLabels();
@endphp
<section style="background:linear-gradient(135deg,#0a1020 0%,#0f1e38 100%);border-top:1px solid #1e2430;border-bottom:1px solid #1e2430;">
    <div class="container py-4">

        {{-- Header de la sección --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                     style="width:36px;height:36px;background:rgba(56,182,255,.15);">
                    <i class="fas fa-rocket" style="color:var(--primary-color);font-size:.9rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white" style="font-size:.95rem;line-height:1.1;">Startup de la semana</div>
                    <div style="font-size:.72rem;color:#64748b;">Perfil editorial · {{ now()->startOfWeek()->format('d M') }} – {{ now()->endOfWeek()->format('d M Y') }}</div>
                </div>
            </div>
            <a href="{{ route('startups.show', $s) }}"
               class="btn btn-sm btn-outline-light rounded-pill px-3"
               style="font-size:.75rem;border-color:rgba(255,255,255,.2);">
                Leer perfil completo <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Card principal --}}
        <div class="row g-0 rounded-3 overflow-hidden" style="background:#0d1524;border:1px solid #1e2d47;">

            {{-- Logo / visual --}}
            <div class="col-md-3 d-flex align-items-center justify-content-center p-4"
                 style="background:linear-gradient(135deg,#0f1e38,#162040);border-right:1px solid #1e2d47;min-height:180px;">
                @if($s->logo)
                    <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                         style="max-width:110px;max-height:110px;object-fit:contain;filter:drop-shadow(0 4px 16px rgba(56,182,255,.25));">
                @else
                    <div style="width:80px;height:80px;background:rgba(56,182,255,.12);border-radius:20px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(56,182,255,.2);">
                        <i class="fas fa-rocket fa-2x" style="color:var(--primary-color);"></i>
                    </div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="col-md-9 p-4">
                <div class="d-flex flex-wrap align-items-start gap-2 mb-2">
                    <h2 class="text-white fw-bold mb-0 me-2" style="font-size:1.4rem;">{{ $s->name }}</h2>
                    @if($s->stage)
                    <span class="badge align-self-center" style="background:{{ $s->stage_color }};font-size:.72rem;">{{ $s->stage_label }}</span>
                    @endif
                    @if($s->sector)
                    <span class="badge bg-secondary align-self-center" style="font-size:.72rem;">{{ $sectorLabels[$s->sector] ?? $s->sector }}</span>
                    @endif
                    @if($s->country)
                    <span class="badge align-self-center" style="background:rgba(255,255,255,.08);color:#94a3b8;font-size:.72rem;">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $s->country }}
                    </span>
                    @endif
                </div>

                @if($s->why_it_matters)
                <p style="color:#94a3b8;font-size:.9rem;line-height:1.6;margin-bottom:1rem;">{{ $s->why_it_matters }}</p>
                @elseif($s->tagline)
                <p style="color:#94a3b8;font-size:.9rem;line-height:1.6;margin-bottom:1rem;">{{ $s->tagline }}</p>
                @endif

                @if($s->key_quote)
                <div style="border-left:3px solid var(--primary-color);padding:.5rem 1rem;margin-bottom:1rem;background:rgba(56,182,255,.05);border-radius:0 6px 6px 0;">
                    <p style="color:#cbd5e1;font-size:.85rem;font-style:italic;margin-bottom:0;">"{{ $s->key_quote }}"</p>
                </div>
                @endif

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($s->total_funding_usd)
                    <span style="color:#64748b;font-size:.78rem;">
                        <i class="fas fa-dollar-sign me-1" style="color:var(--primary-color);"></i>
                        <strong style="color:#94a3b8;">{{ $s->funding_label }}</strong> recaudados
                    </span>
                    @endif
                    @if($s->founded_year)
                    <span style="color:#64748b;font-size:.78rem;">
                        <i class="fas fa-calendar me-1"></i>Fundada en {{ $s->founded_year }}
                    </span>
                    @endif
                    @if(!empty($s->founder_names))
                    <span style="color:#64748b;font-size:.78rem;">
                        <i class="fas fa-users me-1"></i>{{ implode(', ', array_slice((array)$s->founder_names, 0, 2)) }}
                    </span>
                    @endif

                    <a href="{{ route('startups.show', $s) }}"
                       class="ms-auto btn btn-sm rounded-pill px-3"
                       style="background:var(--primary-color);color:#fff;font-size:.78rem;">
                        Leer perfil completo <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
@endif
