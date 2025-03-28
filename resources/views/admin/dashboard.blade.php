@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="{{ route('admin.news.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Noticia
        </a>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Noticias</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_news'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Noticias Publicadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published_news'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Categorías</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['categories'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Usuarios</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila de tablas -->
    <div class="row">
        <!-- Noticias Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Noticias Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentNews as $news)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $news->id) }}">
                                            {{ Str::limit($news->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $news->category->name ?? 'Sin categoría' }}</td>
                                    <td>
                                        @if($news->status == 'published')
                                            <span class="badge bg-success">Publicada</span>
                                        @elseif($news->status == 'draft')
                                            <span class="badge bg-warning">Borrador</span>
                                        @else
                                            <span class="badge bg-secondary">Archivada</span>
                                        @endif
                                    </td>
                                    <td>{{ $news->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay noticias recientes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Noticias más populares -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Noticias Populares</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Vistas</th>
                                    <th>Publicada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularNews as $news)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $news->id) }}">
                                            {{ Str::limit($news->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $news->category->name ?? 'Sin categoría' }}</td>
                                    <td>{{ number_format($news->views) }}</td>
                                    <td>{{ $news->published_at ? $news->published_at->format('d/m/Y') : 'No publicada' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay noticias populares</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <!-- Fila para Redes Sociales -->
        <div class="row">
            <!-- Cola de Publicación en Redes Sociales -->
            <div class="col-12 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Publicaciones Pendientes en Redes Sociales</h6>
                        <a href="{{ route('admin.social-media.queue') }}" class="btn btn-sm btn-primary">
                            Ver Todas
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($pendingSocialPosts) && $pendingSocialPosts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Red Social</th>
                                            <th>Contenido</th>
                                            <th>Noticia</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingSocialPosts as $item)
                                            <tr>
                                                <td>
                                                    @if($item->network == 'twitter')
                                                        <i class="fab fa-twitter text-info"></i> Twitter
                                                    @elseif($item->network == 'facebook')
                                                        <i class="fab fa-facebook text-primary"></i> Facebook
                                                    @elseif($item->network == 'linkedin')
                                                        <i class="fab fa-linkedin text-primary"></i> LinkedIn
                                                    @else
                                                        {{ ucfirst($item->network) }}
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($item->content, 60) }}</td>
                                                <td>
                                                    @if($item->news)
                                                        <a href="{{ route('news.show', $item->news->slug) }}" target="_blank">
                                                            {{ Str::limit($item->news->title, 30) }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No disponible</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ $item->manual_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-external-link-alt"></i> Publicar
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center">No hay publicaciones pendientes en la cola</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        

    </div>
</div>
@endsection