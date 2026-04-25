@extends('layouts.app')

@section('title', 'Estado del Arte — Digests semanales de IA | ConocIA')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">DIGEST SEMANAL</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">Estado del <span style="color:var(--primary-color);">Arte</span></h1>
                <p style="color:#94a3b8;font-size:1rem;max-width:500px;line-height:1.7;margin:0;">Un digest semanal por subcampo de la IA. Lo que avanzó, lo que importa, lo que viene.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:.8rem 1.4rem;display:flex;align-items:center;gap:.75rem;">
                    <i class="fas fa-calendar-week" style="color:var(--primary-color);font-size:1.2rem;"></i>
                    <div><div class="text-white fw-semibold" style="font-size:.9rem;">Cada semana</div><div style="color:#64748b;font-size:.78rem;">{{ $subfields->count() }} campos cubiertos</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    @if($latestBySubfield->isNotEmpty())
    <div class="mb-5">
        <p class="profundiza-section-label">Última edición por campo</p>
        <div class="d-flex flex-column gap-3">
            @foreach($latestBySubfield as $subfield => $digest)
            <a href="{{ route('estado-arte.show', $digest->slug) }}" class="text-decoration-none d-block">
                <article class="digest-list-card profundiza-card p-4 p-lg-5">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.7rem;">{{ $digest->subfield_label }}</span>
                        <span class="digest-meta-pill"><i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? 5 }} min</span>
                        @if($digest->week_start)
                            <span class="digest-meta-pill"><i class="far fa-calendar me-1"></i>{{ $digest->week_start->locale('es')->isoFormat('D MMM') }} - {{ $digest->week_end?->locale('es')->isoFormat('D MMM YYYY') }}</span>
                        @endif
                        @if(!empty($digest->views))
                            <span class="digest-meta-pill"><i class="fas fa-eye me-1"></i>{{ number_format($digest->views) }}</span>
                        @endif
                    </div>

                    <div class="row g-3 align-items-start">
                        <div class="col-lg-8">
                            <h2 class="fw-bold mb-3 digest-list-title">{{ $digest->period_label }}</h2>
                            @if($digest->excerpt)<p class="digest-list-excerpt mb-0">{{ Str::limit($digest->excerpt, 220) }}</p>@endif
                        </div>
                        <div class="col-lg-4">
                            <div class="digest-list-side">
                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing:.08em;color:#94a3b8;">Señales clave</div>
                                @if(!empty($digest->key_developments))
                                    <ul class="mb-3 digest-key-list">
                                        @foreach(array_slice($digest->key_developments, 0, 3) as $dev)
                                            <li>{{ Str::limit($dev, 95) }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <span class="digest-read-link">Leer digest <i class="fas fa-arrow-right ms-2"></i></span>
                            </div>
                        </div>
                    </div>
                </article>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($recent->isNotEmpty())
    <div class="mb-5">
        <p class="profundiza-section-label">Ediciones recientes</p>
        <div class="d-flex flex-column gap-2">
            @foreach($recent as $digest)
            <a href="{{ route('estado-arte.show', $digest->slug) }}" class="text-decoration-none">
                <div class="profundiza-card p-3 d-flex align-items-center gap-3">
                    <span class="badge flex-shrink-0" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.7rem;">{{ $digest->subfield_label }}</span>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold" style="color:#1e293b;font-size:.88rem;">{{ $digest->title }}</div>
                        <div style="color:#94a3b8;font-size:.76rem;">{{ $digest->period_label }}</div>
                    </div>
                    <div class="flex-shrink-0 d-none d-md-block">
                        @if(!empty($digest->key_developments))<span style="color:#cbd5e1;font-size:.72rem;">{{ Str::limit($digest->key_developments[0] ?? '', 50) }}</span>@endif
                    </div>
                    <small style="color:#94a3b8;font-size:.75rem;flex-shrink:0;"><i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? 5 }} min</small>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($subfields->isNotEmpty())
    <div>
        <p class="profundiza-section-label">Explorar por campo</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($subfields as $sf)
            <a href="{{ route('estado-arte.subfield', $sf->subfield) }}" class="btn btn-outline-secondary btn-sm">
                {{ $sf->subfield_label }}
                <span class="badge rounded-pill ms-1" style="background:rgba(56,182,255,.1);color:#0369a1;">{{ $sf->count }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.digest-list-card {
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    border: 1px solid #e2e8f0;
}

.digest-list-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 2.25rem rgba(15, 23, 42, .08);
    border-color: rgba(56,182,255,.28);
}

.digest-meta-pill {
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

.digest-list-title {
    color: #0f172a;
    font-size: 1.28rem;
    line-height: 1.3;
}

.digest-list-excerpt {
    color: #475569;
    font-size: .95rem;
    line-height: 1.72;
    max-width: 92%;
}

.digest-list-side {
    border-left: 1px solid #e2e8f0;
    padding-left: 1rem;
}

.digest-key-list {
    padding-left: 1rem;
    color: #475569;
    font-size: .82rem;
    line-height: 1.55;
}

.digest-read-link {
    color: var(--primary-color);
    font-size: .82rem;
    font-weight: 700;
}

@media (max-width: 991.98px) {
    .digest-list-excerpt {
        max-width: 100%;
    }

    .digest-list-side {
        border-left: 0;
        border-top: 1px solid #e2e8f0;
        padding-left: 0;
        padding-top: 1rem;
    }
}
</style>
@endpush
