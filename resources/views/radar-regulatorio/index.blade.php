@extends('layouts.app')

@section('title', 'Radar Regulatorio IA Chile — Seguimiento de Políticas y Leyes | ConocIA')
@section('meta_description', 'Seguimiento actualizado de proyectos de ley, decretos y políticas públicas sobre inteligencia artificial en Chile. Análisis ciudadano sin jerga legal.')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(230,57,70,.2);color:#fca5a5;font-size:.78rem;letter-spacing:.06em;">REGULACIÓN · CHILE</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">Radar <span style="color:#e63946;">Regulatorio</span></h1>
                <p style="color:#94a3b8;font-size:1rem;max-width:520px;line-height:1.7;margin:0;">Seguimiento de proyectos de ley, decretos y políticas públicas sobre IA en Chile. Explicado para ciudadanos, sin jerga legal.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-4 align-items-center">
                <div class="text-center">
                    <div class="fw-bold" style="font-size:2rem;color:#e63946;">{{ $stats['total'] }}</div>
                    <div style="color:#64748b;font-size:.8rem;">hitos registrados</div>
                </div>
                <div class="text-center">
                    <div class="fw-bold" style="font-size:2rem;color:#d97706;">{{ $stats['en_tramite'] }}</div>
                    <div style="color:#64748b;font-size:.8rem;">en trámite</div>
                </div>
                <div class="text-center">
                    <div class="fw-bold" style="font-size:2rem;color:#16a34a;">{{ $stats['vigente'] }}</div>
                    <div style="color:#64748b;font-size:.8rem;">vigentes</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-4">

        {{-- Columna principal --}}
        <div class="col-lg-9">

            {{-- Filtros --}}
            <div class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <a href="{{ route('radar.index') }}" class="btn btn-sm rounded-pill {{ !$tipo && !$estado && !$relevancia ? 'btn-primary' : 'btn-outline-secondary' }}">Todos</a>
                <a href="{{ route('radar.index', ['tipo' => 'proyecto_ley']) }}" class="btn btn-sm rounded-pill {{ $tipo === 'proyecto_ley' ? 'btn-primary' : 'btn-outline-secondary' }}">Proyectos de ley</a>
                <a href="{{ route('radar.index', ['tipo' => 'politica']) }}" class="btn btn-sm rounded-pill {{ $tipo === 'politica' ? 'btn-primary' : 'btn-outline-secondary' }}">Políticas</a>
                <a href="{{ route('radar.index', ['tipo' => 'decreto']) }}" class="btn btn-sm rounded-pill {{ $tipo === 'decreto' ? 'btn-primary' : 'btn-outline-secondary' }}">Decretos</a>
                <a href="{{ route('radar.index', ['estado' => 'en_tramite']) }}" class="btn btn-sm rounded-pill {{ $estado === 'en_tramite' ? 'btn-warning' : 'btn-outline-secondary' }}">En trámite</a>
                <a href="{{ route('radar.index', ['relevancia' => 'alta']) }}" class="btn btn-sm rounded-pill {{ $relevancia === 'alta' ? 'btn-danger' : 'btn-outline-secondary' }}">Alta relevancia</a>
            </div>

            {{-- Timeline --}}
            @if($items->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-gavel fa-3x mb-3 d-block" style="color:#e63946;opacity:.3;"></i>
                <p class="text-muted">El radar está en construcción. El contenido se generará pronto.</p>
            </div>
            @else
            <div class="radar-timeline">
                @foreach($items as $item)
                <div class="radar-item mb-4">
                    <a href="{{ route('radar.show', $item->slug) }}" class="text-decoration-none d-block">
                        <div class="profundiza-card p-4 h-100" style="border-left:4px solid {{ $item->tipo_color }};transition:transform .15s ease;">

                            <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
                                {{-- Tipo --}}
                                <span class="badge" style="background:{{ $item->tipo_color }}20;color:{{ $item->tipo_color }};font-size:.7rem;">
                                    {{ $item->tipo_label }}
                                </span>
                                {{-- Estado --}}
                                <span class="badge" style="background:{{ $item->estado_color }}20;color:{{ $item->estado_color }};font-size:.7rem;">
                                    <i class="fas fa-circle me-1" style="font-size:.45rem;"></i>{{ $item->estado_label }}
                                </span>
                                {{-- Relevancia alta --}}
                                @if($item->relevancia === 'alta')
                                <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;font-size:.7rem;">
                                    <i class="fas fa-exclamation-triangle me-1" style="font-size:.65rem;"></i>Alta relevancia
                                </span>
                                @endif
                                <span class="ms-auto text-muted" style="font-size:.75rem;">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $item->fecha_evento?->locale('es')->isoFormat('D MMM YYYY') ?? '—' }}
                                </span>
                            </div>

                            <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:1rem;line-height:1.35;">{{ $item->title }}</h5>

                            @if($item->organismo)
                            <div class="mb-2" style="color:#64748b;font-size:.8rem;">
                                <i class="fas fa-landmark me-1"></i>{{ $item->organismo }}
                            </div>
                            @endif

                            <p style="color:#475569;font-size:.85rem;line-height:1.55;margin:0;">{{ $item->excerpt }}</p>

                            <div class="mt-3 d-flex align-items-center gap-2" style="font-size:.75rem;color:#94a3b8;">
                                <i class="far fa-clock me-1"></i>{{ $item->reading_time }} min lectura
                                <span class="ms-auto" style="color:var(--primary-color);">Leer análisis <i class="fas fa-arrow-right ms-1" style="font-size:.65rem;"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $items->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="sticky-top" style="top:80px;">

                {{-- Alta relevancia --}}
                @if($recientes->isNotEmpty())
                <div class="profundiza-card p-4 mb-3">
                    <p class="profundiza-section-label mb-3" style="color:#e63946;">Alta relevancia</p>
                    @foreach($recientes as $r)
                    <a href="{{ route('radar.show', $r->slug) }}" class="d-block text-decoration-none mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="mb-1">
                            <span class="badge" style="background:{{ $r->tipo_color }}20;color:{{ $r->tipo_color }};font-size:.65rem;">{{ $r->tipo_label }}</span>
                        </div>
                        <div style="color:#1e293b;font-size:.82rem;font-weight:600;line-height:1.35;">{{ Str::limit($r->title, 70) }}</div>
                        <div style="color:#94a3b8;font-size:.72rem;margin-top:3px;">{{ $r->fecha_evento?->locale('es')->isoFormat('D MMM YYYY') }}</div>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Estado actual --}}
                <div class="profundiza-card p-4 mb-3">
                    <p class="profundiza-section-label mb-3">Estado del radar</p>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.83rem;color:#334155;">En trámite</span>
                        <span class="badge" style="background:rgba(217,119,6,.1);color:#d97706;">{{ $stats['en_tramite'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.83rem;color:#334155;">Vigentes</span>
                        <span class="badge" style="background:rgba(22,163,74,.1);color:#16a34a;">{{ $stats['vigente'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="font-size:.83rem;color:#334155;">Alta relevancia</span>
                        <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;">{{ $stats['alta'] }}</span>
                    </div>
                </div>

                <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary w-100 btn-sm mb-2">
                    <i class="fas fa-microscope me-2"></i>Análisis de Fondo
                </a>
                <a href="{{ route('news.category', 'ia-en-chile') }}" class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="fas fa-newspaper me-2"></i>Noticias IA en Chile
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
