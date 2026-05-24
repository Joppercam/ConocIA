@extends('layouts.app')

@section('title', $regulation->title . ' — Observatorio de Regulación IA | ConocIA')
@section('meta_description', $regulation->summary)

@section('reading_progress', true)

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#0a1020 0%,#16213e 100%);border-bottom:3px solid rgba(56,182,255,.25);" class="py-4">
    <div class="container py-2">
        {{-- Breadcrumb --}}
        <nav style="font-size:.82rem;color:#64748b;margin-bottom:1.25rem;">
            <a href="{{ route('regulacion.index') }}" style="color:#7dd3f0;text-decoration:none;">
                <i class="fas fa-balance-scale me-1"></i>Observatorio de Regulación IA
            </a>
            <span class="mx-2">›</span>
            <span style="color:#94a3b8;">{{ Str::limit($regulation->title, 60) }}</span>
        </nav>

        <div class="d-flex flex-wrap align-items-start gap-3">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge px-3 py-2" style="background:{{ $regulation->status_color }}22;color:{{ $regulation->status_color }};border:1px solid {{ $regulation->status_color }}44;font-size:.78rem;">
                        {{ $regulation->status_label }}
                    </span>
                    <span class="badge px-3 py-2" style="background:rgba(56,182,255,.15);color:#7dd3f0;font-size:.78rem;">
                        {{ $regulation->scope_label }}
                    </span>
                </div>
                <h1 class="fw-bold text-white mb-2" style="font-size:1.75rem;line-height:1.25;max-width:700px;">{{ $regulation->title }}</h1>
                <div style="color:#64748b;font-size:.85rem;" class="d-flex flex-wrap gap-3">
                    <span><i class="fas fa-building me-1" style="color:var(--primary-color);"></i>{{ $regulation->institution }}</span>
                    @if($regulation->date_introduced)
                        <span><i class="fas fa-calendar me-1" style="color:var(--primary-color);"></i>{{ $regulation->date_introduced->isoFormat('D [de] MMMM [de] YYYY') }}</span>
                    @endif
                </div>
            </div>
            @if($regulation->source_url)
                <a href="{{ $regulation->source_url }}" target="_blank" rel="noopener"
                   class="btn btn-outline-light btn-sm align-self-start" style="font-size:.85rem;white-space:nowrap;">
                    <i class="fas fa-external-link-alt me-1"></i>Fuente oficial
                </a>
            @endif
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">

        {{-- Contenido principal --}}
        <div class="col-lg-8">

            {{-- Resumen ejecutivo --}}
            <div class="mb-4 p-4" style="background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border-left:4px solid #f59e0b;border-radius:.75rem;">
                <p class="mb-0 fw-semibold" style="color:#92400e;font-size:.95rem;line-height:1.75;">{{ $regulation->summary }}</p>
            </div>

            {{-- Contenido de divulgación --}}
            <div class="profundiza-card p-4 p-md-5">
                <div class="reg-content">
                    {!! $regulation->content !!}
                </div>
            </div>

            {{-- Nota editorial --}}
            <div class="mt-4 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;">
                <p class="mb-0" style="color:#94a3b8;font-size:.8rem;line-height:1.6;">
                    <i class="fas fa-info-circle me-1"></i>
                    Este análisis es mantenido por ConocIA como parte de su misión de divulgación. La información se actualiza periódicamente con base en fuentes oficiales. No constituye asesoría legal.
                </p>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Ficha técnica --}}
            <div class="profundiza-card p-4 mb-4 sticky-top" style="top:80px;">
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:1rem;border-bottom:2px solid rgba(56,182,255,.2);padding-bottom:.75rem;">
                    Ficha técnica
                </h3>
                <dl style="font-size:.88rem;">
                    <dt style="color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Estado</dt>
                    <dd class="mb-3">
                        <span class="badge" style="background:{{ $regulation->status_color }}22;color:{{ $regulation->status_color }};border:1px solid {{ $regulation->status_color }}44;">
                            {{ $regulation->status_label }}
                        </span>
                    </dd>

                    <dt style="color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Institución</dt>
                    <dd class="mb-3" style="color:#334155;">{{ $regulation->institution }}</dd>

                    <dt style="color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Ámbito</dt>
                    <dd class="mb-3" style="color:#334155;">{{ $regulation->scope_label }}</dd>

                    @if($regulation->date_introduced)
                    <dt style="color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.2rem;">Fecha</dt>
                    <dd class="mb-3" style="color:#334155;">{{ $regulation->date_introduced->isoFormat('D MMM YYYY') }}</dd>
                    @endif

                    @if($regulation->source_url)
                    <dt style="color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.5rem;">Fuente</dt>
                    <dd class="mb-0">
                        <a href="{{ $regulation->source_url }}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-outline-primary w-100" style="font-size:.8rem;">
                            <i class="fas fa-external-link-alt me-1"></i>Ver fuente oficial
                        </a>
                    </dd>
                    @endif
                </dl>
            </div>

            {{-- Otras regulaciones --}}
            @if($others->isNotEmpty())
            <div class="profundiza-card p-4">
                <h3 class="fw-bold mb-3" style="color:#0f172a;font-size:.95rem;">Otras regulaciones</h3>
                <div class="d-flex flex-column gap-3">
                    @foreach($others as $other)
                    <a href="{{ route('regulacion.show', $other->slug) }}"
                       style="text-decoration:none;border:1px solid #e2e8f0;border-radius:.5rem;padding:.75rem;display:block;transition:border-color .2s;"
                       onmouseover="this.style.borderColor='#38b6ff'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div class="d-flex gap-2 align-items-center mb-1 flex-wrap">
                            <span class="badge" style="background:{{ $other->status_color }}22;color:{{ $other->status_color }};border:1px solid {{ $other->status_color }}44;font-size:.7rem;">
                                {{ $other->status_label }}
                            </span>
                        </div>
                        <p class="mb-0 fw-semibold" style="color:#0f172a;font-size:.85rem;line-height:1.4;">{{ $other->title }}</p>
                        <p class="mb-0 mt-1" style="color:#64748b;font-size:.78rem;">{{ $other->institution }}</p>
                    </a>
                    @endforeach
                </div>
                <div class="mt-3 pt-3" style="border-top:1px solid #f1f5f9;">
                    <a href="{{ route('regulacion.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size:.82rem;">
                        <i class="fas fa-balance-scale me-1"></i>Ver observatorio completo
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('styles')
<style>
.reg-content h2 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin-top: 2rem;
    margin-bottom: .75rem;
    padding-bottom: .5rem;
    border-bottom: 2px solid rgba(56,182,255,.15);
}
.reg-content h2:first-child {
    margin-top: 0;
}
.reg-content h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #1e40af;
    margin-top: 1.5rem;
    margin-bottom: .5rem;
}
.reg-content p {
    color: #475569;
    font-size: .97rem;
    line-height: 1.85;
    margin-bottom: 1rem;
}
.reg-content ul, .reg-content ol {
    color: #475569;
    font-size: .97rem;
    line-height: 1.85;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.reg-content li {
    margin-bottom: .35rem;
}
.reg-content strong {
    color: #1e293b;
    font-weight: 600;
}
</style>
@endpush

@endsection
