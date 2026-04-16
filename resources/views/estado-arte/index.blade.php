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
        <div class="row g-3">
            @foreach($latestBySubfield as $subfield => $digest)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('estado-arte.show', $digest->slug) }}" class="text-decoration-none d-block h-100">
                    <div class="profundiza-card h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.7rem;">{{ $digest->subfield_label }}</span>
                            <small style="color:#94a3b8;font-size:.72rem;"><i class="fas fa-clock me-1"></i>{{ $digest->reading_time ?? 5 }} min</small>
                        </div>
                        <h6 class="fw-bold mb-1" style="color:#0f172a;font-size:.9rem;line-height:1.35;">{{ $digest->period_label }}</h6>
                        @if($digest->excerpt)<p style="color:#64748b;font-size:.8rem;line-height:1.5;margin-bottom:.75rem;">{{ Str::limit($digest->excerpt, 95) }}</p>@endif
                        @if(!empty($digest->key_developments))
                        <div class="d-flex flex-column gap-1 mb-3">
                            @foreach(array_slice($digest->key_developments, 0, 3) as $dev)
                            <div class="d-flex gap-2 align-items-baseline">
                                <i class="fas fa-circle flex-shrink-0" style="color:var(--primary-color);font-size:.28rem;margin-top:.4rem;"></i>
                                <span style="color:#64748b;font-size:.75rem;line-height:1.4;">{{ Str::limit($dev, 60) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px solid #f1f5f9;">
                            <small style="color:#94a3b8;font-size:.72rem;"><i class="fas fa-eye me-1"></i>{{ number_format($digest->views) }}</small>
                            <small style="color:var(--primary-color);font-size:.72rem;">Ver digest <i class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </a>
            </div>
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
