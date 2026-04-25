@extends('layouts.app')

@php
    use Illuminate\Support\Str;
    $archiveMetaUrl = route('news.archive', array_filter([
        'year' => $year,
        'month' => $month,
    ]));
    $archiveMetaRobots = request()->integer('page', 1) > 1 ? 'noindex, follow' : 'index, follow';
@endphp

@section('title', 'Archivo — ' . $archiveTitle . ' | ConocIA')

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle' => 'Archivo — ' . $archiveTitle . ' | ConocIA',
        'metaDescription' => 'Archivo editorial de noticias de ConocIA para ' . $archiveTitle . '.',
        'metaUrl' => $archiveMetaUrl,
        'metaRobots' => $archiveMetaRobots,
    ])
@endsection

@section('content')
{{-- Page header --}}
<div style="background:var(--dark-bg);border-bottom:1px solid #2a2a2a;" class="py-4 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('news.index') }}" class="text-secondary text-decoration-none">Noticias</a></li>
                <li class="breadcrumb-item active text-light">Archivo</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="width:4px;height:32px;background:var(--primary-color);border-radius:2px;flex-shrink:0;"></div>
                <div>
                    <h1 class="mb-0 text-white fw-bold" style="font-size:1.6rem;">{{ $archiveTitle }}</h1>
                    <p class="mb-0 mt-1" style="color:#aaa;font-size:.85rem;">
                        {{ $news->total() }} {{ $news->total() === 1 ? 'noticia encontrada' : 'noticias encontradas' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <div class="row">
        {{-- Noticias --}}
        <div class="col-lg-8">
            @forelse($news as $article)
            @php
                $imageSrc = \App\Helpers\ImageHelper::getImageUrlOrNull($article->image, 'news');
                $hasImage = !empty($imageSrc);
            @endphp
            <div class="card border-0 shadow-sm mb-3 news-card">
                <div class="row g-0">
                    @if($hasImage)
                    <div class="col-md-4 news-image-container">
                        <img src="{{ $imageSrc }}"
                             class="img-fluid rounded-start h-100"
                             style="object-fit: cover;"
                             alt="{{ $article->title }}"
                             loading="lazy"
                             onerror="this.closest('.col-md-4').classList.add('d-none');">
                    </div>
                    @endif
                    <div class="col-md-{{ $hasImage ? '8' : '12' }}">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge" style="background:{{ $article->category?->color ?? 'var(--primary-color)' }};">{{ $article->category->name ?? 'General' }}</span>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>{{ ($article->published_at ?? $article->created_at)->locale('es')->isoFormat('D MMM, YYYY') }}
                                </small>
                            </div>
                            <h5 class="card-title fs-5 mb-1">
                                <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <span class="me-3"><i class="fas fa-user-edit me-1"></i>{{ $article->author ?? 'Staff' }}</span>
                                <span><i class="fas fa-eye me-1"></i>{{ number_format($article->views) }} lecturas</span>
                            </div>
                            <p class="card-text small mb-2">
                                {{ Str::limit($article->excerpt, 150) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-light text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3 d-block"></i>
                <p class="mb-0">No hay noticias publicadas en este período.</p>
            </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('vendor.pagination.bootstrap-5-small') }}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Navegar por meses del año --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Meses de {{ $year }}</h5>
                    </div>
                </div>
                <div class="card-body py-2 px-3">
                    <div class="row g-1">
                        @foreach(range(1, 12) as $m)
                        @php
                            $isActive = isset($month) && (int)$month === $m;
                            $monthName = \Carbon\Carbon::create($year, $m)->locale('es')->isoFormat('MMMM');
                        @endphp
                        <div class="col-6">
                            <a href="{{ route('news.archive', [$year, str_pad($m, 2, '0', STR_PAD_LEFT)]) }}"
                               class="btn btn-sm w-100 text-capitalize {{ $isActive ? 'btn-primary' : 'btn-outline-secondary' }} mb-1">
                                {{ $monthName }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('news.archive', $year) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                        Ver todo {{ $year }}
                    </a>
                </div>
            </div>

            {{-- Navegar por años --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Años</h5>
                    </div>
                </div>
                <div class="card-body py-2 px-3">
                    @php
                        $years = \App\Models\News::where('status', 'published')
                            ->selectRaw('YEAR(COALESCE(published_at, created_at)) as y, COUNT(*) as total')
                            ->groupBy('y')->orderByDesc('y')->get();
                    @endphp
                    @foreach($years as $row)
                    <a href="{{ route('news.archive', $row->y) }}"
                       class="d-flex justify-content-between align-items-center py-1 text-decoration-none {{ $row->y == $year ? 'fw-bold text-primary' : 'text-dark' }}">
                        <span>{{ $row->y }}</span>
                        <span class="badge bg-light text-secondary">{{ $row->total }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Lo más leído --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:4px;height:18px;background:var(--primary-color);border-radius:2px;"></div>
                        <h5 class="mb-0 fw-bold" style="font-size:.9rem;">Lo más leído</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @foreach($mostReadArticles ?? [] as $i => $art)
                    <a href="{{ route('news.show', $art->slug) }}" class="text-decoration-none">
                        <div class="d-flex p-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="fw-bold text-primary me-2" style="min-width:20px">{{ $i + 1 }}</div>
                            <div>
                                <p class="mb-0 small text-dark">{{ Str::limit($art->title, 70) }}</p>
                                <small class="text-muted"><i class="fas fa-eye me-1"></i>{{ number_format($art->views) }}</small>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.news-card { transition: transform 0.2s, box-shadow 0.2s; }
.news-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
</style>
@endpush
@endsection
