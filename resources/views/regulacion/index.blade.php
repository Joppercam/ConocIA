@extends('layouts.app')

@section('title', 'Observatorio de Regulación IA — Legislación y Políticas Públicas | ConocIA')
@section('meta_description', 'Seguimiento de la legislación y políticas públicas sobre inteligencia artificial en Chile y el mundo. Proyecto de ley chileno, EU AI Act y más.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">IA EN CHILE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Observatorio de Regulación IA</h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;">
                    Seguimiento de legislación y políticas públicas sobre inteligencia artificial en Chile y el mundo.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Regulación destacada --}}
    @if($featured)
    <div class="mb-5">
        <p class="profundiza-section-label">Regulación en foco</p>
        <div style="background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border:1px solid #f59e0b;border-left:4px solid #f59e0b;border-radius:.75rem;padding:2rem;">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="badge" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;font-size:.78rem;">
                    {{ $featured->status_label }}
                </span>
                <span class="badge" style="background:#e2e8f0;color:#475569;font-size:.78rem;">
                    <i class="fas fa-flag me-1"></i>Chile
                </span>
                @if($featured->date_introduced)
                    <span style="color:#92400e;font-size:.82rem;"><i class="fas fa-calendar me-1"></i>{{ $featured->date_introduced->format('d/m/Y') }}</span>
                @endif
            </div>
            <h2 class="fw-bold mb-3" style="color:#78350f;font-size:1.2rem;line-height:1.4;">{{ $featured->title }}</h2>
            <p style="color:#92400e;font-size:.93rem;line-height:1.8;margin-bottom:1rem;">{{ $featured->summary }}</p>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span style="color:#b45309;font-size:.82rem;"><i class="fas fa-building me-1"></i>{{ $featured->institution }}</span>
                @if($featured->source_url)
                    <a href="{{ $featured->source_url }}" target="_blank" rel="noopener"
                       class="btn btn-sm btn-warning" style="font-size:.82rem;margin-left:auto;">
                        <i class="fas fa-external-link-alt me-1"></i>Ver fuente oficial
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Filtros --}}
    <div class="d-flex gap-2 mb-4 flex-wrap" id="regulation-filters">
        <button class="btn btn-primary btn-sm reg-filter active" data-filter="all">Todas</button>
        <button class="btn btn-outline-secondary btn-sm reg-filter" data-filter="chile">
            <i class="fas fa-flag me-1"></i>Chile
        </button>
        <button class="btn btn-outline-secondary btn-sm reg-filter" data-filter="internacional">
            <i class="fas fa-globe me-1"></i>Internacional
        </button>
    </div>

    {{-- Cards --}}
    <div class="row g-4" id="regulations-grid">

        @foreach($chile as $reg)
        <div class="col-md-6 reg-card" data-scope="chile">
            <div class="profundiza-card h-100 p-4">
                <div class="d-flex align-items-start gap-2 mb-3 flex-wrap">
                    <span class="badge" style="background:{{ $reg->status_color }}22;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}44;font-size:.72rem;">
                        {{ $reg->status_label }}
                    </span>
                    <span class="badge" style="background:#e2e8f0;color:#475569;font-size:.72rem;">Chile</span>
                </div>
                <h3 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;line-height:1.4;">{{ $reg->title }}</h3>
                <p style="color:#64748b;font-size:.82rem;margin-bottom:.75rem;">
                    <i class="fas fa-building me-1"></i>{{ $reg->institution }}
                    @if($reg->date_introduced)
                        &nbsp;·&nbsp;<i class="fas fa-calendar me-1"></i>{{ $reg->date_introduced->format('d/m/Y') }}
                    @endif
                </p>
                <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">{{ $reg->summary }}</p>
                @if($reg->source_url)
                    <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                        <a href="{{ $reg->source_url }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-primary w-100" style="font-size:.8rem;">
                            <i class="fas fa-external-link-alt me-1"></i>Ver fuente oficial
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endforeach

        @foreach($internacional as $reg)
        <div class="col-md-6 reg-card" data-scope="internacional">
            <div class="profundiza-card h-100 p-4">
                <div class="d-flex align-items-start gap-2 mb-3 flex-wrap">
                    <span class="badge" style="background:{{ $reg->status_color }}22;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}44;font-size:.72rem;">
                        {{ $reg->status_label }}
                    </span>
                    <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.72rem;">Internacional</span>
                </div>
                <h3 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;line-height:1.4;">{{ $reg->title }}</h3>
                <p style="color:#64748b;font-size:.82rem;margin-bottom:.75rem;">
                    <i class="fas fa-building me-1"></i>{{ $reg->institution }}
                    @if($reg->date_introduced)
                        &nbsp;·&nbsp;<i class="fas fa-calendar me-1"></i>{{ $reg->date_introduced->format('d/m/Y') }}
                    @endif
                </p>
                <p style="color:#475569;font-size:.9rem;line-height:1.75;margin:0;">{{ $reg->summary }}</p>
            </div>
        </div>
        @endforeach

    </div>

    {{-- Nota actualización --}}
    <div class="mt-4 text-end">
        <small style="color:#94a3b8;">
            <i class="fas fa-sync-alt me-1"></i>
            Este observatorio se actualiza periódicamente. Última actualización:
            {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : 'recientemente' }}
        </small>
    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.reg-filter').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.reg-filter').forEach(b => {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('active', 'btn-primary');

        const filter = this.dataset.filter;
        document.querySelectorAll('.reg-card').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.scope === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush

@endsection
