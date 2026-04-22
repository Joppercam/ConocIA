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
                <div class="d-lg-none mb-1">
                    <span class="badge rounded-pill px-2 py-1" style="background:#00c896;color:#fff;font-size:.62rem;">
                        <i class="fas fa-rocket me-1"></i>Startup de la semana
                    </span>
                </div>

                {{-- Nombre + badges (badges ocultos en móvil) --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-bold" style="font-size:1rem;color:#0f172a;">{{ $s->name }}</span>
                    @if($s->stage)
                    <span class="badge d-none d-sm-inline" style="background:{{ $s->stage_color }};font-size:.68rem;">{{ $s->stage_label }}</span>
                    @endif
                    @if($s->sector)
                    <span class="badge bg-light text-secondary border d-none d-sm-inline" style="font-size:.68rem;">{{ $sectorLabels[$s->sector] ?? $s->sector }}</span>
                    @endif
                </div>

                {{-- Tagline / why it matters --}}
                @if($s->why_it_matters)
                <p class="mb-1" style="color:#475569;font-size:.85rem;line-height:1.5;max-width:600px;">{{ Str::limit($s->why_it_matters, 120) }}</p>
                @elseif($s->tagline)
                <p class="mb-1" style="color:#475569;font-size:.85rem;line-height:1.5;max-width:600px;">{{ Str::limit($s->tagline, 120) }}</p>
                @endif

                {{-- Metadatos: ocultos en móvil --}}
                <div class="d-none d-sm-flex flex-wrap align-items-center gap-3 mt-1">
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
                   class="btn btn-sm rounded-pill fw-semibold px-3 px-sm-4"
                   style="background:#00c896;color:#fff;font-size:.8rem;white-space:nowrap;">
                    <span class="d-none d-sm-inline">Leer perfil </span><i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>

@elseif(isset($recentStartups) && $recentStartups->isNotEmpty())
@php $sectorLabels = \App\Models\Startup::sectorLabels(); @endphp
<div style="background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:3px solid #00c896;">
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="fw-bold text-uppercase" style="font-size:.7rem;letter-spacing:.07em;color:#00c896;">
                <i class="fas fa-rocket me-2"></i>Startups de IA destacadas
            </span>
            <a href="{{ route('startups.index') }}" class="text-decoration-none" style="color:#64748b;font-size:.75rem;">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($recentStartups as $s)
            <div class="col-md-4">
                <a href="{{ route('startups.show', $s) }}" class="text-decoration-none d-block h-100">
                    <div class="h-100 rounded-2 p-3 d-flex gap-3 align-items-start"
                         style="background:#fff;border:1px solid #e2e8f0;transition:border-color .2s;"
                         onmouseover="this.style.borderColor='#00c896'" onmouseout="this.style.borderColor='#e2e8f0'">
                        @if($s->logo)
                            <img src="{{ $s->logo }}" alt="{{ $s->name }}"
                                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;border:1px solid #e2e8f0;background:#fff;flex-shrink:0;">
                        @else
                            <div style="width:40px;height:40px;background:#ecfdf5;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px solid #d1fae5;flex-shrink:0;">
                                <i class="fas fa-rocket" style="color:#00c896;font-size:.85rem;"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-semibold mb-1" style="font-size:.88rem;color:#0f172a;">{{ $s->name }}</div>
                            @if($s->tagline)
                            <div style="font-size:.75rem;color:#64748b;line-height:1.4;">{{ Str::limit($s->tagline, 70) }}</div>
                            @endif
                            @if($s->sector)
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:.62rem;">{{ $sectorLabels[$s->sector] ?? $s->sector }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
