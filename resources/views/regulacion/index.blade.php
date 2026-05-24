@extends('layouts.app')

@section('title', 'Observatorio de Regulación IA — Leyes que definen el futuro de la IA | ConocIA')
@section('meta_description', 'Seguimiento y análisis en profundidad de la legislación sobre inteligencia artificial en Chile y el mundo. Proyecto de ley chileno, EU AI Act y más.')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">IA EN CHILE Y EL MUNDO</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Observatorio de Regulación IA</h1>
                <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;">
                    Entendiendo las leyes que definen el futuro de la inteligencia artificial. Análisis en profundidad para que cualquier ciudadano entienda qué dice cada norma, por qué importa y cómo le afecta.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    {{-- Regulación en foco --}}
    @if($featured)
    <div class="mb-5">
        <p class="profundiza-section-label">Regulación en foco</p>
        <div style="background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border:1px solid #f59e0b;border-left:4px solid #f59e0b;border-radius:.75rem;padding:2rem;">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="badge" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;font-size:.78rem;">
                    {{ $featured->status_label }}
                </span>
                <span class="badge" style="background:#fde68a;color:#92400e;font-size:.78rem;font-weight:600;">
                    <i class="fas fa-gavel me-1"></i>En trámite en el Senado
                </span>
                <span class="badge" style="background:#e2e8f0;color:#475569;font-size:.78rem;">
                    <i class="fas fa-flag me-1"></i>Chile
                </span>
                @if($featured->date_introduced)
                    <span style="color:#92400e;font-size:.82rem;"><i class="fas fa-calendar me-1"></i>{{ $featured->date_introduced->format('d/m/Y') }}</span>
                @endif
            </div>
            <h2 class="fw-bold mb-3" style="color:#78350f;font-size:1.2rem;line-height:1.4;">{{ $featured->title }}</h2>
            <p style="color:#92400e;font-size:.95rem;line-height:1.8;margin-bottom:1.25rem;">{{ $featured->summary }}</p>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span style="color:#b45309;font-size:.82rem;"><i class="fas fa-building me-1"></i>{{ $featured->institution }}</span>
                <div class="ms-auto d-flex gap-2">
                    @if($featured->source_url)
                        <a href="{{ $featured->source_url }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-warning" style="font-size:.82rem;">
                            <i class="fas fa-external-link-alt me-1"></i>Fuente oficial
                        </a>
                    @endif
                    @if($featured->content)
                        <a href="{{ route('regulacion.show', $featured->slug) }}"
                           class="btn btn-sm btn-warning" style="font-size:.82rem;">
                            <i class="fas fa-book-open me-1"></i>Leer análisis completo
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Comparador de enfoques regulatorios --}}
    <div class="mb-5">
        <p class="profundiza-section-label">Comparador de enfoques</p>
        <div class="profundiza-card p-0" style="overflow:hidden;">
            <div class="table-responsive">
                <table class="table table-borderless mb-0" style="font-size:.87rem;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;width:20%;">País / Región</th>
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Enfoque</th>
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Estado</th>
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Prohibiciones clave</th>
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Sanciones</th>
                            <th style="padding:1rem 1.25rem;color:#64748b;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;">Fiscalización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:1rem 1.25rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:1.1rem;">🇨🇱</span>
                                    <strong style="color:#0f172a;">Chile</strong>
                                </div>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Ley basada en riesgo (modelo europeo)</td>
                            <td style="padding:1rem 1.25rem;">
                                <span class="badge" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;font-size:.75rem;">En tramitación</span>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Manipulación subliminal, scoring social, biométrica en espacios públicos</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Por definir en reglamento</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Agencia de Protección de Datos</td>
                        </tr>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:1rem 1.25rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:1.1rem;">🇪🇺</span>
                                    <strong style="color:#0f172a;">Unión Europea</strong>
                                </div>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Ley basada en riesgo (referente mundial)</td>
                            <td style="padding:1rem 1.25rem;">
                                <span class="badge" style="background:#10b98122;color:#065f46;border:1px solid #10b98144;font-size:.75rem;">Vigente</span>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Scoring social, manipulación subliminal, biométrica remota en tiempo real</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Hasta €35M o 7% facturación global</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Autoridades nacionales + Oficina de IA europea</td>
                        </tr>
                        <tr>
                            <td style="padding:1rem 1.25rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size:1.1rem;">🇺🇸</span>
                                    <strong style="color:#0f172a;">Estados Unidos</strong>
                                </div>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Orden ejecutiva + autorregulación voluntaria</td>
                            <td style="padding:1rem 1.25rem;">
                                <span class="badge" style="background:#10b98122;color:#065f46;border:1px solid #10b98144;font-size:.75rem;">Vigente</span>
                            </td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Sin prohibiciones formales (compromisos voluntarios)</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Sin sanciones legislativas</td>
                            <td style="padding:1rem 1.25rem;color:#334155;">Agencias federales sectoriales</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
            <div class="profundiza-card h-100 p-4 d-flex flex-column">
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
                <p style="color:#475569;font-size:.9rem;line-height:1.75;flex-grow:1;">{{ $reg->summary }}</p>
                <div class="mt-3 pt-3 d-flex gap-2 flex-wrap" style="border-top:1px solid #f1f5f9;">
                    @if($reg->content)
                        <a href="{{ route('regulacion.show', $reg->slug) }}"
                           class="btn btn-sm btn-primary flex-grow-1" style="font-size:.8rem;">
                            <i class="fas fa-book-open me-1"></i>Leer análisis completo
                        </a>
                    @endif
                    @if($reg->source_url)
                        <a href="{{ $reg->source_url }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        @foreach($internacional as $reg)
        <div class="col-md-6 reg-card" data-scope="internacional">
            <div class="profundiza-card h-100 p-4 d-flex flex-column">
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
                <p style="color:#475569;font-size:.9rem;line-height:1.75;flex-grow:1;">{{ $reg->summary }}</p>
                @if($reg->content)
                    <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                        <a href="{{ route('regulacion.show', $reg->slug) }}"
                           class="btn btn-sm btn-primary w-100" style="font-size:.8rem;">
                            <i class="fas fa-book-open me-1"></i>Leer análisis completo
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endforeach

    </div>

    {{-- Nota editorial --}}
    <div class="mt-5 p-4" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;">
        <p class="mb-0" style="color:#64748b;font-size:.85rem;line-height:1.7;">
            <i class="fas fa-info-circle me-2" style="color:var(--primary-color);"></i>
            <strong style="color:#475569;">Nota editorial:</strong>
            Este observatorio es mantenido por ConocIA como parte de su misión de divulgación. La información se actualiza periódicamente con base en fuentes oficiales y medios especializados. No constituye asesoría legal.
            <span class="ms-2" style="color:#94a3b8;">
                <i class="fas fa-sync-alt me-1"></i>Última actualización:
                {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : 'recientemente' }}
            </span>
        </p>
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
