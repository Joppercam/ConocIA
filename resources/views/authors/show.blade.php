@extends('layouts.app')

@section('title', $authorName . ' — Autor en ConocIA')
@section('meta_description', 'Artículos escritos por ' . $authorName . ' en ConocIA, el portal de inteligencia artificial en español.')

@push('styles')
<style>
.author-hero {
    background: linear-gradient(135deg, #0a1020 0%, #16213e 100%);
    padding: 3.5rem 0 2.5rem;
    border-bottom: 1px solid rgba(56,182,255,.15);
}
.author-avatar {
    width: 90px; height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(56,182,255,.4);
}
.author-avatar-placeholder {
    width: 90px; height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e3a5f, #0f2744);
    border: 3px solid rgba(56,182,255,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #38b6ff; font-weight: 700;
}
.article-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: box-shadow .2s, transform .2s;
}
.article-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
</style>
@endpush

@section('content')

{{-- Hero autor --}}
<div class="author-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-secondary">Inicio</a></li>
                <li class="breadcrumb-item active text-light">{{ $authorName }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-4">
            @if($authorUser && $authorUser->profile_photo)
                <img src="{{ asset('storage/' . $authorUser->profile_photo) }}"
                     alt="{{ $authorName }}" class="author-avatar">
            @else
                <div class="author-avatar-placeholder flex-shrink-0">
                    {{ strtoupper(substr($authorName, 0, 1)) }}
                </div>
            @endif

            <div>
                <span class="badge mb-2" style="background:rgba(56,182,255,.15);color:#38b6ff;font-size:.72rem;letter-spacing:.08em;">AUTOR</span>
                <h1 class="text-white fw-bold mb-1" style="font-size:1.8rem;">{{ $authorName }}</h1>
                @if($authorUser && $authorUser->bio)
                    <p class="mb-2" style="color:#94a3b8;font-size:.92rem;max-width:540px;">{{ $authorUser->bio }}</p>
                @endif
                <div class="d-flex gap-3 mt-2">
                    @if($authorUser && $authorUser->twitter)
                        <a href="https://twitter.com/{{ ltrim($authorUser->twitter, '@') }}" target="_blank" rel="noopener" class="text-secondary" style="font-size:.85rem;"><i class="fab fa-twitter me-1"></i>{{ $authorUser->twitter }}</a>
                    @endif
                    @if($authorUser && $authorUser->linkedin)
                        <a href="{{ $authorUser->linkedin }}" target="_blank" rel="noopener" class="text-secondary" style="font-size:.85rem;"><i class="fab fa-linkedin me-1"></i>LinkedIn</a>
                    @endif
                    @if($authorUser && $authorUser->website)
                        <a href="{{ $authorUser->website }}" target="_blank" rel="noopener" class="text-secondary" style="font-size:.85rem;"><i class="fas fa-globe me-1"></i>Sitio web</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-3" style="color:#64748b;font-size:.85rem;">
            <i class="fas fa-newspaper me-1"></i> {{ $articles->total() }} artículo{{ $articles->total() !== 1 ? 's' : '' }} publicados
        </div>
    </div>
</div>

{{-- Artículos --}}
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4" style="font-size:1.1rem;color:#0a1020;text-transform:uppercase;letter-spacing:.06em;">
                <span style="display:inline-block;width:4px;height:1em;background:#38b6ff;border-radius:2px;margin-right:8px;vertical-align:middle;"></span>
                Artículos de {{ $authorName }}
            </h2>

            @forelse($articles as $article)
            <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none d-block mb-3">
                <div class="article-card p-3 d-flex gap-3 align-items-start">
                    @if($article->image)
                    <img src="{{ asset(\Illuminate\Support\Str::startsWith($article->image,'storage/') ? $article->image : 'storage/'.$article->image) }}"
                         alt="{{ $article->title }}" class="rounded flex-shrink-0"
                         style="width:90px;height:68px;object-fit:cover;"
                         onerror="this.style.display='none'">
                    @endif
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @if($article->category)
                            <span class="badge" style="background:{{ $article->category->color ?? '#38b6ff' }};font-size:.7rem;">
                                {{ $article->category->name }}
                            </span>
                            @endif
                            <span class="text-muted" style="font-size:.78rem;">
                                {{ $article->published_at?->locale('es')->isoFormat('D MMM YYYY') }}
                            </span>
                        </div>
                        <h3 class="fw-semibold text-dark mb-1" style="font-size:.95rem;line-height:1.4;">{{ $article->title }}</h3>
                        @if($article->excerpt)
                        <p class="text-muted mb-0" style="font-size:.83rem;line-height:1.5;">{{ \Illuminate\Support\Str::limit($article->excerpt, 130) }}</p>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-pen-nib fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aún no hay artículos publicados por este autor.</p>
            </div>
            @endforelse

            <div class="mt-4">
                {{ $articles->links('pagination::bootstrap-5') }}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="rounded-3 p-4" style="background:linear-gradient(135deg,#0a1020,#16213e);border:1px solid rgba(56,182,255,.2);">
                <p class="text-white fw-bold mb-1" style="font-size:.95rem;">Suscríbete al newsletter</p>
                <p class="mb-3" style="color:#94a3b8;font-size:.82rem;">Recibe los mejores artículos en tu correo.</p>
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

{{-- JSON-LD Breadcrumb --}}
@include('partials.schema-breadcrumb', ['crumbs' => [
    ['name' => 'Inicio', 'url' => url('/')],
    ['name' => 'Autores', 'url' => url('/autores')],
    ['name' => $authorName],
]])

{{-- JSON-LD Person --}}
@if($authorUser)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Person",
    "name": "{{ $authorUser->name }}",
    "url": "{{ route('authors.show', $authorName) }}",
    @if($authorUser->bio)"description": "{{ addslashes($authorUser->bio) }}",@endif
    @if($authorUser->profile_photo)"image": "{{ asset('storage/' . $authorUser->profile_photo) }}",@endif
    "worksFor": {
        "@type": "Organization",
        "name": "ConocIA",
        "@id": "{{ url('/') }}/#organization"
    }
}
</script>
@endif

@endsection
