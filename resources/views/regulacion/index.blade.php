@extends('layouts.app')

@section('title', 'Observatorio de Regulación IA — Leyes que definen el futuro de la IA | ConocIA')
@section('meta_description', 'Análisis en profundidad de la legislación sobre inteligencia artificial en Chile y el mundo. Proyecto de ley, opiniones de actores clave, comparador internacional.')

@section('reading_progress', true)

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-5">
    <div class="container py-3">
        <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">IA EN CHILE Y EL MUNDO</span>
        <h1 class="fw-bold text-white mb-3" style="font-size:2.4rem;line-height:1.15;">Observatorio de Regulación IA</h1>
        <p style="color:#94a3b8;font-size:1.05rem;line-height:1.7;max-width:580px;margin-bottom:1.5rem;">
            Entendiendo las leyes que definen el futuro de la inteligencia artificial. Qué dice cada norma, quién opina qué y cómo te afecta.
        </p>
        {{-- Quick nav pills --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="#proyecto-ley" class="badge text-decoration-none px-3 py-2" style="background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.8rem;font-weight:500;border:1px solid rgba(255,255,255,.15);">📋 Proyecto de Ley Chile</a>
            <a href="#opiniones"   class="badge text-decoration-none px-3 py-2" style="background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.8rem;font-weight:500;border:1px solid rgba(255,255,255,.15);">💬 ¿Qué opinan?</a>
            <a href="#comparador"  class="badge text-decoration-none px-3 py-2" style="background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.8rem;font-weight:500;border:1px solid rgba(255,255,255,.15);">🌍 Comparador</a>
            <a href="#otras"       class="badge text-decoration-none px-3 py-2" style="background:rgba(255,255,255,.1);color:#e2e8f0;font-size:.8rem;font-weight:500;border:1px solid rgba(255,255,255,.15);">📚 Otras regulaciones</a>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- ── Sidebar ─────────────────────────────────────────── --}}
        <div class="col-lg-3 d-none d-lg-block">
            <nav class="sticky-top sidebar-nav" style="top:80px;">
                <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:.75rem;">En esta página</p>
                <ul style="list-style:none;padding:0;margin:0;" class="d-flex flex-column gap-1">
                    <li>
                        <a href="#proyecto-ley" class="sidebar-link d-flex align-items-center gap-2">
                            <span style="font-size:.85rem;">📋</span>
                            <span>Proyecto de Ley Chile</span>
                        </a>
                    </li>
                    <li>
                        <a href="#opiniones" class="sidebar-link d-flex align-items-center gap-2">
                            <span style="font-size:.85rem;">💬</span>
                            <span>¿Qué opinan?</span>
                        </a>
                    </li>
                    <li>
                        <a href="#comparador" class="sidebar-link d-flex align-items-center gap-2">
                            <span style="font-size:.85rem;">🌍</span>
                            <span>Comparador internacional</span>
                        </a>
                    </li>
                    <li>
                        <a href="#otras" class="sidebar-link d-flex align-items-center gap-2">
                            <span style="font-size:.85rem;">📚</span>
                            <span>Otras regulaciones</span>
                        </a>
                    </li>
                </ul>

                {{-- Estado del proyecto --}}
                @if($featured)
                <div class="mt-4 p-3" style="background:#fffbeb;border:1px solid #f59e0b44;border-radius:.625rem;">
                    <p style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#92400e;margin-bottom:.5rem;">Estado actual</p>
                    <span class="badge d-block text-start mb-1" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;font-size:.72rem;font-weight:600;">⚖️ En trámite en el Senado</span>
                    <p style="font-size:.75rem;color:#92400e;margin:.5rem 0 0;">Urgencia suma — Comisión de Desafíos del Futuro</p>
                </div>
                @endif

                {{-- Nota --}}
                <p class="mt-4" style="font-size:.73rem;color:#94a3b8;line-height:1.6;">
                    <i class="fas fa-sync-alt me-1"></i>Actualizado:
                    {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->locale('es')->isoFormat('D MMM YYYY') : 'recientemente' }}
                </p>
            </nav>
        </div>

        {{-- ── Contenido principal ──────────────────────────────── --}}
        <div class="col-lg-9">

            {{-- ══ SECCIÓN 1: Proyecto de Ley ══ --}}
            <section id="proyecto-ley" class="mb-5" style="scroll-margin-top:80px;">
                <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                    <h2 class="fw-bold mb-0" style="color:#0f172a;font-size:1.5rem;">Proyecto de Ley de IA de Chile</h2>
                    <span class="badge" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;">En tramitación</span>
                    <span class="badge" style="background:#fde68a;color:#92400e;font-weight:600;">Boletín 16821-19</span>
                </div>
                <p style="color:#64748b;font-size:.85rem;margin-bottom:1.5rem;">
                    <i class="fas fa-building me-1"></i>Ministerio de Ciencia / Gobierno de Chile
                    &nbsp;·&nbsp;
                    <i class="fas fa-calendar me-1"></i>Ingresado el 7 de mayo de 2024
                    @if($featured && $featured->source_url)
                        &nbsp;·&nbsp;
                        <a href="{{ $featured->source_url }}" target="_blank" rel="noopener" style="color:var(--primary-color);">
                            <i class="fas fa-external-link-alt me-1"></i>Fuente oficial
                        </a>
                    @endif
                </p>

                @if($featured && $featured->content)
                    <div class="reg-content">
                        {!! $featured->content !!}
                    </div>
                @elseif($featured)
                    <p style="color:#475569;font-size:.97rem;line-height:1.85;">{{ $featured->summary }}</p>
                @endif
            </section>

            <hr style="border-color:#e2e8f0;margin:3rem 0;">

            {{-- ══ SECCIÓN 2: Voces y Posturas ══ --}}
            <section id="opiniones" class="mb-5" style="scroll-margin-top:80px;">
                <h2 class="fw-bold mb-1" style="color:#0f172a;font-size:1.5rem;">¿Qué opinan los actores clave?</h2>
                <p style="color:#64748b;font-size:.93rem;margin-bottom:2rem;">
                    El debate es intenso. Estas son las principales posturas — presentadas sin tomar partido.
                </p>

                {{-- Espectro --}}
                <div class="mb-4 p-3 p-md-4" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;">
                    <div class="d-flex justify-content-between mb-2" style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">
                        <span><i class="fas fa-arrow-left me-1"></i>Más regulación</span>
                        <span>Menos regulación<i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                    <div style="position:relative;height:6px;background:linear-gradient(to right,#3b82f6,#10b981,#f59e0b,#ef4444);border-radius:3px;margin-bottom:2rem;">
                        @foreach($voices as $voice)
                        <div style="position:absolute;top:-5px;left:{{ $voice['spectrum_pos'] }}%;transform:translateX(-50%);">
                            <div style="width:16px;height:16px;background:{{ $voice['postura_color'] }};border:2px solid #fff;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.2);"></div>
                        </div>
                        @endforeach
                    </div>
                    <div style="position:relative;height:3rem;">
                        @foreach($voices as $voice)
                        <div style="position:absolute;left:{{ $voice['spectrum_pos'] }}%;transform:translateX(-50%);text-align:center;width:80px;">
                            <div style="font-size:.68rem;font-weight:600;color:#475569;line-height:1.3;">{{ $voice['name'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Voces --}}
                <div class="d-flex flex-column gap-4">
                    @foreach($voices as $voice)
                    <div style="border:1px solid #e2e8f0;border-left:4px solid {{ $voice['postura_color'] }};border-radius:0 .75rem .75rem 0;padding:1.5rem 1.75rem;background:#fff;">

                        {{-- Cabecera --}}
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div style="width:40px;height:40px;min-width:40px;background:{{ $voice['postura_color'] }}15;border:1px solid {{ $voice['postura_color'] }}30;border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                                <i class="{{ $voice['icon'] }}" style="color:{{ $voice['postura_color'] }};font-size:.85rem;"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <strong style="color:#0f172a;font-size:.97rem;">{{ $voice['name'] }}</strong>
                                    <span class="badge" style="background:{{ $voice['postura_color'] }}18;color:{{ $voice['postura_color'] }};border:1px solid {{ $voice['postura_color'] }}35;font-size:.7rem;">{{ $voice['postura_label'] }}</span>
                                </div>
                                <p style="color:#64748b;font-size:.78rem;margin:0;">{{ $voice['role'] }} · {{ $voice['institution'] }}</p>
                            </div>
                        </div>

                        {{-- Resumen --}}
                        <p style="color:#1e293b;font-size:.95rem;font-weight:500;line-height:1.7;margin-bottom:.875rem;">
                            {{ $voice['summary'] }}
                        </p>

                        {{-- Contexto --}}
                        <p style="color:#475569;font-size:.9rem;line-height:1.9;margin-bottom:1rem;">
                            {{ $voice['context'] }}
                        </p>

                        {{-- Punto clave --}}
                        <div style="background:#f8fafc;border-radius:.375rem;padding:.75rem 1rem;border:1px solid #e2e8f0;">
                            <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;">Punto clave</span>
                            <p style="color:#334155;font-size:.87rem;line-height:1.75;margin:.25rem 0 0;">{{ $voice['punto_clave'] }}</p>
                        </div>

                    </div>
                    @endforeach
                </div>

                <p class="mt-3" style="color:#94a3b8;font-size:.78rem;">
                    <i class="fas fa-info-circle me-1"></i>Opiniones basadas en declaraciones y análisis públicos verificables. ConocIA no toma partido.
                </p>
            </section>

            <hr style="border-color:#e2e8f0;margin:3rem 0;">

            {{-- ══ SECCIÓN 3: Comparador ══ --}}
            <section id="comparador" class="mb-5" style="scroll-margin-top:80px;">
                <h2 class="fw-bold mb-1" style="color:#0f172a;font-size:1.5rem;">Chile en el mundo</h2>
                <p style="color:#64748b;font-size:.93rem;margin-bottom:1.5rem;">Tres modelos regulatorios, tres enfoques distintos.</p>

                <div style="border:1px solid #e2e8f0;border-radius:.75rem;overflow:hidden;">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" style="font-size:.86rem;">
                            <thead>
                                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">País / Región</th>
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;">Enfoque</th>
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;">Estado</th>
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;min-width:180px;">Prohibiciones clave</th>
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;">Sanciones</th>
                                    <th style="padding:.875rem 1.25rem;color:#64748b;font-weight:600;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;">Fiscalización</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:.875rem 1.25rem;white-space:nowrap;"><span style="font-size:1.1rem;">🇨🇱</span> <strong style="color:#0f172a;">Chile</strong></td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Ley basada en riesgo</td>
                                    <td style="padding:.875rem 1.25rem;">
                                        <span class="badge" style="background:#f59e0b22;color:#b45309;border:1px solid #f59e0b44;font-size:.73rem;white-space:nowrap;">En tramitación</span>
                                    </td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Manipulación subliminal, scoring social, biométrica pública</td>
                                    <td style="padding:.875rem 1.25rem;color:#64748b;font-size:.82rem;">Por definir en reglamento</td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Agencia de Protección de Datos</td>
                                </tr>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:.875rem 1.25rem;white-space:nowrap;"><span style="font-size:1.1rem;">🇪🇺</span> <strong style="color:#0f172a;">Unión Europea</strong></td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Ley basada en riesgo</td>
                                    <td style="padding:.875rem 1.25rem;">
                                        <span class="badge" style="background:#10b98122;color:#065f46;border:1px solid #10b98144;font-size:.73rem;">Vigente</span>
                                    </td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Scoring social, manipulación, biométrica remota</td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Hasta €35M o 7% facturación</td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Autoridades nacionales + Oficina IA europea</td>
                                </tr>
                                <tr>
                                    <td style="padding:.875rem 1.25rem;white-space:nowrap;"><span style="font-size:1.1rem;">🇺🇸</span> <strong style="color:#0f172a;">Estados Unidos</strong></td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Orden ejecutiva + autorregulación</td>
                                    <td style="padding:.875rem 1.25rem;">
                                        <span class="badge" style="background:#10b98122;color:#065f46;border:1px solid #10b98144;font-size:.73rem;">Vigente</span>
                                    </td>
                                    <td style="padding:.875rem 1.25rem;color:#64748b;font-size:.82rem;">Sin prohibiciones formales</td>
                                    <td style="padding:.875rem 1.25rem;color:#64748b;font-size:.82rem;">Sin sanciones legislativas</td>
                                    <td style="padding:.875rem 1.25rem;color:#334155;">Agencias federales sectoriales</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <hr style="border-color:#e2e8f0;margin:3rem 0;">

            {{-- ══ SECCIÓN 4: Otras regulaciones ══ --}}
            <section id="otras" style="scroll-margin-top:80px;">
                <h2 class="fw-bold mb-1" style="color:#0f172a;font-size:1.5rem;">Otras regulaciones</h2>
                <p style="color:#64748b;font-size:.93rem;margin-bottom:1.5rem;">El ecosistema regulatorio completo, en Chile y el mundo.</p>

                @if($chile->isNotEmpty())
                <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:.75rem;">Chile</p>
                <div class="d-flex flex-column gap-2 mb-4">
                    @foreach($chile as $reg)
                    <div class="d-flex align-items-start gap-3 p-3" style="border:1px solid #e2e8f0;border-radius:.625rem;background:#fff;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge" style="background:{{ $reg->status_color }}18;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}33;font-size:.7rem;">{{ $reg->status_label }}</span>
                                <strong style="color:#0f172a;font-size:.9rem;">{{ $reg->title }}</strong>
                            </div>
                            <p style="color:#64748b;font-size:.8rem;margin-bottom:.5rem;">{{ $reg->institution }}@if($reg->date_introduced) · {{ $reg->date_introduced->format('Y') }}@endif</p>
                            <p style="color:#475569;font-size:.85rem;line-height:1.65;margin:0;">{{ Str::limit($reg->summary, 180) }}</p>
                        </div>
                        @if($reg->content)
                        <a href="{{ route('regulacion.show', $reg->slug) }}"
                           class="btn btn-sm btn-outline-primary flex-shrink-0 align-self-center" style="font-size:.78rem;white-space:nowrap;">
                            Ver análisis <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                @if($internacional->isNotEmpty())
                <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:.75rem;">Internacional</p>
                <div class="d-flex flex-column gap-2">
                    @foreach($internacional as $reg)
                    <div class="d-flex align-items-start gap-3 p-3" style="border:1px solid #e2e8f0;border-radius:.625rem;background:#fff;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge" style="background:{{ $reg->status_color }}18;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}33;font-size:.7rem;">{{ $reg->status_label }}</span>
                                <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">Internacional</span>
                                <strong style="color:#0f172a;font-size:.9rem;">{{ $reg->title }}</strong>
                            </div>
                            <p style="color:#64748b;font-size:.8rem;margin-bottom:.5rem;">{{ $reg->institution }}@if($reg->date_introduced) · {{ $reg->date_introduced->format('Y') }}@endif</p>
                            <p style="color:#475569;font-size:.85rem;line-height:1.65;margin:0;">{{ Str::limit($reg->summary, 180) }}</p>
                        </div>
                        @if($reg->content)
                        <a href="{{ route('regulacion.show', $reg->slug) }}"
                           class="btn btn-sm btn-outline-primary flex-shrink-0 align-self-center" style="font-size:.78rem;white-space:nowrap;">
                            Ver análisis <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Nota editorial --}}
                <div class="mt-5 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.625rem;">
                    <p class="mb-0" style="color:#94a3b8;font-size:.78rem;line-height:1.65;">
                        <i class="fas fa-info-circle me-1"></i>
                        Observatorio mantenido por ConocIA con fines de divulgación. Información basada en fuentes oficiales. No constituye asesoría legal.
                        <span class="ms-2"><i class="fas fa-sync-alt me-1"></i>Actualizado: {{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : 'recientemente' }}</span>
                    </p>
                </div>
            </section>

        </div>{{-- /col-lg-9 --}}
    </div>{{-- /row --}}
</div>

@push('styles')
<style>
.sidebar-link {
    display: block;
    padding: .4rem .75rem;
    font-size: .85rem;
    color: #64748b;
    text-decoration: none;
    border-radius: .375rem;
    transition: background .15s, color .15s;
}
.sidebar-link:hover,
.sidebar-link.active {
    background: rgba(56,182,255,.1);
    color: var(--primary-color);
}
.reg-content h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1.75rem;
    margin-bottom: .625rem;
    padding-bottom: .4rem;
    border-bottom: 2px solid rgba(56,182,255,.15);
}
.reg-content h2:first-child { margin-top: 0; }
.reg-content h3 {
    font-size: .95rem;
    font-weight: 700;
    color: #1e40af;
    margin-top: 1.25rem;
    margin-bottom: .4rem;
}
.reg-content p {
    color: #475569;
    font-size: .95rem;
    line-height: 1.875;
    margin-bottom: .875rem;
}
.reg-content ul, .reg-content ol {
    color: #475569;
    font-size: .95rem;
    line-height: 1.875;
    padding-left: 1.5rem;
    margin-bottom: .875rem;
}
.reg-content li { margin-bottom: .3rem; }
.reg-content strong { color: #1e293b; font-weight: 600; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const sections = document.querySelectorAll('section[id]');
    const links    = document.querySelectorAll('.sidebar-link');
    if (!sections.length || !links.length) return;

    function setActive() {
        let current = '';
        sections.forEach(sec => {
            if (window.scrollY >= sec.offsetTop - 120) current = sec.id;
        });
        links.forEach(link => {
            const href = link.getAttribute('href').replace('#', '');
            link.classList.toggle('active', href === current);
        });
    }

    window.addEventListener('scroll', setActive, { passive: true });
    setActive();
})();
</script>
@endpush

@endsection
