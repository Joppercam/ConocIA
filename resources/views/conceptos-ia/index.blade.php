@extends('layouts.app')

@section('title', 'Conceptos IA — Enciclopedia de Inteligencia Artificial | ConocIA')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">ENCICLOPEDIA</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">Conceptos <span style="color:var(--primary-color);">IA</span></h1>
                <p style="color:#94a3b8;font-size:1rem;max-width:500px;line-height:1.7;margin:0;">Explicaciones profundas de los conceptos que moldean la inteligencia artificial. Sin jerga innecesaria, con todo el rigor técnico.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-4 align-items-center">
                <div class="text-center"><div class="fw-bold" style="font-size:2rem;color:var(--primary-color);">{{ $conceptos->total() }}</div><div style="color:#64748b;font-size:.8rem;">conceptos</div></div>
                <div class="text-center"><div class="fw-bold" style="font-size:2rem;color:var(--primary-color);">{{ $categories->count() }}</div><div style="color:#64748b;font-size:.8rem;">áreas</div></div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    @if($featured->isNotEmpty())
    <div class="mb-5">
        <p class="profundiza-section-label">Conceptos destacados</p>
        <div class="row g-3">
            @foreach($featured as $c)
            <div class="col-md-4">
                <a href="{{ route('conceptos.show', $c->slug) }}" class="text-decoration-none d-block h-100">
                    <div class="profundiza-card-featured h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            @if($c->category)<span class="badge" style="background:rgba(56,182,255,.15);color:#0369a1;font-size:.72rem;">{{ $c->category }}</span>@endif
                            <small style="color:#94a3b8;font-size:.75rem;"><i class="fas fa-clock me-1"></i>{{ $c->reading_time ?? 5 }} min</small>
                        </div>
                        <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;line-height:1.35;">{{ $c->title }}</h5>
                        <p style="color:#475569;font-size:.84rem;line-height:1.55;margin:0;">{{ Str::limit($c->definition ?? strip_tags($c->excerpt), 110) }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-9">
            @if($conceptos->isEmpty())
            <div class="text-center py-5"><i class="fas fa-book-open fa-3x mb-3 d-block" style="color:var(--primary-color);opacity:.3;"></i><p class="text-muted">Los conceptos se están generando. Vuelve pronto.</p></div>
            @else
            <p class="profundiza-section-label">Todos los conceptos</p>
            <div class="row g-3">
                @foreach($conceptos as $concepto)
                <div class="col-md-6">
                    <a href="{{ route('conceptos.show', $concepto->slug) }}" class="text-decoration-none d-block h-100">
                        <div class="profundiza-card h-100 p-3 d-flex gap-3 align-items-start">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3" style="width:40px;height:40px;background:rgba(56,182,255,.1);margin-top:2px;">
                                <i class="fas fa-brain" style="color:var(--primary-color);font-size:.85rem;"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                @if($concepto->category)<span class="badge mb-1" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.65rem;">{{ $concepto->category }}</span>@endif
                                <h6 class="fw-bold mb-1" style="color:#0f172a;font-size:.88rem;line-height:1.3;">{{ $concepto->title }}</h6>
                                <p style="color:#64748b;font-size:.78rem;line-height:1.45;margin:0;">{{ Str::limit($concepto->definition ?? strip_tags($concepto->excerpt), 85) }}</p>
                            </div>
                            <i class="fas fa-chevron-right flex-shrink-0 mt-2" style="color:#cbd5e1;font-size:.65rem;"></i>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $conceptos->links() }}</div>
            @endif
        </div>

        <div class="col-lg-3">
            <div class="sticky-top" style="top:80px;">
                <div class="profundiza-card p-4 mb-3">
                    <p class="profundiza-section-label mb-3">Por área</p>
                    @foreach($categories as $cat => $count)
                    <a href="{{ route('conceptos.index', ['category' => $cat]) }}" class="d-flex justify-content-between align-items-center mb-2 pb-2 text-decoration-none" style="border-bottom:1px solid #f1f5f9;">
                        <span style="color:#334155;font-size:.84rem;">{{ $cat }}</span>
                        <span class="badge rounded-pill" style="background:rgba(56,182,255,.1);color:#0369a1;">{{ $count }}</span>
                    </a>
                    @endforeach
                </div>
                <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="fas fa-microscope me-2"></i>Análisis de Fondo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
