@extends('admin.layouts.app')

@section('title', 'Artículos Recomendados para TikTok')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Artículos Recomendados para TikTok</h1>
        <a href="{{ route('admin.tiktok.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Artículos con Mayor Potencial para TikTok</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="recommendationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Visitas</th>
                            <th>Comentarios</th>
                            <th>Puntuación TikTok</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recommendedArticles as $article)
                        <tr>
                            <td>{{ Str::limit($article->title, 60) }}</td>
                            <td>{{ $article->category->name ?? 'N/A' }}</td>
                            <td>{{ $article->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($article->views_count ?? 0) }}</td>
                            <td>{{ number_format($article->comments()->count() ?? 0) }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar {{ $article->tiktok_score >= 70 ? 'bg-success' : ($article->tiktok_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                         role="progressbar" style="width: {{ $article->tiktok_score }}%" 
                                         aria-valuenow="{{ $article->tiktok_score }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $article->tiktok_score }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.tiktok.generate', $article->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-magic"></i> Generar Guión
                                </a>
                                <a href="{{ route('news.show', $article->slug) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Artículo
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay artículos recomendados disponibles.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Criterios de Puntuación</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Actualidad</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">30%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Popularidad</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">25%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-eye fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Engagement</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">20%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Viralidad</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">25%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#recommendationsTable').DataTable({
            order: [[5, 'desc']], // Ordenar por puntuación TikTok
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    });
</script>
@endsection