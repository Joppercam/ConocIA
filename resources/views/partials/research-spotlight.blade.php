@php
    $homeResearch = collect($featuredResearch ?? [])->concat(collect($researchArticles ?? []))
        ->filter(fn($item) => !empty($item))
        ->unique('id')
        ->values();
    $heroResearch = $homeResearch->first();
    $sideResearch = $homeResearch->slice(1, 2);
    $heroResearchPublishedAt = $heroResearch ? ($heroResearch->published_at ?? $heroResearch->created_at) : null;
    $isNewHeroResearch = $heroResearchPublishedAt && $heroResearchPublishedAt->diffInHours(now()) < 48;
@endphp

@if($heroResearch)
<div style="background:linear-gradient(135deg,#0f172a 0%,#0b2545 60%,#112a46 100%);border-top:1px solid rgba(56,182,255,.25);border-bottom:3px solid #38b6ff;">
    <div class="container py-4">
        <div class="row align-items-center g-4">

            <div class="col-auto d-none d-lg-flex flex-column align-items-center" style="min-width:90px;">
                <span class="fw-bold text-uppercase rounded-pill px-3 py-2 mb-2 text-center"
                      style="background:#38b6ff;color:#fff;font-size:.65rem;letter-spacing:.06em;line-height:1.3;white-space:nowrap;">
                    <i class="fas fa-flask d-block mb-1" style="font-size:.9rem;"></i>
                    Investigación<br>destacada
                </span>
                <span style="color:#94a3b8;font-size:.65rem;text-align:center;">
                    {{ ($heroResearch->published_at ?? $heroResearch->created_at)->locale('es')->isoFormat('D MMM') }}<br>
                    ConocIA
                </span>
            </div>

            <div class="col-auto d-none d-lg-block px-0">
                <div style="width:1px;height:76px;background:rgba(148,163,184,.25);"></div>
            </div>

            <div class="col-auto">
                <a href="{{ route('research.show', $heroResearch->slug ?? $heroResearch->id) }}" class="text-decoration-none">
                    <div class="rounded-3 overflow-hidden shadow-sm" style="width:96px;height:96px;border:1px solid rgba(148,163,184,.2);background:#0b1220;">
                        <img src="{{ $getImageUrl($heroResearch->image ?? null, 'research', 'small') }}"
                             alt="{{ $heroResearch->title }}"
                             class="w-100 h-100"
                             style="object-fit:cover;"
                             loading="lazy"
                             onerror="this.src='{{ asset('images/defaults/research-default-small.jpg') }}'">
                    </div>
                </a>
            </div>

            <div class="col">
                <div class="d-lg-none mb-2">
                    <span class="badge rounded-pill px-2 py-1" style="background:#38b6ff;color:#fff;font-size:.62rem;">
                        <i class="fas fa-flask me-1"></i>Investigación destacada
                    </span>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    @if($heroResearch->category)
                    <span class="badge rounded-pill" style="{{ $getCategoryStyle($heroResearch->category) }} font-size:.68rem;">
                        <i class="fas {{ $getCategoryIcon($heroResearch->category) }} me-1"></i>{{ $heroResearch->category->name }}
                    </span>
                    @endif
                    @if($isNewHeroResearch)
                    <span class="badge rounded-pill" style="background:#38b6ff;color:#fff;font-size:.68rem;">
                        Nuevo
                    </span>
                    @endif
                    <span class="badge rounded-pill" style="background:rgba(255,255,255,.08);color:#cbd5e1;font-size:.68rem;">
                        <i class="far fa-eye me-1"></i>{{ number_format($heroResearch->views ?? 0) }} vistas
                    </span>
                </div>

                <a href="{{ route('research.show', $heroResearch->slug ?? $heroResearch->id) }}" class="text-decoration-none">
                    <h3 class="fw-bold mb-2" style="font-size:1.15rem;color:#f8fafc;line-height:1.3;">
                        {{ $heroResearch->title }}
                    </h3>
                </a>

                <p class="mb-2" style="color:#cbd5e1;font-size:.88rem;line-height:1.6;max-width:720px;">
                    {{ Str::limit($heroResearch->excerpt ?? $heroResearch->abstract ?? $heroResearch->summary ?? '', 185) }}
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3 mt-1">
                    <span style="color:#94a3b8;font-size:.78rem;">
                        <i class="fas fa-user-edit me-1" style="color:#38b6ff;"></i>{{ $heroResearch->author ?? 'Staff ConocIA' }}
                    </span>
                    @foreach($sideResearch as $item)
                    <a href="{{ route('research.show', $item->slug ?? $item->id) }}"
                       class="text-decoration-none d-none d-md-inline-flex align-items-center"
                       style="color:#cbd5e1;font-size:.76rem;">
                        <i class="fas fa-angle-right me-2" style="color:#38b6ff;"></i>{{ Str::limit($item->title, 42) }}
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="col-auto">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('research.show', $heroResearch->slug ?? $heroResearch->id) }}"
                       class="btn btn-sm rounded-pill fw-semibold px-3 px-sm-4"
                       style="background:#38b6ff;color:#fff;font-size:.8rem;white-space:nowrap;">
                        Leer investigación <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('research.index') }}"
                       class="btn btn-sm rounded-pill fw-semibold px-3 px-sm-4"
                       style="background:rgba(255,255,255,.08);color:#e2e8f0;border:1px solid rgba(255,255,255,.12);font-size:.78rem;white-space:nowrap;">
                        Ver todas
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endif
