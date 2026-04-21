@extends('layouts.app')

@section('title', 'Agentes de IA — ConocIA')
@section('meta_description', 'Directorio de agentes de inteligencia artificial: frameworks, herramientas y asistentes autónomos.')

@section('content')
<div class="container py-5">

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="fw-bold mb-2" style="font-size:2rem;">Agentes de IA</h1>
        <p class="text-muted mb-0">Directorio de frameworks, herramientas y agentes autónomos de inteligencia artificial.</p>
    </div>

    {{-- Filtros --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-sm btn-primary filter-btn active" data-filter="all">Todos</button>
        @foreach($categories as $cat)
            <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="{{ $cat }}">
                {{ \App\Models\AiAgent::categoryLabels()[$cat] ?? ucfirst($cat) }}
            </button>
        @endforeach
    </div>

    {{-- Grid --}}
    @if($agents->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fas fa-robot fa-3x mb-3 d-block opacity-25"></i>
            <p>Próximamente agentes de IA destacados.</p>
        </div>
    @else
    <div class="row g-4" id="agents-grid">
        @foreach($agents as $agent)
        <div class="col-md-6 col-lg-4 agent-card" data-category="{{ $agent->category }}">
            <div class="card h-100 border-0 shadow-sm" style="border-radius:12px;overflow:hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        @if($agent->logo)
                            <img src="{{ $agent->logo }}" alt="{{ $agent->name }}" style="width:48px;height:48px;object-fit:contain;border-radius:8px;">
                        @else
                            <div style="width:48px;height:48px;background:var(--primary-color);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-robot text-white"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1 min-width-0">
                            <h5 class="fw-bold mb-0" style="font-size:1rem;">
                                <a href="{{ route('agents.show', $agent) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $agent->name }}
                                </a>
                            </h5>
                            @if($agent->stars_github)
                            <span class="text-muted" style="font-size:.78rem;">
                                <i class="fas fa-star me-1" style="color:#fbbf24;"></i>{{ $agent->formatted_stars }} en GitHub
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($agent->tagline)
                    <p class="text-muted mb-3" style="font-size:.88rem;line-height:1.5;">{{ $agent->tagline }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        @if($agent->category)
                        <span class="badge bg-secondary" style="font-size:.72rem;">
                            {{ $agent->category_label }}
                        </span>
                        @endif
                        <span class="badge" style="background:{{ $agent->pricing_color }};font-size:.72rem;">
                            {{ $agent->pricing_label }}
                        </span>
                        @if($agent->type === 'open-source')
                        <span class="badge bg-success" style="font-size:.72rem;">Open Source</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $agents->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active','btn-primary');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-secondary');

        const filter = this.dataset.filter;
        document.querySelectorAll('.agent-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
