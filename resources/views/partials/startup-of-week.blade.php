@if(isset($startupOfWeek) && $startupOfWeek)
@php
    $s = $startupOfWeek;
    $sectorLabels = \App\Models\Startup::sectorLabels();
@endphp
<div style="background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:3px solid #00c896;">
    <div class="container py-4">
        <div class="row align-items-center g-4">

            {{-- Etiqueta lateral --}}
            <div class="col-auto d-none d-lg-flex flex-column align-items-center" style="min-width:90px;">
                <span class="fw-bold text-uppercase rounded-pill px-3 py-2 mb-2 text-center"
                      style="background:#00c896;color:#fff;font-size:.65rem;letter-spacing:.06em;line-height:1.3;white-space:nowrap;">
                    <i class="fas fa-rocket d-block mb-1" style="font-size:.9rem;"></i>
                    Startup<br>de la semana
                </span>
                <span style="color:#94a3b8;font-size:.65rem;text-align:center;">
                    {{ now()->startOfWeek()->format('d M') }}<br>– {{ now()->endOfWeek()->format('d M') }}
                </span>
            </div>

            {{-- Separador --}}
            <div class="col-auto d-none d-lg-block px-0">
                <div style="width:1px;height:70px;background:#e2e8f0;"></div>
            </div>

            {{-- Logo + nombre --}}
            <div class="col-auto">
                @if($s->logo)
                    <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                         style="width:52px;height:52px;object-fit:contain;border-radius:10px;border:1px solid #e2e8f0;background:#fff;padding:4px;">
                @else
                    <div style="width:52px;height:52px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;border:1px solid #d1fae5;">
                        <i class="fas fa-rocket" style="color:#00c896;font-size:1.1rem;"></i>
                    </div>
                @endif
            </div>

            {{-- Contenido principal --}}
            <div class="col">
                {{-- Mobile label --}}
                <div class="d-lg-none mb-2">
                    <span class="badge rounded-pill px-2 py-1" style="background:#00c896;color:#fff;font-size:.65rem;">
                        <i class="fas fa-rocket me-1"></i>Startup de la semana
                    </span>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-bold" style="font-size:1.05rem;color:#0f172a;">{{ $s->name }}</span>
                    @if($s->stage)
                    <span class="badge" style="background:{{ $s->stage_color }};font-size:.68rem;">{{ $s->stage_label }}</span>
                    @endif
                    @if($s->sector)
                    <span class="badge bg-light text-secondary border" style="font-size:.68rem;">{{ $sectorLabels[$s->sector] ?? $s->sector }}</span>
                    @endif
                    @if($s->country)
                    <span style="color:#94a3b8;font-size:.75rem;"><i class="fas fa-map-marker-alt me-1"></i>{{ $s->country }}</span>
                    @endif
                </div>

                @if($s->why_it_matters)
                <p class="mb-2" style="color:#475569;font-size:.88rem;line-height:1.6;max-width:600px;">{{ $s->why_it_matters }}</p>
                @elseif($s->tagline)
                <p class="mb-2" style="color:#475569;font-size:.88rem;line-height:1.6;max-width:600px;">{{ $s->tagline }}</p>
                @endif

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($s->total_funding_usd)
                    <span style="color:#64748b;font-size:.78rem;">
                        <i class="fas fa-dollar-sign me-1" style="color:#00c896;"></i>
                        <strong style="color:#334155;">{{ $s->funding_label }}</strong> recaudados
                    </span>
                    @endif
                    @if($s->founded_year)
                    <span style="color:#94a3b8;font-size:.75rem;">
                        <i class="fas fa-calendar me-1"></i>Fundada en {{ $s->founded_year }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- CTA --}}
            <div class="col-auto">
                <a href="{{ route('startups.show', $s) }}"
                   class="btn btn-sm rounded-pill fw-semibold px-4"
                   style="background:#00c896;color:#fff;font-size:.82rem;white-space:nowrap;">
                    Leer perfil <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endif
