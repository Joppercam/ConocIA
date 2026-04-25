@extends('layouts.app')

@section('title', 'Análisis de Fondo — Editorial profundo sobre IA | ConocIA')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">EDITORIAL</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">Análisis de <span style="color:var(--primary-color);">Fondo</span></h1>
                <p style="color:#94a3b8;font-size:1rem;max-width:540px;line-height:1.7;margin:0;">Piezas editoriales de largo aliento. Sin el ruido del ciclo de noticias — solo el análisis que importa.</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    @if($featured)
    <div class="mb-5">
        <p class="profundiza-section-label">Análisis destacado</p>
        <a href="{{ route('analisis.show', $featured->slug) }}" class="text-decoration-none d-block">
            <div class="profundiza-card-featured p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge mb-3" style="background:rgba(56,182,255,.15);color:#0369a1;font-size:.75rem;letter-spacing:.04em;">{{ strtoupper($featured->category ?? 'ANÁLISIS') }}</span>
                        <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.6rem;line-height:1.3;">{{ $featured->title }}</h2>
                        <p style="color:#475569;font-size:.95rem;line-height:1.7;margin-bottom:1.2rem;">{{ $featured->excerpt }}</p>
                        <div class="d-flex flex-wrap gap-3" style="font-size:.82rem;color:#64748b;">
                            @if($featured->author)<span><i class="fas fa-user me-1"></i>{{ $featured->author->name }}</span>@endif
                            <span><i class="fas fa-clock me-1"></i>{{ $featured->reading_time ?? 10 }} min de lectura</span>
                            @if($featured->published_at)<span>{{ $featured->published_at->locale('es')->isoFormat('D MMM YYYY') }}</span>@endif
                        </div>
                    </div>
                    <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                        <div class="btn" style="background:var(--primary-color);color:#fff;">Leer análisis <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-9">
            @if($analyses->isEmpty())
            <div class="text-center py-5"><i class="fas fa-microscope fa-3x mb-3 d-block" style="color:var(--primary-color);opacity:.3;"></i><p class="text-muted">Los análisis se publican semanalmente.</p></div>
            @else
            <p class="profundiza-section-label">Todos los análisis</p>
            <div class="d-flex flex-column gap-3">
                @foreach($analyses as $analysis)
                <a href="{{ route('analisis.show', $analysis->slug) }}" class="text-decoration-none d-block">
                    <article class="analysis-list-card profundiza-card p-4 p-lg-5">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.68rem;letter-spacing:.05em;">{{ strtoupper($analysis->category ?? 'ANÁLISIS') }}</span>
                            @if($analysis->published_at)
                                <span class="analysis-meta-pill">{{ $analysis->published_at->locale('es')->isoFormat('D MMM YYYY') }}</span>
                            @endif
                            <span class="analysis-meta-pill"><i class="fas fa-clock me-1"></i>{{ $analysis->reading_time ?? 10 }} min</span>
                            @if(!empty($analysis->views))
                                <span class="analysis-meta-pill"><i class="fas fa-eye me-1"></i>{{ number_format($analysis->views) }}</span>
                            @endif
                        </div>

                        <div class="row g-3 align-items-start">
                            <div class="col-lg-9">
                                <h3 class="fw-bold mb-3 analysis-list-title">{{ $analysis->title }}</h3>
                                <p class="analysis-list-excerpt mb-0">{{ Str::limit($analysis->excerpt, 240) }}</p>
                            </div>
                            <div class="col-lg-3">
                                <div class="analysis-list-side">
                                    <div class="small text-uppercase fw-bold mb-2" style="letter-spacing:.08em;color:#94a3b8;">Lectura editorial</div>
                                    <p class="mb-3" style="color:#475569;font-size:.82rem;line-height:1.55;">
                                        {{ Str::limit(strip_tags($analysis->excerpt ?? ''), 95) }}
                                    </p>
                                    <span class="analysis-read-link">Leer análisis <i class="fas fa-arrow-right ms-2"></i></span>
                                </div>
                            </div>
                        </div>
                    </article>
                </a>
                @endforeach
            </div>
            <div class="mt-4">{{ $analyses->links() }}</div>
            @endif
        </div>

        <div class="col-lg-3">
            <div class="sticky-top" style="top:80px;">
                @if($topics->isNotEmpty())
                <div class="profundiza-card p-4 mb-3">
                    <p class="profundiza-section-label">Temas cubiertos</p>
                    @foreach($topics as $topic => $count)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom:1px solid #f1f5f9;">
                        <span style="color:#334155;font-size:.82rem;">{{ Str::limit($topic, 26) }}</span>
                        <span class="badge rounded-pill" style="background:rgba(56,182,255,.1);color:#0369a1;">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                <a href="{{ route('conceptos.index') }}" class="btn btn-outline-secondary w-100 btn-sm mb-2"><i class="fas fa-book-open me-2"></i>Conceptos IA</a>
                <a href="{{ route('estado-arte.index') }}" class="btn btn-outline-secondary w-100 btn-sm"><i class="fas fa-chart-line me-2"></i>Estado del Arte</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.analysis-list-card {
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    border: 1px solid #e2e8f0;
}

.analysis-list-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 2.25rem rgba(15, 23, 42, .08);
    border-color: rgba(56,182,255,.28);
}

.analysis-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .32rem .7rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: .73rem;
}

.analysis-list-title {
    color: #0f172a;
    font-size: 1.3rem;
    line-height: 1.3;
}

.analysis-list-excerpt {
    color: #475569;
    font-size: .95rem;
    line-height: 1.75;
    max-width: 90%;
}

.analysis-list-side {
    border-left: 1px solid #e2e8f0;
    padding-left: 1rem;
}

.analysis-read-link {
    color: var(--primary-color);
    font-size: .82rem;
    font-weight: 700;
}

@media (max-width: 991.98px) {
    .analysis-list-excerpt {
        max-width: 100%;
    }

    .analysis-list-side {
        border-left: 0;
        border-top: 1px solid #e2e8f0;
        padding-left: 0;
        padding-top: 1rem;
    }
}
</style>
@endpush
