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
        <div class="d-flex flex-column gap-3">
            @foreach($featured as $paper)
            <a href="{{ route('papers.show', $paper->slug) }}" class="text-decoration-none d-block">
                <article class="paper-list-card profundiza-card-featured p-4 p-lg-5">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge" style="background:var(--primary-color);color:#fff;font-size:.7rem;">{{ $paper->arxiv_category }}</span>
                        @if($paper->difficulty_level)<span class="badge difficulty-badge-{{ Str::ascii(strtolower($paper->difficulty_level)) }}" style="font-size:.7rem;">{{ ucfirst($paper->difficulty_level) }}</span>@endif
                        <span class="paper-meta-pill"><i class="fas fa-clock me-1"></i>{{ $paper->reading_time ?? 5 }} min</span>
                        @if($paper->arxiv_published_date)
                            <span class="paper-meta-pill"><i class="far fa-calendar me-1"></i>{{ $paper->arxiv_published_date->locale('es')->isoFormat('D MMM YYYY') }}</span>
                        @endif
                    </div>

                    <div class="row g-3 align-items-start">
                        <div class="col-lg-8">
                            <h2 class="fw-bold mb-3 paper-list-title">{{ $paper->title }}</h2>
                            <p class="mb-2 paper-original-title">{{ Str::limit($paper->original_title, 120) }}</p>
                            <p class="paper-list-excerpt mb-0">{{ Str::limit($paper->excerpt, 220) }}</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="paper-list-side">
                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing:.08em;color:#94a3b8;">Autores</div>
                                <p class="mb-3" style="color:#475569;font-size:.82rem;line-height:1.55;">{{ Str::limit($paper->authorsFormatted(), 110) }}</p>
                                <span class="paper-read-link">Leer paper <i class="fas fa-arrow-right ms-2"></i></span>
                            </div>
                        </div>
                    </div>
                </article>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($papers->isEmpty())
    <div class="text-center py-5"><i class="fas fa-file-alt fa-3x mb-3 d-block" style="color:var(--primary-color);opacity:.3;"></i><p class="text-muted">Los papers se importan automáticamente dos veces por semana desde arXiv.</p></div>
    @else
    <p class="profundiza-section-label">Todos los papers</p>
    <div class="d-flex flex-column gap-3">
        @foreach($papers as $paper)
        <a href="{{ route('papers.show', $paper->slug) }}" class="text-decoration-none d-block">
            <article class="paper-list-card profundiza-card p-4 p-lg-5">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge" style="background:rgba(56,182,255,.1);color:#0369a1;font-size:.65rem;">{{ $paper->arxiv_category }}</span>
                    @if($paper->difficulty_level)<span class="badge difficulty-badge-{{ Str::ascii(strtolower($paper->difficulty_level)) }}" style="font-size:.65rem;">{{ ucfirst($paper->difficulty_level) }}</span>@endif
                    <span class="paper-meta-pill"><i class="fas fa-clock me-1"></i>{{ $paper->reading_time ?? 5 }} min</span>
                    @if($paper->arxiv_published_date)
                        <span class="paper-meta-pill"><i class="far fa-calendar me-1"></i>{{ $paper->arxiv_published_date->locale('es')->isoFormat('D MMM YYYY') }}</span>
                    @endif
                </div>

                <div class="row g-3 align-items-start">
                    <div class="col-lg-8">
                        <h3 class="fw-bold mb-3 paper-list-title">{{ $paper->title }}</h3>
                        <p class="mb-2 paper-original-title">{{ Str::limit($paper->original_title, 120) }}</p>
                        <p class="paper-list-excerpt mb-0">{{ Str::limit($paper->excerpt, 230) }}</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="paper-list-side">
                            <div class="small text-uppercase fw-bold mb-2" style="letter-spacing:.08em;color:#94a3b8;">Autores</div>
                            <p class="mb-3" style="color:#475569;font-size:.82rem;line-height:1.55;">{{ Str::limit($paper->authorsFormatted(), 110) }}</p>
                            <span class="paper-read-link">Leer paper <i class="fas fa-arrow-right ms-2"></i></span>
                        </div>
                    </div>
                </div>
            </article>
        </a>
        @endforeach
    </div>
    <div class="mt-4">{{ $papers->links() }}</div>
    @endif

</div>
@endsection

@push('styles')
<style>
.paper-list-card {
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    border: 1px solid #e2e8f0;
}

.paper-list-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 2.25rem rgba(15, 23, 42, .08);
    border-color: rgba(56,182,255,.28);
}

.paper-meta-pill {
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

.paper-list-title {
    color: #0f172a;
    font-size: 1.28rem;
    line-height: 1.3;
}

.paper-original-title {
    color: #94a3b8;
    font-size: .82rem;
    font-style: italic;
}

.paper-list-excerpt {
    color: #475569;
    font-size: .95rem;
    line-height: 1.72;
    max-width: 92%;
}

.paper-list-side {
    border-left: 1px solid #e2e8f0;
    padding-left: 1rem;
}

.paper-read-link {
    color: var(--primary-color);
    font-size: .82rem;
    font-weight: 700;
}

@media (max-width: 991.98px) {
    .paper-list-excerpt {
        max-width: 100%;
    }

    .paper-list-side {
        border-left: 0;
        border-top: 1px solid #e2e8f0;
        padding-left: 0;
        padding-top: 1rem;
    }
}
</style>
@endpush
