@extends('layouts.app')

@section('title', 'Dashboard ConocIA')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Dashboard ConocIA</h1>
            <p class="text-muted mb-0">Panel comercial de inteligencia para tu plan <span class="badge bg-primary">{{ $user->planLabel() }}</span></p>
        </div>
        <a href="{{ route('billing.plans') }}" class="btn btn-outline-primary">Actualizar plan</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Insights recientes</h2>
                    @forelse($recentInsights as $insight)
                        @php($locked = $insight->is_premium && !$user->canAccessFeature('insights'))
                        <div class="border rounded p-3 mb-3 position-relative">
                            <div class="{{ $locked ? 'opacity-50' : '' }}">
                                <div class="d-flex gap-2 mb-2">
                                    <span class="badge bg-info">{{ ucfirst($insight->tipo) }}</span>
                                    <span class="badge bg-light text-dark border">{{ $insight->relevancia }}/100</span>
                                    @if($insight->is_premium)<span class="badge bg-warning text-dark">PRO</span>@endif
                                </div>
                                <a href="{{ $insight->noticia ? route('news.show', $insight->noticia) : '#' }}" class="fw-semibold text-decoration-none">
                                    {{ $insight->noticia->title ?? 'Noticia no disponible' }}
                                </a>
                                <p class="text-muted mb-0 mt-2">{{ $locked ? 'Insight premium bloqueado para plan FREE.' : $insight->insight_accionable }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aún no hay insights disponibles.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Noticias importantes</h2>
                    @foreach($importantNews as $news)
                        <div class="mb-3">
                            <a href="{{ route('news.show', $news) }}" class="fw-semibold text-decoration-none">{{ Str::limit($news->title, 72) }}</a>
                            <div class="small text-muted">{{ number_format($news->views) }} vistas</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Alertas</h2>
                    @forelse($alerts as $alert)
                        <div class="badge bg-light text-dark border me-1 mb-2">{{ $alert->keyword }}</div>
                    @empty
                        <p class="text-muted mb-0">Las alertas personalizadas se activan desde PRO.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
