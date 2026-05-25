@extends('layouts.app')

@section('title', 'Mapa del Ecosistema IA en Chile — Universidades, Startups y Centros | ConocIA')
@section('meta_description', 'Directorio de ' . $total . ' actores clave del ecosistema de inteligencia artificial en Chile: universidades, centros de investigación, startups y organismos públicos.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">IA EN CHILE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Mapa del Ecosistema IA en Chile</h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;">
                    Universidades, centros de investigación, startups, instituciones gubernamentales y organizaciones que dan forma al ecosistema de inteligencia artificial en Chile.
                </p>
            </div>
            {{-- Stats --}}
            <div class="col-lg-4">
                <div class="row g-2">
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:1rem;" class="text-center">
                            <div class="fw-bold text-white" style="font-size:1.8rem;line-height:1;">{{ $total }}</div>
                            <div style="color:#64748b;font-size:.75rem;margin-top:3px;">actores mapeados</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:1rem;" class="text-center">
                            <div class="fw-bold" style="color:#38b6ff;font-size:1.8rem;line-height:1;">{{ $academicos }}</div>
                            <div style="color:#64748b;font-size:.75rem;margin-top:3px;">inst. académicas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:.75rem;" class="text-center">
                            <div class="fw-bold" style="color:#00c896;font-size:1.4rem;line-height:1;">{{ $typeCounts['startup'] }}</div>
                            <div style="color:#64748b;font-size:.72rem;margin-top:3px;">startups</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:.75rem;" class="text-center">
                            <div class="fw-bold" style="color:#f59e0b;font-size:1.4rem;line-height:1;">{{ $typeCounts['gobierno'] }}</div>
                            <div style="color:#64748b;font-size:.72rem;margin-top:3px;">gobierno</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:.75rem;" class="text-center">
                            <div class="fw-bold" style="color:#f472b6;font-size:1.4rem;line-height:1;">{{ $typeCounts['organizacion'] }}</div>
                            <div style="color:#64748b;font-size:.72rem;margin-top:3px;">organ.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- Sidebar navegación --}}
        <div class="col-lg-3">
            <nav style="position:sticky;top:88px;z-index:10;">
                <p class="text-uppercase fw-semibold mb-2" style="color:#94a3b8;font-size:.72rem;letter-spacing:.08em;">Explorar por categoría</p>

                <div class="d-flex flex-column gap-1">
                    <button class="eco-nav-btn active" data-target="todos">
                        <span class="flex-grow-1 text-start">Todos</span>
                        <span class="eco-count">{{ $total }}</span>
                    </button>
                    <button class="eco-nav-btn" data-target="universidad">
                        <span class="eco-dot" style="background:#38b6ff;"></span>
                        <span class="flex-grow-1 text-start">Universidades</span>
                        <span class="eco-count">{{ $typeCounts['universidad'] }}</span>
                    </button>
                    <button class="eco-nav-btn" data-target="centro_investigacion">
                        <span class="eco-dot" style="background:#a78bfa;"></span>
                        <span class="flex-grow-1 text-start">C. de Investigación</span>
                        <span class="eco-count">{{ $typeCounts['centro_investigacion'] }}</span>
                    </button>
                    <button class="eco-nav-btn" data-target="startup">
                        <span class="eco-dot" style="background:#00c896;"></span>
                        <span class="flex-grow-1 text-start">Startups</span>
                        <span class="eco-count">{{ $typeCounts['startup'] }}</span>
                    </button>
                    <button class="eco-nav-btn" data-target="gobierno">
                        <span class="eco-dot" style="background:#f59e0b;"></span>
                        <span class="flex-grow-1 text-start">Gobierno</span>
                        <span class="eco-count">{{ $typeCounts['gobierno'] }}</span>
                    </button>
                    <button class="eco-nav-btn" data-target="organizacion">
                        <span class="eco-dot" style="background:#f472b6;"></span>
                        <span class="flex-grow-1 text-start">Organizaciones</span>
                        <span class="eco-count">{{ $typeCounts['organizacion'] }}</span>
                    </button>
                </div>

                <div class="mt-4 pt-3" style="border-top:1px solid #f1f5f9;">
                    <p style="color:#94a3b8;font-size:.78rem;line-height:1.6;margin:0;">
                        ¿Conoces un actor que no está aquí? Escríbenos a
                        <a href="mailto:contacto@conocia.cl" style="color:var(--primary-color);">contacto@conocia.cl</a>
                    </p>
                </div>
            </nav>
        </div>

        {{-- Paneles de contenido --}}
        <div class="col-lg-9">

            {{-- Panel: Todos --}}
            <div data-panel="todos">
                @php $prevType = null; @endphp
                @foreach($actors as $actor)
                    @if($actor->type !== $prevType)
                        @if($prevType !== null)
                            <div style="height:2rem;"></div>
                        @endif
                        <div class="d-flex align-items-center gap-2 mb-3" style="padding-bottom:.5rem;border-bottom:2px solid rgba(56,182,255,.12);">
                            <span class="fw-bold" style="color:#0f172a;font-size:.92rem;">{{ $actor->type_label }}</span>
                            <span style="color:#94a3b8;font-size:.78rem;">— {{ $actors->where('type', $actor->type)->count() }} actores</span>
                        </div>
                        @php $prevType = $actor->type; @endphp
                    @endif

                    <div class="eco-actor-row">
                        <div class="d-flex align-items-start gap-3">
                            <div style="width:40px;height:40px;background:{{ $actor->type_color }}18;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.1rem;">
                                @if($actor->type === 'universidad')
                                    <i class="fas fa-graduation-cap" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                                @elseif($actor->type === 'centro_investigacion')
                                    <i class="fas fa-flask" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                                @elseif($actor->type === 'startup')
                                    <i class="fas fa-rocket" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                                @elseif($actor->type === 'gobierno')
                                    <i class="fas fa-landmark" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                                @else
                                    <i class="fas fa-handshake" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1" style="color:#94a3b8;font-size:.78rem;">
                                    <span><i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>{{ $actor->location }}@if($actor->region && $actor->region !== 'Metropolitana'), {{ $actor->region }}@endif</span>
                                    @if($actor->founded)
                                        <span><i class="fas fa-calendar me-1"></i>{{ $actor->founded }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('ecosistema.show', $actor->slug) }}" class="eco-actor-name d-block mb-2">{{ $actor->name }}</a>
                                <p style="color:#475569;font-size:.88rem;line-height:1.65;margin:0 0 .75rem;">{{ $actor->excerpt }}</p>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($actor->focus_areas)
                                        @foreach(array_slice($actor->focus_areas, 0, 3) as $area)
                                            <span style="background:#f1f5f9;color:#475569;font-size:.7rem;font-weight:500;padding:.2rem .55rem;border-radius:999px;">{{ $area }}</span>
                                        @endforeach
                                        @if(count($actor->focus_areas) > 3)
                                            <span style="background:#f1f5f9;color:#94a3b8;font-size:.7rem;padding:.2rem .55rem;border-radius:999px;">+{{ count($actor->focus_areas) - 3 }}</span>
                                        @endif
                                    @endif
                                    <a href="{{ route('ecosistema.show', $actor->slug) }}" style="margin-left:auto;color:var(--primary-color);font-size:.82rem;font-weight:600;text-decoration:none;white-space:nowrap;">
                                        Ver ficha <i class="fas fa-arrow-right ms-1" style="font-size:.75rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @php
            $panelDefs = [
                'universidad'          => ['label' => 'Universidades',           'count_key' => 'universidad',          'icon' => 'fa-graduation-cap', 'color' => '#38b6ff', 'suffix' => 'instituciones'],
                'centro_investigacion' => ['label' => 'Centros de Investigación','count_key' => 'centro_investigacion',  'icon' => 'fa-flask',           'color' => '#a78bfa', 'suffix' => 'centros'],
                'startup'              => ['label' => 'Startups',                'count_key' => 'startup',               'icon' => 'fa-rocket',          'color' => '#00c896', 'suffix' => 'empresas'],
                'gobierno'             => ['label' => 'Gobierno',                'count_key' => 'gobierno',              'icon' => 'fa-landmark',        'color' => '#f59e0b', 'suffix' => 'instituciones'],
                'organizacion'         => ['label' => 'Organizaciones',          'count_key' => 'organizacion',          'icon' => 'fa-handshake',       'color' => '#f472b6', 'suffix' => 'organizaciones'],
            ];
            @endphp

            @foreach($panelDefs as $type => $def)
            <div data-panel="{{ $type }}" style="display:none;">
                <div class="d-flex align-items-center gap-2 mb-4" style="padding-bottom:.75rem;border-bottom:2px solid rgba(56,182,255,.12);">
                    <div style="width:36px;height:36px;background:{{ $def['color'] }}18;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                        <i class="fas {{ $def['icon'] }}" style="color:{{ $def['color'] }};font-size:.9rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-0" style="color:#0f172a;font-size:1.15rem;">{{ $def['label'] }}</h2>
                    <span style="color:#94a3b8;font-size:.85rem;">{{ $typeCounts[$def['count_key']] }} {{ $def['suffix'] }}</span>
                </div>

                @foreach($actors->where('type', $type) as $actor)
                <div class="eco-actor-row">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width:40px;height:40px;background:{{ $actor->type_color }}18;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.1rem;">
                            <i class="fas {{ $def['icon'] }}" style="color:{{ $actor->type_color }};font-size:.9rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1" style="color:#94a3b8;font-size:.78rem;">
                                <span><i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>{{ $actor->location }}@if($actor->region && $actor->region !== 'Metropolitana'), {{ $actor->region }}@endif</span>
                                @if($actor->founded)
                                    <span><i class="fas fa-calendar me-1"></i>{{ $actor->founded }}</span>
                                @endif
                            </div>
                            <a href="{{ route('ecosistema.show', $actor->slug) }}" class="eco-actor-name d-block mb-2">{{ $actor->name }}</a>
                            <p style="color:#475569;font-size:.88rem;line-height:1.65;margin:0 0 .75rem;">{{ $actor->excerpt }}</p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if($actor->focus_areas)
                                    @foreach(array_slice($actor->focus_areas, 0, 3) as $area)
                                        <span style="background:#f1f5f9;color:#475569;font-size:.7rem;font-weight:500;padding:.2rem .55rem;border-radius:999px;">{{ $area }}</span>
                                    @endforeach
                                    @if(count($actor->focus_areas) > 3)
                                        <span style="background:#f1f5f9;color:#94a3b8;font-size:.7rem;padding:.2rem .55rem;border-radius:999px;">+{{ count($actor->focus_areas) - 3 }}</span>
                                    @endif
                                @endif
                                <a href="{{ route('ecosistema.show', $actor->slug) }}" style="margin-left:auto;color:var(--primary-color);font-size:.82rem;font-weight:600;text-decoration:none;white-space:nowrap;">
                                    Ver ficha <i class="fas fa-arrow-right ms-1" style="font-size:.75rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
    </div>
</div>

@push('styles')
<style>
.eco-nav-btn {
    display: flex;
    align-items: center;
    gap: .5rem;
    width: 100%;
    padding: .6rem .85rem;
    background: transparent;
    border: 1px solid transparent;
    border-radius: .5rem;
    cursor: pointer;
    font-size: .88rem;
    color: #475569;
    transition: all .15s;
}
.eco-nav-btn:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #0f172a;
}
.eco-nav-btn.active {
    background: rgba(56,182,255,.08);
    border-color: rgba(56,182,255,.3);
    color: #0f172a;
    font-weight: 600;
}
.eco-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.eco-count {
    background: #f1f5f9;
    color: #64748b;
    font-size: .72rem;
    font-weight: 600;
    padding: .1rem .45rem;
    border-radius: 999px;
    flex-shrink: 0;
}
.eco-nav-btn.active .eco-count {
    background: rgba(56,182,255,.15);
    color: #0369a1;
}
.eco-actor-row {
    padding: 1.25rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.eco-actor-row:last-child {
    border-bottom: none;
}
.eco-actor-name {
    font-size: .97rem;
    font-weight: 700;
    color: #0f172a;
    text-decoration: none;
    line-height: 1.35;
}
.eco-actor-name:hover {
    color: var(--primary-color);
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const panels = document.querySelectorAll('[data-panel]');
    const links  = document.querySelectorAll('[data-target]');

    function show(target) {
        panels.forEach(p => p.style.display = p.dataset.panel === target ? '' : 'none');
        links.forEach(l => l.classList.toggle('active', l.dataset.target === target));
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    links.forEach(link => link.addEventListener('click', () => show(link.dataset.target)));
})();
</script>
@endpush

@endsection
