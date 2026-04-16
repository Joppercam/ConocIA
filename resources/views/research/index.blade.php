@extends('layouts.app')

@section('title', 'Investigación y Análisis - ConocIA')

@php use Illuminate\Support\Str; @endphp

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
                    $hasImage = false;
                    $imageSrc = null;
                    if (!empty($research->image) &&
                        $research->image !== 'default.jpg' &&
                        !str_contains($research->image, 'default') &&
                        !str_contains($research->image, 'placeholder')) {
                        $imageSrc = Str::startsWith($research->image, 'storage/')
                            ? asset($research->image)
                            : asset('storage/research/' . $research->image);
                        $hasImage = true;
                    }
                    $rColor = is_object($research->category) ? ($research->category->color ?? 'var(--primary-color)') : 'var(--primary-color)';
                    $rIcon  = is_object($research->category) ? ($research->category->icon ?? 'fa-microscope') : 'fa-microscope';
                @endphp
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm research-card overflow-hidden">

                        {{-- Image / placeholder header --}}
                        <div class="position-relative" style="height:170px;overflow:hidden;">
                            @if($hasImage)
                            <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="d-block h-100">
                                <img src="{{ $imageSrc }}"
                                     class="w-100 h-100 research-thumb"
                                     style="object-fit:cover;"
                                     alt="{{ $research->title }}"
                                     loading="lazy"
                                     onerror="this.closest('.position-relative').innerHTML='<div class=\'d-flex align-items-center justify-content-center h-100\' style=\'background:{{ $rColor }}18;border-bottom:3px solid {{ $rColor }}\'><i class=\'fas {{ $rIcon }} fa-2x\' style=\'color:{{ $rColor }};opacity:.5;\'></i></div>';">
                            </a>
                            @else
                            <a href="{{ route('research.show', $research->slug ?? $research->id) }}"
                               class="d-flex align-items-center justify-content-center h-100 text-decoration-none"
                               style="background:{{ $rColor }}18;border-bottom:3px solid {{ $rColor }};">
                                <i class="fas {{ $rIcon }} fa-2x" style="color:{{ $rColor }};opacity:.5;"></i>
                            </a>
                            @endif

                            {{-- Category badge --}}
                            @if(is_object($research->category))
                            <div class="position-absolute bottom-0 end-0 m-2">
                                <span class="badge" style="background:{{ $rColor }};font-size:.7rem;">
                                    <i class="fas {{ $rIcon }} me-1"></i>{{ $research->category->name }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1 fw-bold" style="font-size:.95rem;line-height:1.35;">
                                <a href="{{ route('research.show', $research->slug ?? $research->id) }}"
                                   class="text-decoration-none text-dark stretched-link">
                                    {{ $research->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-2 flex-grow-1" style="font-size:.82rem;line-height:1.5;">
                                {{ Str::limit($research->excerpt ?? $research->abstract ?? '', 110) }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:.75rem;">
                                    <span><i class="fas fa-user-edit me-1"></i>{{ $research->author ?? 'Staff' }}</span>
                                    <span><i class="fas fa-eye me-1"></i>{{ number_format($research->views ?? 0) }}</span>
                                </div>
                                <span class="text-muted" style="font-size:.72rem;">
                                    {{ ($research->published_at ?? $research->created_at)->locale('es')->isoFormat('D MMM, YY') }}
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
                                 onerror="this.style.display='none'">
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
}
.research-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12) !important;
}
.research-thumb {
    transition: transform .4s ease;
}
.research-card:hover .research-thumb {
    transform: scale(1.04);
}
.research-feat-item {
    transition: background .18s ease;
}
.research-feat-item:hover {
    background: #f8f9fa;
}
</style>
@endpush
@endsection
