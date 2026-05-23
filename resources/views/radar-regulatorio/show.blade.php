@extends('layouts.app')

@section('title', $item->title . ' — Radar Regulatorio IA Chile | ConocIA')
@section('meta_description', $item->excerpt ?? 'Análisis regulatorio de IA en Chile.')

@section('content')

<div class="container py-5">
    <div class="row g-5">

        {{-- Artículo principal --}}
        <div class="col-lg-8">

            {{-- Breadcrumb --}}
            <nav class="mb-4" style="font-size:.82rem;">
                <a href="{{ route('home') }}" style="color:#64748b;text-decoration:none;">Inicio</a>
                <span class="mx-2 text-muted">/</span>
                <a href="{{ route('radar.index') }}" style="color:#64748b;text-decoration:none;">Radar Regulatorio</a>
                <span class="mx-2 text-muted">/</span>
                <span style="color:#334155;">{{ Str::limit($item->title, 50) }}</span>
            </nav>

            {{-- Badges de estado --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge px-3 py-2" style="background:{{ $item->tipo_color }}20;color:{{ $item->tipo_color }};font-size:.78rem;">
                    {{ $item->tipo_label }}
                </span>
                <span class="badge px-3 py-2" style="background:{{ $item->estado_color }}20;color:{{ $item->estado_color }};font-size:.78rem;">
                    <i class="fas fa-circle me-1" style="font-size:.5rem;"></i>{{ $item->estado_label }}
                </span>
                @if($item->relevancia === 'alta')
                <span class="badge px-3 py-2" style="background:rgba(220,38,38,.1);color:#dc2626;font-size:.78rem;">
                    <i class="fas fa-exclamation-triangle me-1"></i>Alta relevancia
                </span>
                @endif
            </div>

            <h1 class="fw-bold mb-3" style="font-size:1.9rem;line-height:1.25;color:#0f172a;">{{ $item->title }}</h1>

            {{-- Meta --}}
            <div class="d-flex flex-wrap gap-3 mb-4 pb-4 border-bottom" style="font-size:.82rem;color:#64748b;">
                @if($item->organismo)
                <span><i class="fas fa-landmark me-1"></i>{{ $item->organismo }}</span>
                @endif
                @if($item->fecha_evento)
                <span><i class="far fa-calendar-alt me-1"></i>{{ $item->fecha_evento->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>
                @endif
                <span><i class="far fa-clock me-1"></i>{{ $item->reading_time }} min lectura</span>
            </div>

            {{-- Excerpt destacado --}}
            @if($item->excerpt)
            <div class="mb-4 p-4 rounded-3" style="background:#f8fafc;border-left:4px solid #e63946;">
                <p class="mb-0 fw-semibold" style="color:#1e293b;font-size:1rem;line-height:1.6;">{{ $item->excerpt }}</p>
            </div>
            @endif

            {{-- Contenido --}}
            <div class="radar-content" style="color:#1e293b;font-size:.97rem;line-height:1.8;">
                {!! $item->content !!}
            </div>

            {{-- Actores clave --}}
            @if($item->key_actors && count($item->key_actors) > 0)
            <div class="mt-5 pt-4 border-top">
                <h5 class="fw-bold mb-3" style="font-size:.95rem;color:#374151;">Actores clave</h5>
                <div class="row g-3">
                    @foreach($item->key_actors as $actor)
                    <div class="col-md-6">
                        <div class="d-flex gap-3 p-3 rounded-3" style="background:#f8fafc;">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width:38px;height:38px;background:rgba(230,57,70,.1);">
                                <i class="fas fa-user" style="color:#e63946;font-size:.8rem;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.85rem;color:#1e293b;">{{ $actor['name'] ?? '' }}</div>
                                <div style="font-size:.75rem;color:#64748b;">{{ $actor['role'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Fuente --}}
            @if($item->fuente_url)
            <div class="mt-4">
                <a href="{{ $item->fuente_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fas fa-external-link-alt me-2"></i>Fuente oficial
                </a>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px;">

                {{-- Ficha técnica --}}
                <div class="profundiza-card p-4 mb-4">
                    <h6 class="fw-bold mb-3" style="font-size:.88rem;color:#374151;text-transform:uppercase;letter-spacing:.05em;">Ficha del hito</h6>
                    <table class="w-100" style="font-size:.83rem;">
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">Tipo</td>
                            <td class="py-2 fw-semibold text-end">{{ $item->tipo_label }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">Estado</td>
                            <td class="py-2 fw-semibold text-end" style="color:{{ $item->estado_color }};">{{ $item->estado_label }}</td>
                        </tr>
                        @if($item->organismo)
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">Organismo</td>
                            <td class="py-2 fw-semibold text-end" style="max-width:160px;">{{ $item->organismo }}</td>
                        </tr>
                        @endif
                        @if($item->fecha_evento)
                        <tr class="border-bottom">
                            <td class="py-2 text-muted">Fecha</td>
                            <td class="py-2 fw-semibold text-end">{{ $item->fecha_evento->locale('es')->isoFormat('D MMM YYYY') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-2 text-muted">Relevancia</td>
                            <td class="py-2 text-end">
                                <span class="badge" style="background:{{ $item->estado_color }}20;color:{{ $item->estado_color }};">{{ ucfirst($item->relevancia) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Relacionados --}}
                @if($relacionados->isNotEmpty())
                <div class="profundiza-card p-4 mb-4">
                    <h6 class="fw-bold mb-3" style="font-size:.88rem;color:#374151;text-transform:uppercase;letter-spacing:.05em;">También en el radar</h6>
                    @foreach($relacionados as $rel)
                    <a href="{{ route('radar.show', $rel->slug) }}" class="d-block text-decoration-none mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="mb-1">
                            <span class="badge" style="background:{{ $rel->tipo_color }}20;color:{{ $rel->tipo_color }};font-size:.65rem;">{{ $rel->tipo_label }}</span>
                        </div>
                        <div style="color:#1e293b;font-size:.82rem;font-weight:600;line-height:1.35;">{{ Str::limit($rel->title, 65) }}</div>
                    </a>
                    @endforeach
                </div>
                @endif

                <a href="{{ route('radar.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Radar
                </a>
            </div>
        </div>

    </div>
</div>

<style>
.radar-content h2 { font-size: 1.2rem; font-weight: 700; color: #1e293b; margin-top: 2rem; margin-bottom: .75rem; }
.radar-content p  { margin-bottom: 1.1rem; }
.radar-content ul { padding-left: 1.5rem; margin-bottom: 1.1rem; }
.radar-content li { margin-bottom: .4rem; }
.radar-content blockquote { border-left: 4px solid #e63946; padding: .75rem 1.25rem; background: #fff5f5; margin: 1.5rem 0; border-radius: 0 .5rem .5rem 0; }
</style>
@endsection
