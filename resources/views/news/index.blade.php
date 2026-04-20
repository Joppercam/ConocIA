@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isCategory      = isset($category);
    $metaTitle       = $isCategory
        ? $category->name . ' — Noticias de IA | ConocIA'
        : 'Últimas Noticias de Inteligencia Artificial | ConocIA';
    $metaDescription = $isCategory && $category->description
        ? Str::limit($category->description, 155)
        : 'Las últimas noticias sobre inteligencia artificial, machine learning y tecnología en español. Actualización diaria.';
    $metaUrl         = $isCategory ? route('news.category', $category->slug) : route('news.index');
@endphp

@section('title', $metaTitle)

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle'       => $metaTitle,
        'metaDescription' => $metaDescription,
        'metaKeywords'    => $isCategory
            ? $category->name . ', noticias inteligencia artificial, IA en español'
            : 'noticias inteligencia artificial, IA, machine learning, tecnología, noticias IA español',
        'metaType'        => 'website',
        'metaUrl'         => $metaUrl,
    ])
    @if($isCategory)
    @include('partials.schema-breadcrumb', ['crumbs' => [
        ['name' => 'Inicio',   'url' => url('/')],
        ['name' => 'Noticias', 'url' => route('news.index')],
        ['name' => $category->name],
    ]])
    @endif
@endsection

@section('content')

{{-- Page header --}}
<div style="background:var(--dark-bg); border-bottom:1px solid #2a2a2a;" class="py-4 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                @if(isset($category))
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}" class="text-secondary text-decoration-none">Noticias</a></li>
                    <li class="breadcrumb-item active text-light">{{ $category->name }}</li>
                @else
                    <li class="breadcrumb-item active text-light">Noticias</li>
                @endif
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <div style="width:4px;height:32px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
            <div>
                <h1 class="mb-0 text-white fw-bold" style="font-size:1.6rem;">
                    @if(isset($category))
                        {{ $category->name }}
                    @else
                        Últimas Noticias
                    @endif
                </h1>
                @if(isset($category) && $category->description)
                    <p class="mb-0 mt-1" style="color:#aaa;font-size:.85rem;">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">

        {{-- ── Main content ── --}}
        <div class="col-lg-8">

            @foreach($news as $article)
            @php
                $imageSrc = null;
                $hasImage = false;

                if (!empty($article->image) &&
                    $article->image !== 'default.jpg' &&
                    !str_contains($article->image, 'default') &&
                    !str_contains($article->image, 'placeholder')) {

                    if (Str::startsWith($article->image, ['http://', 'https://'])) {
                        $imageSrc = $article->image; // URL completa (R2, CDN, externa)
                    } elseif (Str::startsWith($article->image, 'storage/')) {
                        $imageSrc = asset($article->image);
                    } else {
                        $imageSrc = asset('storage/news/' . $article->image);
                    }

                    $hasImage = true;
                }

                $catColor = $article->category?->color ?? 'var(--primary-color)';
            @endphp

            <article class="news-card card border-0 shadow-sm mb-3 overflow-hidden">
                <div class="row g-0">

                    @if($hasImage)
                    <div class="col-md-4 position-relative" style="min-height:180px;">
                        @if(in_array($article->id, $trendingIds ?? []))
                        <span class="badge-trending"><i class="fas fa-fire me-1"></i>Trending</span>
                        @endif
                        <a href="{{ route('news.show', $article->slug) }}" class="d-block h-100" style="overflow:hidden;">
                            <img src="{{ $imageSrc }}"
                                 class="w-100 h-100 news-thumb-img"
                                 style="object-fit:cover;"
                                 alt="{{ $article->title }}"
                                 loading="lazy"
                                 onerror="this.closest('.col-md-4').remove(); this.closest('.row.g-0').querySelector('.news-content').classList.replace('col-md-8','col-12');">
                        </a>
                    </div>
                    @endif

                    <div class="col-md-{{ $hasImage ? '8' : '12' }} news-content d-flex flex-column">
                        <div class="card-body py-3 px-3 d-flex flex-column h-100">

                            {{-- Category + date --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('news.category', $article->category->slug ?? 'general') }}"
                                   class="badge text-decoration-none"
                                   style="background:{{ $catColor }};font-size:.72rem;letter-spacing:.02em;">
                                    {{ $article->category->name ?? 'General' }}
                                </a>
                                <span class="text-muted" style="font-size:.78rem;">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $article->published_at?->locale('es')->isoFormat('D MMM, YYYY') ?? $article->created_at->locale('es')->isoFormat('D MMM, YYYY') }}
                                </span>
                            </div>

                            {{-- Title + trending (sin imagen) --}}
                            <h2 class="card-title mb-2 fw-bold d-flex align-items-start gap-2" style="font-size:{{ $hasImage ? '1rem' : '1.1rem' }};line-height:1.35;">
                                <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none text-dark stretched-link flex-grow-1">
                                    {{ $article->title }}
                                </a>
                                @if(!$hasImage && in_array($article->id, $trendingIds ?? []))
                                <span class="badge flex-shrink-0 align-self-start" style="background:linear-gradient(135deg,#ff4757,#ff6b81);font-size:.65rem;">
                                    <i class="fas fa-fire"></i>
                                </span>
                                @endif
                            </h2>

                            {{-- Excerpt --}}
                            <p class="text-muted mb-3 flex-grow-1" style="font-size:.875rem;line-height:1.55;">
                                {{ Str::limit($article->summary ?? $article->excerpt ?? '', $hasImage ? 120 : 200) }}
                            </p>

                            {{-- Meta row --}}
                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <div class="d-flex align-items-center gap-3 text-muted" style="font-size:.78rem;">
                                    <span><i class="fas fa-user-edit me-1"></i>{{ $article->author ?? 'Staff' }}</span>
                                    <span><i class="fas fa-eye me-1"></i>{{ number_format($article->views) }}</span>
                                </div>
                                <div class="d-flex gap-2" style="z-index:2;position:relative;">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill bookmark-btn"
                                            data-bookmark-id="{{ $article->id }}"
                                            data-bookmark-title="{{ addslashes($article->title) }}"
                                            data-bookmark-url="{{ route('news.show', $article->slug) }}"
                                            data-bookmark-category="{{ $article->category?->name }}"
                                            data-bookmark-image="{{ $article->image ?? '' }}"
                                            title="Guardar"
                                            style="font-size:.75rem;width:30px;height:30px;padding:0;display:flex;align-items:center;justify-content:center;">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                    <a href="{{ route('news.show', $article->slug) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                       style="font-size:.75rem;">
                                        Leer más <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </article>
            @endforeach

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('vendor.pagination.bootstrap-5-small') }}
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="col-lg-4">

            {{-- Categories --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Categorías</h5>
                    </div>
                </div>
                <div class="card-body pt-1 pb-3 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($categories as $cat)
                        @php
                            $isActive = isset($category) && $category->id === $cat->id;
                            $color    = $cat->color ?? 'var(--primary-color)';
                        @endphp
                        <a href="{{ route('news.category', $cat->slug) }}"
                           class="text-decoration-none rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1"
                           style="font-size:.78rem;
                                  background:{{ $isActive ? $color : 'transparent' }};
                                  color:{{ $isActive ? '#fff' : $color }};
                                  border:1px solid {{ $color }};
                                  transition:all .2s;">
                            {{ $cat->name }}
                            @if($cat->news_count)
                            <span class="ms-1" style="opacity:.7;">{{ $cat->news_count }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Most read --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Lo más leído</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @foreach($mostReadArticles ?? [] as $idx => $mr)
                    <a href="{{ route('news.show', $mr->slug) }}" class="text-decoration-none">
                        <div class="d-flex align-items-start gap-2 px-3 py-2 most-read-item {{ !$loop->last ? 'border-bottom' : '' }}">
                            <span class="fw-bold flex-shrink-0 mt-1"
                                  style="font-size:1.1rem;min-width:22px;color:var(--primary-color);line-height:1;">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <h6 class="mb-1 text-dark" style="font-size:.82rem;line-height:1.35;">{{ $mr->title }}</h6>
                                <span class="text-muted" style="font-size:.75rem;">
                                    <i class="fas fa-eye me-1"></i>{{ number_format($mr->views) }}
                                    @if($mr->category)
                                    <span class="ms-2 badge"
                                          style="font-size:.65rem;background:{{ $mr->category->color ?? 'var(--primary-color)' }};">
                                        {{ $mr->category->name }}
                                    </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Tags cloud (if available via related logic) --}}
            @if(!empty($popularTags) && $popularTags->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Tags populares</h5>
                    </div>
                </div>
                <div class="card-body pt-1 pb-3 px-3">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                        <a href="{{ route('news.tag', $tag->slug) }}"
                           class="badge bg-light text-dark text-decoration-none border"
                           style="font-size:.75rem;font-weight:500;">
                            #{{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('styles')
<style>
/* ── News index cards ── */
.news-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.news-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12) !important;
}
.news-thumb-img {
    transition: transform .4s ease;
}
.news-card:hover .news-thumb-img {
    transform: scale(1.04);
}

/* Most read hover */
.most-read-item {
    transition: background .18s ease;
}
.most-read-item:hover {
    background: #f8f9fa;
}

/* Pagination SVG fix */
.pagination svg { width:18px; height:18px; }

/* Category pill hover */
.sidebar-cat-pill:hover {
    filter: brightness(1.1);
}
</style>
@endpush
@endsection
