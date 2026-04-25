@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    $metaRobots = request()->integer('page', 1) > 1 ? 'noindex, follow' : 'index, follow';
@endphp

@section('title', 'IA en Chile — Ecosistema, investigación y startups | ConocIA')

@section('meta')
    @include('partials.seo-meta', [
        'metaTitle' => 'IA en Chile — Ecosistema, investigación y startups | ConocIA',
        'metaDescription' => 'Cobertura editorial sobre inteligencia artificial en Chile: universidades, investigación, startups, regulación, astronomía, salud y ecosistema local.',
        'metaKeywords' => 'IA en Chile, inteligencia artificial Chile, startups IA Chile, investigación IA Chile, universidades Chile IA',
        'metaUrl' => route('chile.index'),
        'metaRobots' => $metaRobots,
    ])
    @include('partials.schema-breadcrumb', ['crumbs' => [
        ['name' => 'Inicio', 'url' => url('/')],
        ['name' => 'IA en Chile'],
    ]])
@endsection

@section('content')
<section style="background:linear-gradient(135deg,#0f172a 0%,#111827 45%,#3b0d11 100%);border-bottom:1px solid rgba(255,255,255,.08);" class="py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge px-3 py-2 mb-3 d-inline-block" style="background:rgba(248,113,113,.16);color:#fecaca;font-size:.78rem;letter-spacing:.06em;">ECOSISTEMA LOCAL</span>
                <h1 class="fw-bold text-white mb-3" style="font-size:2.25rem;line-height:1.15;">IA en <span style="color:#f87171;">Chile</span></h1>
                <p style="color:#cbd5e1;font-size:1rem;max-width:640px;line-height:1.75;margin:0;">
                    Universidades, startups, papers, salud, astronomía y políticas públicas. Una sección dedicada a seguir cómo se está construyendo la inteligencia artificial desde Chile.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="text-center rounded-4 p-3" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);">
                            <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $news->total() }}</div>
                            <div style="color:#94a3b8;font-size:.76rem;">notas</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center rounded-4 p-3" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);">
                            <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $latestResearch->count() }}</div>
                            <div style="color:#94a3b8;font-size:.76rem;">investigaciones</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center rounded-4 p-3" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);">
                            <div class="text-white fw-bold" style="font-size:1.5rem;">{{ $chileStartups->count() }}</div>
                            <div style="color:#94a3b8;font-size:.76rem;">startups</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    @if($featuredArticle)
    <div class="mb-5">
        <p class="profundiza-section-label" style="color:#991b1b;">Destacado en Chile</p>
        <a href="{{ route('news.show', $featuredArticle->slug) }}" class="text-decoration-none d-block">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-left:4px solid #ef4444;">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge" style="background:#fee2e2;color:#b91c1c;">{{ $featuredArticle->category->name ?? 'IA en Chile' }}</span>
                        <span class="badge" style="background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;">{{ $featuredArticle->published_at?->locale('es')->isoFormat('D MMM YYYY') }}</span>
                    </div>
                    <h2 class="fw-bold mb-3" style="color:#0f172a;font-size:1.55rem;line-height:1.25;">{{ $featuredArticle->title }}</h2>
                    <p class="mb-0" style="color:#475569;font-size:.98rem;line-height:1.75;max-width:860px;">
                        {{ Str::limit($featuredArticle->summary ?? $featuredArticle->excerpt ?? '', 240) }}
                    </p>
                </div>
            </div>
        </a>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <p class="profundiza-section-label" style="color:#991b1b;">Cobertura reciente</p>
            <div class="d-flex flex-column gap-3">
                @foreach($news as $article)
                    @php
                        $imageSrc = \App\Helpers\ImageHelper::getImageUrlOrNull($article->image, 'news');
                    @endphp
                    <article class="card border-0 shadow-sm overflow-hidden">
                        <div class="row g-0">
                            @if($imageSrc)
                            <div class="col-md-4" style="min-height:190px;">
                                <a href="{{ route('news.show', $article->slug) }}" class="d-block h-100">
                                    <img src="{{ $imageSrc }}" alt="{{ $article->title }}" class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                                </a>
                            </div>
                            @endif
                            <div class="col-md-{{ $imageSrc ? '8' : '12' }}">
                                <div class="card-body p-4">
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                        <span class="badge" style="background:#fee2e2;color:#b91c1c;">{{ $article->category->name ?? 'IA en Chile' }}</span>
                                        <span class="badge" style="background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;">{{ $article->published_at?->locale('es')->isoFormat('D MMM YYYY') }}</span>
                                        <span class="badge" style="background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;"><i class="fas fa-eye me-1"></i>{{ number_format($article->views) }}</span>
                                    </div>
                                    <h2 class="fw-bold mb-3" style="font-size:1.12rem;line-height:1.35;">
                                        <a href="{{ route('news.show', $article->slug) }}" class="text-decoration-none text-dark">{{ $article->title }}</a>
                                    </h2>
                                    <p class="mb-3" style="color:#475569;font-size:.9rem;line-height:1.7;">
                                        {{ Str::limit($article->summary ?? $article->excerpt ?? '', 180) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span style="color:#94a3b8;font-size:.8rem;"><i class="fas fa-user-edit me-1"></i>{{ $article->author ?? 'Staff' }}</span>
                                        <a href="{{ route('news.show', $article->slug) }}" class="btn btn-sm rounded-pill px-3" style="background:#fee2e2;color:#b91c1c;">Leer más <i class="fas fa-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('vendor.pagination.bootstrap-5-small') }}
            </div>
        </div>

        <div class="col-lg-4">
            @if($latestResearch->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="mb-0 fw-bold" style="font-size:.98rem;color:#0f172a;">Investigación local</h3>
                </div>
                <div class="card-body p-0">
                    @foreach($latestResearch as $research)
                    <a href="{{ route('research.show', $research->slug ?? $research->id) }}" class="text-decoration-none d-block px-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="fw-semibold text-dark mb-1" style="font-size:.88rem;line-height:1.35;">{{ Str::limit($research->title, 85) }}</div>
                        <div style="color:#64748b;font-size:.78rem;">{{ Str::limit($research->institution ?? $research->summary ?? '', 85) }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($chileStartups->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="mb-0 fw-bold" style="font-size:.98rem;color:#0f172a;">Startups en radar</h3>
                </div>
                <div class="card-body p-0">
                    @foreach($chileStartups as $startup)
                    <a href="{{ route('startups.show', $startup->slug) }}" class="text-decoration-none d-block px-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="fw-semibold text-dark mb-1" style="font-size:.88rem;">{{ $startup->name }}</div>
                        <div style="color:#64748b;font-size:.78rem;">{{ Str::limit($startup->tagline ?? $startup->sector_label ?? '', 85) }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($upcomingEvents->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="mb-0 fw-bold" style="font-size:.98rem;color:#0f172a;">Agenda cercana</h3>
                </div>
                <div class="card-body p-0">
                    @foreach($upcomingEvents as $event)
                    <a href="{{ route('agenda.index') }}" class="text-decoration-none d-block px-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="fw-semibold text-dark mb-1" style="font-size:.88rem;">{{ $event->title }}</div>
                        <div style="color:#64748b;font-size:.78rem;">{{ $event->location }} · {{ $event->start_date?->locale('es')->isoFormat('D MMM YYYY') }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="mb-0 fw-bold" style="font-size:.98rem;color:#0f172a;">Explorar ConocIA</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('research.index') }}" class="btn btn-outline-secondary btn-sm">Investigación</a>
                        <a href="{{ route('startups.index') }}" class="btn btn-outline-secondary btn-sm">Startups IA</a>
                        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary btn-sm">Agenda IA</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
