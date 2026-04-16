@extends('layouts.app')

@section('title', $subfieldLabel . ' — Estado del Arte | ConocIA')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('estado-arte.index') }}" class="text-muted">Estado del Arte</a></li>
                <li class="breadcrumb-item active text-white">{{ $subfieldLabel }}</li>
            </ol>
        </nav>
        <h1 class="fw-bold text-white mb-2" style="font-size:2.1rem;">{{ $subfieldLabel }}</h1>
        <p style="color:#94a3b8;margin:0;">Todas las ediciones del digest semanal de <strong class="text-white">{{ $subfieldLabel }}</strong>.</p>
    </div>
</section>

<div class="container py-5">

    @if($digests->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-chart-line fa-3x mb-3 d-block" style="color:var(--primary-color);opacity:.3;"></i>
        <p class="text-muted">Aún no hay digests publicados para este campo.</p>
    </div>
    @else
    <div class="d-flex flex-column gap-3">
        @foreach($digests as $digest)
        <a href="{{ route('estado-arte.show', $digest->slug) }}" class="text-decoration-none">
            <div class="profundiza-card p-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-7">
                        <div class="d-flex gap-2 align-items-center mb-2 flex-wrap">
                            <span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.7rem;">{{ $digest->subfield_label }}</span>
                            <span style="color:#64748b;font-size:.8rem;">{{ $digest->period_label }}</span>
                        </div>
                        <h5 class="fw-bold mb-1" style="color:#0f172a;font-size:.97rem;line-height:1.35;">{{ $digest->title }}</h5>
                        @if($digest->excerpt)<p style="color:#64748b;font-size:.82rem;line-height:1.5;margin:0;">{{ Str::limit($digest->excerpt, 120) }}</p>@endif
                    </div>
                    <div class="col-md-4">
                        @if(!empty($digest->key_developments))
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(array_slice($digest->key_developments, 0, 3) as $dev)
                            <span class="badge" style="background:#f1f5f9;color:#64748b;font-size:.67rem;font-weight:400;">{{ Str::limit($dev, 45) }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="col-md-1 d-none d-md-flex justify-content-end">
                        <div class="d-flex flex-column align-items-end gap-1" style="font-size:.74rem;color:#94a3b8;">
                            <span><i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? 5 }} min</span>
                            <span><i class="fas fa-eye me-1"></i>{{ number_format($digest->views) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $digests->links() }}</div>
    @endif

    <div class="mt-4">
        <a href="{{ route('estado-arte.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Ver todos los campos
        </a>
    </div>
</div>
@endsection
