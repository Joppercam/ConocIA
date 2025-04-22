@extends('admin.layouts.app')

@section('title', 'Administrar Videos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Administrar Videos</h1>
        
        <div class="btn-group">
            <a href="{{ route('admin.videos.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus-circle"></i> Agregar Video
            </a>
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#importUrlModal">
                <i class="fas fa-link"></i> Importar desde URL
            </button>
            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#bulkImportModal">
                <i class="fas fa-cloud-download-alt"></i> Importación Masiva
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.videos.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="platform">Plataforma</label>
                        <select name="platform" id="platform" class="form-control">
                            <option value="">Todas las plataformas</option>
                            @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}" {{ request('platform') == $platform->id ? 'selected' : '' }}>
                                {{ $platform->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="category">Categoría</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Título, descripción o ID" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Videos</h6>
            <span>Total: {{ $videos->total() }} videos</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Miniatura</th>
                            <th>Título</th>
                            <th>Plataforma</th>
                            <th>Categorías</th>
                            <th>Publicado</th>
                            <th>Vistas</th>
                            <th>Destacado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                        <tr>
                            <td width="120">
                                <a href="{{ route('videos.show', $video->id) }}" target="_blank">
                                    <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="img-thumbnail" style="max-width: 100px;">
                                </a>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ Str::limit($video->title, 50) }}</div>
                                <div class="small text-muted">ID: {{ $video->external_id }}</div>
                            </td>
                            <td>{{ $video->platform->name }}</td>
                            <td>
                                @foreach($video->categories as $category)
                                <span class="badge bg-info">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $video->published_at->format('d/m/Y') }}</td>
                            <td>{{ number_format($video->view_count) }}</td>
                            <td class="text-center">
                                @if($video->is_featured)
                                <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                @else
                                <span class="badge bg-secondary"><i class="fas fa-times"></i></span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="if(confirm('¿Estás seguro de eliminar este video?')) document.getElementById('delete-form-{{ $video->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $video->id }}" action="{{ route('admin.videos.destroy', $video->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay videos disponibles.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $videos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Importar desde URL -->
<div class="modal fade" id="importUrlModal" tabindex="-1" role="dialog" aria-labelledby="importUrlModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.videos.import-url') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importUrlModalLabel">Importar Video desde URL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="video_url">URL del Video</label>
                        <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." required>
                        <small class="form-text text-muted">
                            Introduce la URL completa de YouTube, Vimeo o Dailymotion.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Importación Masiva -->
<div class="modal fade" id="bulkImportModal" tabindex="-1" role="dialog" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.videos.bulk-import') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkImportModalLabel">Importación Masiva de Videos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="platform_id">Plataforma</label>
                        <select name="platform_id" id="platform_id" class="form-control" required>
                            <option value="">Selecciona una plataforma</option>
                            @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keywords">Palabras clave para búsqueda</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" placeholder="noticias, política, economía" required>
                        <small class="form-text text-muted">
                            Introduce palabras clave separadas por comas para buscar videos.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Categoría (opcional)</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Sin categoría</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="limit">Cantidad de videos a importar</label>
                        <input type="number" class="form-control" id="limit" name="limit" min="1" max="50" value="10">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection