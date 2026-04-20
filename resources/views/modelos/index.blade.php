@extends('layouts.app')

@section('title', 'Comparador de Modelos de IA — GPT vs Claude vs Gemini | ConocIA')

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle'       => 'Comparador de Modelos de IA 2025 | ConocIA',
        'metaDescription' => 'Compara GPT-4o, Claude, Gemini, Llama y más. Precios, contexto, capacidades y benchmarks de los principales modelos de inteligencia artificial en un solo lugar.',
        'metaKeywords'    => 'comparar modelos IA, GPT vs Claude, mejor modelo inteligencia artificial, LLM comparativa, precios modelos IA',
        'metaUrl'         => route('modelos.index'),
        'metaType'        => 'website',
    ])
@endsection

@section('content')

{{-- Hero --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-5">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:4px;height:40px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
            <div>
                <h1 class="mb-0 text-white fw-bold" style="font-size:2rem;">Comparador de Modelos IA</h1>
                <p class="mb-0 mt-1" style="color:#aaa;font-size:.9rem;">
                    {{ $models->count() }} modelos · Actualizado {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                </p>
            </div>
        </div>
        <p class="text-white-50 mb-0" style="max-width:600px;font-size:.92rem;">
            Compará precios, capacidades y benchmarks de los principales modelos de inteligencia artificial en un solo lugar.
        </p>
    </div>
</div>

<div class="container py-4">

    {{-- Filtros --}}
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <span class="text-muted" style="font-size:.82rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">Filtrar:</span>

        <button class="btn btn-sm btn-primary filter-btn active" data-filter="all">Todos</button>
        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="open">Open Source</button>
        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="closed">Propietario</button>
        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="free">Con capa gratuita</button>

        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size:.8rem;">Empresa:</span>
            <select id="company-filter" class="form-select form-select-sm" style="width:auto;font-size:.82rem;">
                <option value="all">Todas</option>
                @foreach($companies as $slug => $name)
                    <option value="{{ $slug }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Cards grid --}}
    <div class="row g-3" id="models-grid">
        @foreach($models as $model)
        <div class="col-lg-6"
             data-access="{{ $model->access }}"
             data-company="{{ $model->company_slug }}"
             data-free="{{ $model->has_free_tier ? 'true' : 'false' }}"
             data-model-card>
            <div class="card border-0 h-100" style="background:#fff;border-radius:.875rem;box-shadow:0 2px 12px rgba(0,0,0,.07);transition:box-shadow .2s,transform .2s;">
                <div class="card-body p-4">

                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h2 class="mb-0 fw-bold" style="font-size:1.15rem;color:#0f172a;">{{ $model->name }}</h2>
                                @if($model->featured)
                                <span class="badge" style="background:rgba(56,182,255,.12);color:#0369a1;font-size:.65rem;font-weight:600;">DESTACADO</span>
                                @endif
                            </div>
                            <span class="text-muted" style="font-size:.82rem;">{{ $model->company }} · {{ $model->release_date }}</span>
                        </div>
                        <div class="text-end">
                            <span class="badge px-2 py-1" style="font-size:.7rem;font-weight:600;
                                background:{{ $model->access === 'open' ? 'rgba(0,200,150,.12)' : 'rgba(56,182,255,.1)' }};
                                color:{{ $model->access === 'open' ? '#00875a' : '#0369a1' }};">
                                {{ $model->access === 'open' ? 'Open Source' : ($model->access === 'api-only' ? 'API' : 'Propietario') }}
                            </span>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    @if($model->description)
                    <p class="mb-3" style="font-size:.85rem;color:#475569;line-height:1.6;">{{ $model->description }}</p>
                    @endif

                    {{-- Specs --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2 text-center rounded-2" style="background:#f8fafc;">
                                <div class="fw-bold" style="font-size:.95rem;color:#0f172a;">{{ $model->context_window_label ?? '—' }}</div>
                                <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;">Contexto</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 text-center rounded-2" style="background:#f8fafc;">
                                @if($model->price_input)
                                <div class="fw-bold" style="font-size:.85rem;color:#0f172a;">USD ${{ number_format($model->price_input, 2) }}</div>
                                <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;">por 1M tokens</div>
                                @else
                                <div class="fw-bold" style="font-size:.95rem;color:#00875a;">{{ $model->has_free_tier ? 'Gratis' : 'Varía' }}</div>
                                <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;">Precio USD</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 text-center rounded-2" style="background:#f8fafc;">
                                @if($model->score_mmlu)
                                <div class="fw-bold" style="font-size:.95rem;color:#0f172a;">{{ $model->score_mmlu }}%</div>
                                <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;">MMLU</div>
                                @else
                                <div class="fw-bold" style="font-size:.95rem;color:#94a3b8;">—</div>
                                <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;">MMLU</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Capacidades --}}
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @foreach($model->getCapabilities() as $cap)
                        <span class="badge" style="background:rgba(56,182,255,.08);color:#0369a1;font-size:.68rem;font-weight:500;padding:.3rem .55rem;">
                            <i class="fas {{ $cap['icon'] }} me-1" style="font-size:.6rem;"></i>{{ $cap['label'] }}
                        </span>
                        @endforeach
                        @if($model->has_free_tier)
                        <span class="badge" style="background:rgba(0,200,150,.1);color:#00875a;font-size:.68rem;font-weight:500;padding:.3rem .55rem;">
                            <i class="fas fa-check-circle me-1" style="font-size:.6rem;"></i>Capa gratuita
                        </span>
                        @endif
                    </div>

                    {{-- Footer --}}
                    @if($model->official_url && $model->official_url !== '#')
                    <a href="{{ $model->official_url }}" target="_blank" rel="noopener"
                       class="btn btn-sm btn-outline-primary w-100" style="font-size:.78rem;border-radius:.5rem;">
                        <i class="fas fa-external-link-alt me-1"></i>Sitio oficial
                    </a>
                    @endif

                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Nota de actualización --}}
    <p class="text-center text-muted mt-5" style="font-size:.8rem;">
        <i class="fas fa-info-circle me-1"></i>
        Los precios y benchmarks son aproximados y pueden variar. Fuentes: sitios oficiales de cada modelo y <a href="https://lmsys.org/blog/2023-05-25-chatbot-arena/" target="_blank" rel="noopener" class="text-muted">Chatbot Arena</a>.
        Última actualización: {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}.
    </p>

</div>

@push('scripts')
<script>
(function () {
    const cards   = document.querySelectorAll('[data-model-card]');
    const btns    = document.querySelectorAll('.filter-btn');
    const select  = document.getElementById('company-filter');

    let activeAccess  = 'all';
    let activeCompany = 'all';

    function applyFilters() {
        cards.forEach(card => {
            const access  = card.dataset.access;
            const company = card.dataset.company;
            const free    = card.dataset.free === 'true';

            const matchAccess  = activeAccess === 'all'
                || (activeAccess === 'open'   && access === 'open')
                || (activeAccess === 'closed' && access !== 'open')
                || (activeAccess === 'free'   && free);
            const matchCompany = activeCompany === 'all' || company === activeCompany;

            card.style.display = (matchAccess && matchCompany) ? '' : 'none';
        });
    }

    btns.forEach(btn => {
        btn.addEventListener('click', function () {
            btns.forEach(b => b.classList.remove('active', 'btn-primary'));
            btns.forEach(b => { b.classList.add('btn-outline-secondary'); b.classList.remove('btn-primary'); });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary');
            activeAccess = this.dataset.filter;
            applyFilters();
        });
    });

    select.addEventListener('change', function () {
        activeCompany = this.value;
        applyFilters();
    });
})();
</script>
@endpush

@push('styles')
<style>
    [data-model-card] .card:hover {
        box-shadow: 0 8px 28px rgba(56,182,255,.15) !important;
        transform: translateY(-2px);
    }
</style>
@endpush

@endsection
