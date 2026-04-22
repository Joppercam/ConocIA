@extends('layouts.app')

@section('title', 'Columnistas — ConocIA')
@section('meta_description', 'Investigadores, académicos y expertos en inteligencia artificial que escriben en ConocIA. Perspectivas profundas desde Chile y Latinoamérica.')

@section('content')

<div style="background:linear-gradient(135deg,#0a1020 0%,#0d1b2e 100%);border-bottom:1px solid #1e2430;">
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider-color:#6c757d;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('columns.index') }}" class="text-secondary">Columnas</a></li>
                <li class="breadcrumb-item active text-light">Columnistas</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge mb-3 px-3 py-2" style="background:rgba(56,182,255,.15);color:var(--primary-color);font-size:.75rem;letter-spacing:.05em;">
                    <i class="fas fa-pen-fancy me-2"></i>VOCES EXPERTAS
                </span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2rem;line-height:1.2;">
                    Columnistas de <span style="color:var(--primary-color);">ConocIA</span>
                </h1>
                <p style="color:#94a3b8;font-size:1rem;line-height:1.7;" class="mb-4">
                    Investigadores, académicos y profesionales que analizan la inteligencia artificial desde Chile y Latinoamérica. Sus columnas van más allá de la noticia: explican, cuestionan y proyectan.
                </p>
                <a href="{{ route('columns.write-for-us') }}"
                   class="btn rounded-pill px-4 py-2"
                   style="background:var(--primary-color);color:#fff;font-size:.88rem;">
                    <i class="fas fa-pencil-alt me-2"></i>Escribí para ConocIA
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">

    @if($featured->isNotEmpty())
    <h2 class="fw-bold mb-4" style="font-size:1.1rem;">
        <i class="fas fa-star me-2" style="color:#fbbf24;"></i>Columnistas destacados
    </h2>
    <div class="row g-4 mb-5">
        @foreach($featured as $user)
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('columns.author', $user->id) }}" class="text-decoration-none d-block h-100">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="transition:transform .15s,box-shadow .15s;"
                     onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.12)'"
                     onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <img src="{{ $user->avatar_url ?? $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=38b6ff&color=fff&size=80' }}"
                             alt="{{ $user->name }}"
                             style="width:64px;height:64px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid rgba(56,182,255,.3);">
                        <div>
                            <div class="fw-bold text-dark mb-0" style="font-size:.95rem;line-height:1.3;">
                                {{ $user->full_title }}
                            </div>
                            @if($user->institution)
                            <div style="color:#38b6ff;font-size:.75rem;font-weight:500;">{{ $user->institution }}</div>
                            @endif
                            @if($user->expertise_area)
                            <div style="color:#64748b;font-size:.72rem;">{{ $user->expertise_area }}</div>
                            @endif
                        </div>
                    </div>
                    @if($user->bio)
                    <p class="text-muted mb-3" style="font-size:.82rem;line-height:1.55;">{{ Str::limit($user->bio, 130) }}</p>
                    @endif
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-2" style="border-top:1px solid #f1f5f9;">
                        <span style="color:#64748b;font-size:.75rem;">
                            <i class="fas fa-pen-fancy me-1" style="color:var(--primary-color);"></i>
                            {{ $user->columns_count }} {{ $user->columns_count === 1 ? 'columna' : 'columnas' }}
                        </span>
                        @if($user->linkedin_url)
                        <a href="{{ $user->linkedin_url }}" target="_blank" rel="noopener"
                           onclick="event.stopPropagation();"
                           style="color:#0a66c2;font-size:.8rem;" class="text-decoration-none">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @endif

    @if($all->isNotEmpty())
    <h2 class="fw-bold mb-4" style="font-size:1.05rem;color:#64748b;">
        <i class="fas fa-users me-2"></i>Todos los columnistas
    </h2>
    <div class="row g-3">
        @foreach($all as $user)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('columns.author', $user->id) }}" class="text-decoration-none d-flex align-items-center gap-3 p-3 rounded-2"
               style="border:1px solid #e9ecef;transition:border-color .2s,background .2s;"
               onmouseover="this.style.borderColor='rgba(56,182,255,.4)';this.style.background='#f8fafc'"
               onmouseout="this.style.borderColor='#e9ecef';this.style.background=''">
                <img src="{{ $user->avatar_url ?? $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=64748b&color=fff&size=60' }}"
                     alt="{{ $user->name }}"
                     style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                <div class="min-w-0">
                    <div class="fw-semibold text-dark" style="font-size:.88rem;">{{ $user->full_title }}</div>
                    @if($user->institution)
                    <div style="color:#64748b;font-size:.72rem;">{{ $user->institution }}</div>
                    @endif
                    <div style="color:#94a3b8;font-size:.7rem;">{{ $user->columns_count }} {{ $user->columns_count === 1 ? 'columna' : 'columnas' }}</div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @endif

    @if($featured->isEmpty() && $all->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="fas fa-pen-fancy fa-3x mb-3 d-block opacity-25"></i>
        <p>Próximamente columnistas expertos.</p>
        <a href="{{ route('columns.write-for-us') }}" class="btn btn-primary rounded-pill px-4">
            Sé el primero en escribir
        </a>
    </div>
    @endif

</div>

@include('partials.schema-breadcrumb', ['crumbs' => [
    ['name' => 'Inicio', 'url' => url('/')],
    ['name' => 'Columnas', 'url' => route('columns.index')],
    ['name' => 'Columnistas'],
]])
@endsection
