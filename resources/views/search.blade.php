@extends('layouts.app')

@section('title', 'Búsqueda: ' . $query . ' — ConocIA')

@push('styles')
<style>
.search-hero {
    background: linear-gradient(135deg, #0a1020 0%, #16213e 100%);
    padding: 3rem 0 2rem;
    border-bottom: 1px solid rgba(56,182,255,.15);
}
.search-result-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: box-shadow .2s, transform .2s;
}
.search-result-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.search-type-badge {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 3px 9px;
    border-radius: 30px;
}
.result-highlight { background: rgba(56,182,255,.15); border-radius: 3px; padding: 0 2px; }
.section-divider { border-color: rgba(56,182,255,.2); margin: 2rem 0; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="search-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider-color:#6c757d;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary">Inicio</a></li>
                <li class="breadcrumb-item active text-light">Búsqueda</li>
            </ol>
        </nav>
        <h1 class="text-white fw-bold mb-1" style="font-size:1.8rem;">
            Resultados para <span style="color:#38b6ff;">"{{ $query }}"</span>
        </h1>
        @php $total = $news->total() + $researches->total() + $guestPosts->total(); @endphp
        <p class="text-secondary mb-3">{{ number_format($total) }} resultado{{ $total !== 1 ? 's' : '' }} encontrados</p>

        {{-- Nueva búsqueda --}}
        <form action="{{ route('search') }}" method="GET" class="d-flex gap-2" style="max-width:540px;">
            <input type="text" name="query" value="{{ $query }}" placeholder="Buscar en ConocIA…"
                   class="form-control" style="border-radius:8px;border:1px solid rgba(56,182,255,.3);background:rgba(255,255,255,.05);color:#fff;">
            <button class="btn btn-primary px-4" style="background:#38b6ff;border:none;border-radius:8px;">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

{{-- Resultados --}}
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Noticias --}}
            @if($news->count() > 0)
            <h2 class="fw-bold mb-3" style="font-size:1.1rem;color:#0a1020;">
                <i class="fas fa-newspaper text-primary me-2"></i>Noticias
                <span class="badge bg-primary ms-2" style="font-size:.75rem;">{{ $news->total() }}</span>
            </h2>
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($news as $article)
                <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none">
                    <div class="search-result-card p-3 d-flex gap-3 align-items-start">
                        @if($article->image)
                        <img src="{{ asset(Str::startsWith($article->image,'storage/') ? $article->image : 'storage/'.$article->image) }}"
                             alt="{{ $article->title }}" class="rounded flex-shrink-0"
                             style="width:80px;height:60px;object-fit:cover;"
                             onerror="this.style.display='none'">
                        @endif
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="search-type-badge text-white" style="background:{{ $article->category?->color ?? '#38b6ff' }}">
                                    {{ $article->category?->name ?? 'Noticia' }}
                                </span>
                                <span class="text-muted" style="font-size:.8rem;">
                                    {{ $article->published_at?->locale('es')->diffForHumans() }}
                                </span>
                            </div>
                            <h3 class="fw-semibold text-dark mb-1" style="font-size:.95rem;line-height:1.4;">{{ $article->title }}</h3>
                            @if($article->excerpt)
                            <p class="text-muted mb-0" style="font-size:.85rem;line-height:1.5;">{{ Str::limit($article->excerpt, 140) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            {{ $news->appends(['query' => $query])->links('pagination::bootstrap-5') }}
            @endif

            {{-- Investigación --}}
            @if($researches->count() > 0)
            <hr class="section-divider">
            <h2 class="fw-bold mb-3" style="font-size:1.1rem;color:#0a1020;">
                <i class="fas fa-flask text-primary me-2"></i>Investigación y Análisis
                <span class="badge bg-primary ms-2" style="font-size:.75rem;">{{ $researches->total() }}</span>
            </h2>
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($researches as $research)
                <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none">
                    <div class="search-result-card p-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="search-type-badge bg-secondary text-white">Investigación</span>
                            <span class="text-muted" style="font-size:.8rem;">
                                {{ $research->published_at?->locale('es')->diffForHumans() }}
                            </span>
                        </div>
                        <h3 class="fw-semibold text-dark mb-1" style="font-size:.95rem;line-height:1.4;">{{ $research->title }}</h3>
                        @if($research->abstract)
                        <p class="text-muted mb-0" style="font-size:.85rem;line-height:1.5;">{{ Str::limit($research->abstract, 140) }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            {{ $researches->appends(['query' => $query])->links('pagination::bootstrap-5') }}
            @endif

            {{-- Colaboraciones --}}
            @if($guestPosts->count() > 0)
            <hr class="section-divider">
            <h2 class="fw-bold mb-3" style="font-size:1.1rem;color:#0a1020;">
                <i class="fas fa-pen-nib text-primary me-2"></i>Colaboraciones
                <span class="badge bg-primary ms-2" style="font-size:.75rem;">{{ $guestPosts->total() }}</span>
            </h2>
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($guestPosts as $post)
                <div class="search-result-card p-3">
                    <span class="search-type-badge" style="background:#6c757d;color:#fff;">Colaboración</span>
                    <h3 class="fw-semibold text-dark mt-2 mb-1" style="font-size:.95rem;">{{ $post->title }}</h3>
                    @if($post->excerpt)
                    <p class="text-muted mb-0" style="font-size:.85rem;">{{ Str::limit($post->excerpt, 140) }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Sin resultados --}}
            @if($news->count() === 0 && $researches->count() === 0 && $guestPosts->count() === 0)
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3 class="fw-semibold text-muted">No encontramos resultados para "{{ $query }}"</h3>
                <p class="text-muted">Intenta con otras palabras clave o navega por nuestras <a href="{{ route('news.index') }}">últimas noticias</a>.</p>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
                <h5 class="fw-bold mb-3" style="font-size:.9rem;text-transform:uppercase;letter-spacing:.06em;color:#38b6ff;">
                    <i class="fas fa-bolt me-1"></i> Sugerencias
                </h5>
                <ul class="list-unstyled mb-0" style="font-size:.88rem;">
                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Verifica la ortografía</li>
                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Usa términos más generales</li>
                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Busca por categorías</li>
                </ul>
            </div>

            {{-- Newsletter sidebar --}}
            <div class="rounded-3 p-4" style="background:linear-gradient(135deg,#0a1020,#16213e);border:1px solid rgba(56,182,255,.2);">
                <p class="text-white fw-bold mb-1" style="font-size:.95rem;">Recibe lo mejor de ConocIA</p>
                <p class="mb-3" style="color:#94a3b8;font-size:.82rem;">Noticias y análisis de IA directo a tu correo.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <input type="email" name="email" placeholder="tu@correo.com" required
                           class="form-control mb-2" style="border-radius:8px;font-size:.85rem;">
                    <button type="submit" class="btn btn-primary w-100" style="background:#38b6ff;border:none;border-radius:8px;font-size:.85rem;">
                        Suscribirme
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.schema-breadcrumb', ['crumbs' => [
    ['name' => 'Inicio', 'url' => url('/')],
    ['name' => 'Búsqueda: ' . $query],
]])
@endsection
