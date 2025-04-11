@extends('admin.layouts.app')

@section('title', 'Dashboard TikTok')

@section('content')
<div class="container-fluid">
    <!-- Cabecera con botones de acción -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard TikTok</h1>
        <a href="{{ route('admin.tiktok.recommendations') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-lightbulb fa-sm text-white-50 mr-1"></i> Ver recomendaciones
        </a>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <!-- Guiones pendientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendientes de revisión</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_review'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guiones aprobados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aprobados listos para producción</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Videos publicados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Videos publicados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitas generadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tráfico generado</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clicks'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guiones pendientes de revisión -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Guiones pendientes de revisión</h6>
        </div>
        <div class="card-body">
            @if($pendingScripts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Noticia</th>
                                <th>Creado</th>
                                <th>Puntuación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingScripts as $script)
                                <tr>
                                    <td>
                                        <a href="{{ route('news.show', $script->news->slug) }}" target="_blank">
                                            {{ Str::limit($script->news->title, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ $script->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $script->tiktok_score >= 70 ? 'bg-success' : ($script->tiktok_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                role="progressbar" style="width: {{ $script->tiktok_score }}%" 
                                                aria-valuenow="{{ $script->tiktok_score }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $script->tiktok_score }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.tiktok.edit', $script->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Revisar
                                        </a>
                                        
                                        <form method="POST" action="{{ route('admin.tiktok.update-status', $script->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.tiktok.update-status', $script->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $pendingScripts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No hay guiones pendientes de revisión. 
                    <a href="{{ route('admin.tiktok.recommendations') }}" class="alert-link">Ver recomendaciones</a> para generar nuevos guiones.
                </div>
            @endif
        </div>
    </div>

    <!-- Guiones aprobados listos para producción -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Guiones aprobados listos para producción</h6>
        </div>
        <div class="card-body">
            @if($approvedScripts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Noticia</th>
                                <th>Aprobado</th>
                                <th>Puntuación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedScripts as $script)
                                <tr>
                                    <td>
                                        <a href="{{ route('news.show', $script->news->slug) }}" target="_blank">
                                            {{ Str::limit($script->news->title, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ $script->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $script->tiktok_score >= 70 ? 'bg-success' : ($script->tiktok_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                role="progressbar" style="width: {{ $script->tiktok_score }}%" 
                                                aria-valuenow="{{ $script->tiktok_score }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $script->tiktok_score }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.tiktok.edit', $script->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Ver/Editar
                                        </a>
                                        
                                        <form method="POST" action="{{ route('admin.tiktok.update-status', $script->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="published">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-check-double"></i> Marcar como publicado
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $approvedScripts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No hay guiones aprobados pendientes de producción.
                </div>
            @endif
        </div>
    </div>

    <!-- Recomendaciones principales -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Noticias recomendadas para TikTok</h6>
            <a href="{{ route('admin.tiktok.recommendations') }}" class="btn btn-sm btn-primary">
                Ver todas las recomendaciones
            </a>
        </div>
        <div class="card-body">
            @if(isset($recommendedArticles) && $recommendedArticles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Noticia</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                                <th>Puntuación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendedArticles as $news)
                                <tr>
                                    <td>
                                        <a href="{{ route('news.show', $news->slug) }}" target="_blank">
                                            {{ Str::limit($news->title, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ $news->category->name ?? 'Sin categoría' }}</td>
                                    <td>{{ $news->published_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar {{ $news->tiktok_score >= 70 ? 'bg-success' : ($news->tiktok_score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                role="progressbar" style="width: {{ $news->tiktok_score }}%" 
                                                aria-valuenow="{{ $news->tiktok_score }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $news->tiktok_score }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.tiktok.create', $news->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-pen"></i> Crear manual
                                        </a>
                                        <a href="{{ route('admin.tiktok.generate', $news->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-robot"></i> Generar automático
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    No hay noticias recomendadas en este momento.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script adicional para el dashboard de TikTok si es necesario
</script>
@endpush