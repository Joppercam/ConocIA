@extends('layouts.app')

@section('title', 'Startups de IA — ConocIA')
@section('meta_description', 'Directorio de startups de inteligencia artificial: financiamiento, sector, fundadores y productos.')

@section('content')
<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="fw-bold mb-2" style="font-size:2rem;">Startups de IA</h1>
        <p class="text-muted mb-0">Directorio actualizado de startups de inteligencia artificial relevantes a nivel global.</p>
    </div>

    {{-- Filtros --}}
    <div class="d-flex flex-wrap gap-2 mb-4" id="filtros-startups">
        <button class="btn btn-sm btn-primary filter-btn active" data-filter="all">Todas</button>
        @foreach($sectors as $sector)
            <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="{{ $sector }}">
                {{ \App\Models\Startup::sectorLabels()[$sector] ?? ucfirst($sector) }}
            </button>
        @endforeach
    </div>

    {{-- Grid --}}
    @if($startups->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-rocket fa-3x mb-3 d-block opacity-25"></i>
            <p>Próximamente startups de IA destacadas.</p>
        </div>
    @else
    <div class="row g-4" id="startups-grid">
        @foreach($startups as $startup)
        <div class="col-md-6 col-lg-4 startup-card" data-sector="{{ $startup->sector }}">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        @if($startup->logo)
                            <img src="{{ $startup->logo }}" alt="{{ $startup->name }}" style="width:48px;height:48px;object-fit:contain;border-radius:8px;">
                        @else
                            <div style="width:48px;height:48px;background:var(--primary-color);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-rocket text-white"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1 min-width-0">
                            <h5 class="fw-bold mb-0" style="font-size:1rem;">
                                <a href="{{ route('startups.show', $startup) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $startup->name }}
                                </a>
                            </h5>
                            @if($startup->country)
                            <span class="text-muted" style="font-size:.78rem;">{{ $startup->city ? $startup->city.', ' : '' }}{{ $startup->country }}</span>
                            @endif
                        </div>
                    </div>

                    @if($startup->tagline)
                    <p class="text-muted mb-3" style="font-size:.88rem;line-height:1.5;">{{ $startup->tagline }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        @if($startup->stage)
                        <span class="badge" style="background:{{ $startup->stage_color }};font-size:.72rem;">
                            {{ $startup->stage_label }}
                        </span>
                        @endif
                        @if($startup->sector)
                        <span class="badge bg-secondary" style="font-size:.72rem;">
                            {{ \App\Models\Startup::sectorLabels()[$startup->sector] ?? $startup->sector }}
                        </span>
                        @endif
                        @if($startup->total_funding_usd)
                        <span class="badge bg-light text-dark border" style="font-size:.72rem;">
                            <i class="fas fa-dollar-sign me-1"></i>{{ $startup->funding_label }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $startups->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active','btn-primary'));
        document.querySelectorAll('.filter-btn').forEach(b => { if(!b.classList.contains('active')) b.classList.add('btn-outline-secondary'); });
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-secondary');

        const filter = this.dataset.filter;
        document.querySelectorAll('.startup-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.sector === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
