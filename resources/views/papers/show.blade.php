@extends('layouts.app')

@section('reading_progress', true)

@section('title', $paper->title . ' — ConocIA Papers | ConocIA')

@section('content')
<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('papers.index') }}" class="text-muted">ConocIA Papers</a></li>
                    <li class="breadcrumb-item active" style="color:#334155;">{{ Str::limit($paper->title, 40) }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.75rem;">{{ $paper->arxiv_category }}</span>
                @if($paper->difficulty_level)
                <span class="badge difficulty-badge-{{ $paper->difficulty_level }}" style="font-size:.75rem;">{{ ucfirst($paper->difficulty_level) }}</span>
                @endif
            </div>

            <h1 class="fw-bold mb-2" style="color:#0f172a;font-size:1.9rem;line-height:1.25;">{{ $paper->title }}</h1>
            <p class="mb-4" style="color:#94a3b8;font-size:.88rem;font-style:italic;">Basado en: "{{ $paper->original_title }}"</p>

            @if($paper->excerpt)
            <p style="color:#475569;font-size:1rem;line-height:1.7;margin-bottom:1.5rem;">{{ $paper->excerpt }}</p>
            @endif

            <div class="d-flex flex-wrap align-items-center gap-3 py-3 mb-5" style="border-top:2px solid #f1f5f9;border-bottom:2px solid #f1f5f9;font-size:.82rem;color:#64748b;">
                <span><i class="fas fa-users me-1"></i>{{ $paper->authorsFormatted() }}</span>
                @if($paper->arxiv_published_date)<span><i class="fas fa-calendar me-1"></i>{{ $paper->arxiv_published_date->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</span>@endif
                <span><i class="fas fa-clock me-1"></i>{{ $paper->reading_time ?? 5 }} min</span>
                <a href="{{ $paper->arxiv_url }}" target="_blank" rel="noopener" style="color:var(--primary-color);"><i class="fas fa-external-link-alt me-1"></i>Paper original</a>
            </div>

            <div class="article-content mb-5">
                {!! $paper->content !!}
            </div>

            @if(!empty($paper->key_contributions))
            <div class="profundiza-card p-4 mb-4">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-star me-2" style="color:var(--primary-color);"></i>Contribuciones principales
                </h5>
                <ul class="mb-0" style="list-style:none;padding:0;">
                    @foreach($paper->key_contributions as $c)
                    <li class="d-flex gap-2 mb-2">
                        <i class="fas fa-check-circle mt-1 flex-shrink-0" style="color:var(--primary-color);font-size:.8rem;"></i>
                        <span style="color:#334155;font-size:.9rem;line-height:1.55;">{{ $c }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(!empty($paper->practical_implications))
            <div class="profundiza-card p-4 mb-5">
                <h5 class="fw-semibold mb-3" style="color:#0f172a;font-size:.95rem;">
                    <i class="fas fa-bolt me-2" style="color:#d97706;"></i>Implicaciones prácticas
                </h5>
                <ul class="mb-0" style="list-style:none;padding:0;">
                    @foreach($paper->practical_implications as $imp)
                    <li class="d-flex gap-2 mb-2">
                        <i class="fas fa-angle-right mt-1 flex-shrink-0" style="color:#d97706;font-size:.8rem;"></i>
                        <span style="color:#334155;font-size:.9rem;line-height:1.55;">{{ $imp }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($related->isNotEmpty())
            <p class="profundiza-section-label mt-5">Más papers de {{ $paper->arxiv_category }}</p>
            <div class="row g-3">
                @foreach($related as $r)
                <div class="col-md-6">
                    <a href="{{ route('papers.show', $r->slug) }}" class="text-decoration-none d-block">
                        <div class="profundiza-card p-3">
                            <h6 class="mb-1" style="color:#1e293b;font-size:.87rem;line-height:1.35;">{{ $r->title }}</h6>
                            <small style="color:#94a3b8;">{{ $r->authorsFormatted() }}</small>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top:80px;">
                <div class="profundiza-card p-4 mb-4">
                    <p class="profundiza-section-label"><i class="fas fa-file-alt me-2" style="color:var(--primary-color);"></i>Abstract original</p>
                    <p class="mb-3" style="color:#64748b;font-size:.8rem;line-height:1.65;font-style:italic;">{{ Str::limit($paper->original_abstract, 380) }}</p>
                    <a href="{{ $paper->arxiv_url }}" target="_blank" rel="noopener" class="btn btn-sm w-100" style="background:var(--primary-color);color:#fff;">
                        <i class="fas fa-external-link-alt me-1"></i>Leer paper completo en arXiv
                    </a>
                </div>
                <a href="{{ route('papers.index') }}" class="btn w-100 btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-2"></i>Ver todos los papers
                </a>
                <a href="{{ route('conceptos.index') }}" class="btn w-100 btn-outline-secondary btn-sm">
                    <i class="fas fa-book-open me-2"></i>Conceptos IA
                </a>
            </div>
        </div>
    </div>
</div>
@include('partials.schema-breadcrumb', ['crumbs' => [['name' => 'Inicio', 'url' => url('/')'], ['name' => 'ConocIA Papers', 'url' => route('papers.index')'], ['name' => $paper->title]]])
@include('partials.schema-article', ['item' => $paper, 'routeName' => 'papers.show', 'type' => 'ScholarlyArticle', 'section' => 'ConocIA Papers'])
@endsection
