@extends('admin.layouts.app')

@section('title', 'Recomendaciones TikTok')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Recomendaciones TikTok</h1>
        <a href="{{ route('admin.tiktok.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    {{-- ── Sección prioritaria: tienen podcast listo ───────────────────────── --}}
    @if($withPodcast->isNotEmpty())
    <div class="card shadow-sm mb-4 border-start border-success border-4">
        <div class="card-header bg-success bg-opacity-10 py-3 d-flex align-items-center gap-2">
            <i class="fas fa-microphone text-success"></i>
            <h6 class="m-0 fw-bold text-success">Tienen podcast listo — audio reutilizable, kit gratuito</h6>
            <span class="badge bg-success ms-auto">{{ $withPodcast->count() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Artículo</th>
                            <th>Categoría</th>
                            <th>Publicado</th>
                            <th>Duración podcast</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withPodcast as $article)
                        <tr>
                            <td class="align-middle">
                                <div class="fw-semibold" style="max-width:360px;">
                                    {{ Str::limit($article->title, 70) }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-secondary">{{ $article->category->name ?? 'Sin categoría' }}</span>
                            </td>
                            <td class="align-middle text-muted small">
                                {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                            </td>
                            <td class="align-middle">
                                <span class="text-success fw-semibold">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $article->podcastEpisode->duration_formatted ?? 'Listo' }}
                                </span>
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.tiktok.generate', $article->id) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-magic me-1"></i> Generar guión
                                </a>
                                <a href="{{ route('news.show', $article->slug) }}"
                                   class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Recomendaciones estándar ─────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Otras noticias recientes con potencial</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="recommendationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Artículo</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Visitas</th>
                            <th>Puntuación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recommendedArticles as $article)
                        <tr>
                            <td class="align-middle" style="max-width:360px;">
                                {{ Str::limit($article->title, 70) }}
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-secondary">{{ $article->category->name ?? 'Sin categoría' }}</span>
                            </td>
                            <td class="align-middle text-muted small">
                                {{ $article->created_at->format('d/m/Y') }}
                            </td>
                            <td class="align-middle">{{ number_format($article->views ?? 0) }}</td>
                            <td class="align-middle" style="min-width:120px;">
                                <div class="progress" style="height:18px;">
                                    <div class="progress-bar {{ $article->tiktok_score >= 70 ? 'bg-success' : ($article->tiktok_score >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                         role="progressbar"
                                         style="width:{{ $article->tiktok_score }}%">
                                        {{ $article->tiktok_score }}
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.tiktok.generate', $article->id) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-magic me-1"></i> Generar guión
                                </a>
                                <a href="{{ route('news.show', $article->slug) }}"
                                   class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay más artículos recomendados en este momento.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
