@extends('layouts.app')

@section('title', 'Investigación y Análisis - ConocIA')

@php
    use Illuminate\Support\Str;
    $metaRobots = request()->integer('page', 1) > 1 ? 'noindex, follow' : 'index, follow';
@endphp

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle' => 'Investigación y Análisis - ConocIA',
        'metaDescription' => 'Investigaciones, estudios y análisis sobre inteligencia artificial y tecnología en ConocIA.',
        'metaUrl' => route('research.index'),
        'metaRobots' => $metaRobots,
    ])
@endsection

@section('content')

{{-- Page header --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-4 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item active text-light">Investigación</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <div style="width:4px;height:32px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
            <div>
                <h1 class="mb-0 text-white fw-bold" style="font-size:1.6rem;">Investigación y Análisis</h1>
                <p class="mb-0 mt-1" style="color:#aaa;font-size:.85rem;">Avances, estudios y análisis en inteligencia artificial y tecnología</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">

        {{-- ── Main content ── --}}
        <div class="col-lg-8">
            <div class="row g-3">
                @forelse($researches as $research)
                @if($research->status === 'published' || $research->status === 'active')
                @php
                    $rColor = is_object($research->category) ? ($research->category->color ?? 'var(--primary-color)') : 'var(--primary-color)';
                    $rIcon  = is_object($research->category) ? ($research->category->icon ?? 'fa-microscope') : 'fa-microscope';
                    $researchLabel = $research->research_type ?? $research->type ?? 'Investigación';
                @endphp
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm research-card" style="--research-color:{{ $rColor }};">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if(is_object($research->category))
                                <a href="{{ route('research.category', $research->category->slug) }}"
                                   class="text-decoration-none rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 research-cat-badge"
                                   style="--research-color:{{ $rColor }};">
                                    <i class="fas {{ $rIcon }}" style="font-size:.7rem;"></i>{{ $research->category->name }}
                                </a>
                                @endif
                                <span class="research-type-badge">
                                    {{ Str::headline(str_replace(['-', '_'], ' ', $researchLabel)) }}
                                </span>
                            </div>

                            <h5 class="card-title mb-1 fw-bold" style="font-size:.95rem;line-height:1.35;">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}"
                                   class="text-decoration-none text-dark stretched-link research-title-link">
                                    {{ $research->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-2 flex-grow-1" style="font-size:.82rem;line-height:1.5;">
                                {{ Str::limit($research->excerpt ?? $research->abstract ?? '', 135) }}
                            </p>
                            <div class="research-meta-row mt-auto pt-3">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 text-muted" style="font-size:.75rem;">
                                    <span class="research-meta-pill"><i class="fas fa-user-edit me-1"></i>{{ $research->author_name ?? $research->author ?? 'Staff' }}</span>
                                    <span class="research-meta-pill"><i class="far fa-calendar me-1"></i>{{ ($research->published_at ?? $research->created_at)->locale('es')->isoFormat('D MMM, YY') }}</span>
                                    <span class="research-meta-pill"><i class="fas fa-eye me-1"></i>{{ number_format($research->views ?? 0) }}</span>
                                    @if(!empty($research->citations))
                                    <span class="research-meta-pill"><i class="fas fa-quote-right me-1"></i>{{ number_format($research->citations) }} citas</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="research-editorial-label">Investigación</span>
                                <span class="text-muted" style="font-size:.72rem;">
                                    <i class="far fa-bookmark me-1"></i>Ver análisis
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                @endif
                @empty
                <div class="col-12">
                    <div class="alert alert-light border">
                        No se encontraron artículos de investigación.
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $researches->links() }}
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
                        @php $catColor = $cat->color ?? 'var(--primary-color)'; $catIcon = $cat->icon ?? 'fa-tag'; @endphp
                        <a href="{{ route('research.category', $cat->slug) }}"
                           class="text-decoration-none rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1"
                           style="font-size:.78rem;color:{{ $catColor }};border:1px solid {{ $catColor }};transition:all .2s;">
                            <i class="fas {{ $catIcon }} me-1" style="font-size:.7rem;"></i>{{ $cat->name }}
                            @if($cat->research_count ?? false)
                            <span class="ms-1" style="opacity:.7;">{{ $cat->research_count }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Featured research --}}
            @if($featuredResearch->count())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Investigaciones destacadas</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @foreach($featuredResearch as $featured)
                    @if($featured->status === 'published' || $featured->status === 'active')
                    @php
                        $fColor = is_object($featured->category) ? ($featured->category->color ?? 'var(--primary-color)') : 'var(--primary-color)';
                        $fImg = null;
                        if (!empty($featured->image) && !str_contains($featured->image, 'default')) {
                            $fImg = Str::startsWith($featured->image, 'storage/')
                                ? asset($featured->image)
                                : asset('storage/research/' . $featured->image);
                        }
                    @endphp
                    <a href="{{ route('research.show', $featured->slug ?? $featured->id) }}" class="text-decoration-none">
                        <div class="d-flex align-items-start gap-2 px-3 py-2 research-feat-item {{ !$loop->last ? 'border-bottom' : '' }}">
                            @if($fImg)
                            <img src="{{ $fImg }}" alt="{{ $featured->title }}"
                                 class="rounded flex-shrink-0"
                                 style="width:52px;height:44px;object-fit:cover;"
                                 loading="lazy"
                                 onerror="this.src='{{ asset('images/defaults/research-default-small.jpg') }}'">
                            @else
                            <div class="rounded flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:52px;height:44px;background:{{ $fColor }}18;">
                                <i class="fas fa-microscope" style="color:{{ $fColor }};font-size:.8rem;"></i>
                            </div>
                            @endif
                            <div>
                                <p class="mb-1 text-dark" style="font-size:.8rem;line-height:1.3;">
                                    {{ Str::limit($featured->title, 60) }}
                                </p>
                                <span class="text-muted" style="font-size:.72rem;">
                                    {{ ($featured->published_at ?? $featured->created_at)->locale('es')->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('styles')
<style>
.research-card {
    transition: transform .2s ease, box-shadow .2s ease;
    position: relative;
    overflow: hidden;
}
.research-card::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 3px;
    background: var(--research-color, var(--primary-color));
    opacity: .9;
}
.research-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12) !important;
}
.research-feat-item {
    transition: background .18s ease;
}
.research-feat-item:hover {
    background: #f8f9fa;
}
.research-card .card-body {
    --research-color: var(--primary-color);
}
.research-cat-badge {
    color: var(--research-color);
    border: 1px solid color-mix(in srgb, var(--research-color) 38%, transparent);
    background: color-mix(in srgb, var(--research-color) 12%, white);
    font-size: .72rem;
    letter-spacing: .03em;
}
.research-type-badge {
    display: inline-flex;
    align-items: center;
    padding: .33rem .7rem;
    border-radius: 999px;
    background: #eef2f7;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
}
.research-title-link:hover {
    color: var(--primary-color) !important;
}
.research-meta-row {
    border-top: 1px solid rgba(15,23,42,.06);
}
.research-meta-pill {
    display: inline-flex;
    align-items: center;
    padding: .28rem .55rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
}
.research-editorial-label {
    color: #64748b;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}
</style>
@endpush
@endsection
