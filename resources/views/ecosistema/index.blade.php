@extends('layouts.app')

@section('title', 'Mapa del Ecosistema IA en Chile — Universidades, Startups y Centros | ConocIA')
@section('meta_description', 'Directorio de los ' . $total . ' actores clave del ecosistema de inteligencia artificial en Chile: universidades, centros de investigación, startups y organismos públicos.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">IA EN CHILE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Mapa del Ecosistema IA en Chile</h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;">
                    Universidades, centros de investigación, startups, instituciones gubernamentales y organizaciones que dan forma al ecosistema de inteligencia artificial en Chile.
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2rem;min-width:180px;" class="text-center">
                    <div class="fw-bold text-white" style="font-size:2.5rem;line-height:1;">{{ $total }}</div>
                    <div style="color:#64748b;font-size:.88rem;margin-top:.3rem;">actores mapeados</div>
                    <div class="mt-3 d-flex flex-wrap justify-content-center gap-2" style="font-size:.72rem;">
                        <span style="color:#38b6ff;">{{ $typeCounts['universidad'] }} Univ.</span>
                        <span style="color:#a78bfa;">{{ $typeCounts['centro_investigacion'] }} Centros</span>
                        <span style="color:#00c896;">{{ $typeCounts['startup'] }} Startups</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Filtros --}}
    <div class="d-flex flex-wrap gap-2 mb-4" id="filter-buttons">
        <button class="btn btn-primary btn-sm filter-btn active" data-filter="all">
            Todos <span class="badge bg-white text-primary ms-1">{{ $total }}</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="universidad" style="--bs-btn-active-color:#38b6ff;">
            Universidades <span class="badge bg-secondary ms-1">{{ $typeCounts['universidad'] }}</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="centro_investigacion">
            Centros de Investigación <span class="badge bg-secondary ms-1">{{ $typeCounts['centro_investigacion'] }}</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="startup">
            Startups <span class="badge bg-secondary ms-1">{{ $typeCounts['startup'] }}</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="gobierno">
            Gobierno <span class="badge bg-secondary ms-1">{{ $typeCounts['gobierno'] }}</span>
        </button>
        <button class="btn btn-outline-secondary btn-sm filter-btn" data-filter="organizacion">
            Organizaciones <span class="badge bg-secondary ms-1">{{ $typeCounts['organizacion'] }}</span>
        </button>
    </div>

    {{-- Grid de actores --}}
    <div class="row g-4" id="actors-grid">
        @foreach($actors as $actor)
            <div class="col-md-6 col-lg-4 actor-card" data-type="{{ $actor->type }}">
                <div class="profundiza-card h-100 p-4">
                    {{-- Header --}}
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <span class="badge" style="background:{{ $actor->type_color }}22;color:{{ $actor->type_color }};border:1px solid {{ $actor->type_color }}44;font-size:.72rem;white-space:nowrap;">
                            {{ $actor->type_label }}
                        </span>
                        @if($actor->url)
                            <a href="{{ $actor->url }}" target="_blank" rel="noopener" title="Sitio web"
                               style="color:#94a3b8;font-size:.9rem;text-decoration:none;flex-shrink:0;margin-left:.5rem;">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endif
                    </div>

                    {{-- Nombre --}}
                    <h3 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;line-height:1.4;">{{ $actor->name }}</h3>

                    {{-- Ubicación --}}
                    <div class="mb-2" style="color:#64748b;font-size:.8rem;">
                        <i class="fas fa-map-marker-alt me-1" style="color:var(--primary-color);"></i>
                        {{ $actor->location }}@if($actor->region !== 'Metropolitana'), {{ $actor->region }}@endif
                    </div>

                    {{-- Descripción --}}
                    <p style="color:#475569;font-size:.88rem;line-height:1.7;flex:1;">{{ $actor->description }}</p>

                    {{-- Áreas --}}
                    @if($actor->focus_areas)
                        <div class="d-flex flex-wrap gap-1 mt-3">
                            @foreach($actor->focus_areas as $area)
                                <span class="badge" style="background:#f1f5f9;color:#475569;font-size:.72rem;font-weight:500;">{{ $area }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Link --}}
                    @if($actor->url)
                        <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                            <a href="{{ $actor->url }}" target="_blank" rel="noopener"
                               class="btn btn-sm btn-outline-primary w-100" style="font-size:.8rem;">
                                <i class="fas fa-external-link-alt me-1"></i>Visitar sitio web
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Sin resultados del filtro --}}
    <div id="no-actors" class="text-center py-5 d-none">
        <p style="color:#64748b;">No hay actores en esta categoría.</p>
    </div>

    {{-- CTA --}}
    <div style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);border-radius:.75rem;padding:2rem;border:1px solid #bae6fd;margin-top:3rem;" class="text-center">
        <p style="color:#0369a1;font-size:.95rem;margin:0;">
            <i class="fas fa-plus-circle me-2"></i>
            ¿Conoces un actor del ecosistema IA que no está en esta lista? Escríbenos a
            <a href="mailto:contacto@conocia.cl" style="color:#0369a1;font-weight:600;">contacto@conocia.cl</a>
        </p>
    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
        this.classList.remove('btn-outline-secondary');
        this.classList.add('active', 'btn-primary');

        const filter = this.dataset.filter;
        let   visible = 0;

        document.querySelectorAll('.actor-card').forEach(card => {
            const show = filter === 'all' || card.dataset.type === filter;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('no-actors').classList.toggle('d-none', visible > 0);
    });
});
</script>
@endpush

@endsection
