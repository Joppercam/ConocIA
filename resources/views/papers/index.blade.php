@extends('layouts.app')

@section('title', 'ConocIA Papers — Ciencia de IA accesible en español | ConocIA')

@section('content')

<section class="profundiza-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(56,182,255,.2);color:#7dd3f0;font-size:.78rem;letter-spacing:.06em;">INVESTIGACIÓN</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.2rem;line-height:1.2;">ConocIA <span style="color:var(--primary-color);">Papers</span></h1>
                <p style="color:#94a3b8;font-size:1rem;max-width:500px;line-height:1.7;margin:0;">Papers de arXiv explicados en español. Ciencia de frontera, sin la barrera del idioma.</p>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-end">
                <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;padding:.8rem 1.2rem;font-size:.82rem;display:flex;align-items:center;gap:.75rem;">
                    <i class="fas fa-sync-alt" style="color:var(--primary-color);"></i>
                    <span style="color:#94a3b8;">Actualizado 2 veces por semana desde arXiv</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">

    @if($arxivCategories->isNotEmpty())
    <div class="d-flex flex-wrap gap-2 mb-5">
        <a href="{{ route('papers.index') }}" class="btn btn-sm {{ !isset($category) ? 'text-white' : 'btn-outline-secondary' }}" style="{{ !isset($category) ? 'background:var(--primary-color);' : '' }}">Todos</a>
        @foreach($arxivCategories as $cat => $count)
        <a href="{{ route('papers.category', $cat) }}" class="btn btn-sm {{ (isset($category) && $category === $cat) ? 'text-white' : 'btn-outline-secondary' }}" style="{{ (isset($category) && $category === $cat) ? 'background:var(--primary-color);' : '' }}">
            {{ $cat }} <span class="ms-1 opacity-75">{{ $count }}</span>
        </a>
        @endforeach
    </div>
    @endif

    @if(isset($featured) && $featured->isNotEmpty())
    <div class="mb-5">
        <p class="profundiza-section-label">Papers destacados</p>
        <div class="row g-3">
            @foreach($featured as $paper)
            <div class="col-md-6">
                <a href="{{ route('papers.show', $paper->slug) }}" class="text-decoration-none d-block h-100">
                    <div class="profundiza-card-featured h-100 p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.7rem;">{{ $paper->arxiv_category }}</span>
                                @if($paper->difficulty_level)<span class="badge difficulty-badge-{{ $paper->difficulty_level }}" style="font-size:.7rem;">{{ ucfirst($paper->difficulty_level) }}</span>@endif
                            </div>
                            <small style="color:#94a3b8;"><i class="fas fa-clock me-1"></i>{{ $paper->reading_time ?? 5 }} min</small>
                        </div>
                        <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:.97rem;line-height:1.35;">{{ $paper->title }}</h5>
                        <p class="mb-2" style="color:#94a3b8;font-size:.78rem;font-style:italic;">{{ Str::limit($paper->original_title, 80) }}</p>
                        <p style="color:#475569;font-size:.85rem;line-height:1.5;margin:0;">{{ Str::limit($paper->excerpt, 100) }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($papers->isEmpty())
    <div class="text-center py-5"><i class="fas fa-file-alt fa-3x mb-3 d-block" style="color:var(--primary-color);opacity:.3;"></i><p class="text-muted">Los papers se importan automáticamente dos veces por semana desde arXiv.</p></div>
    @else
    <p class="profundiza-section-label">Todos los papers</p>
    <div class="row g-3">
        @foreach($papers as $paper)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('papers.show', $paper->slug) }}" class="text-decoration-none d-block h-100">
                <div class="profundiza-card h-100 p-3">
                    <div class="d-flex gap-2 mb-2 flex-wrap">
                        <span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.65rem;">{{ $paper->arxiv_category }}</span>
                        @if($paper->difficulty_level)<span class="badge difficulty-badge-{{ $paper->difficulty_level }}" style="font-size:.65rem;">{{ ucfirst($paper->difficulty_level) }}</span>@endif
                    </div>
                    <h6 class="fw-bold mb-1" style="color:#0f172a;font-size:.88rem;line-height:1.35;">{{ $paper->title }}</h6>
                    <p class="mb-2" style="color:#94a3b8;font-size:.73rem;font-style:italic;">{{ Str::limit($paper->original_title, 65) }}</p>
                    <p style="color:#64748b;font-size:.78rem;line-height:1.45;margin:0 0 .5rem;">{{ Str::limit($paper->excerpt, 80) }}</p>
                    <div class="d-flex justify-content-between pt-2" style="border-top:1px solid #f1f5f9;font-size:.7rem;color:#94a3b8;">
                        <span>{{ $paper->authorsFormatted() }}</span>
                        <span>{{ $paper->arxiv_published_date?->format('d M Y') }}</span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $papers->links() }}</div>
    @endif

</div>
@endsection
