@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cola de Publicaciones en Redes Sociales</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="fas fa-plus"></i> Nueva Publicación
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Publicaciones Pendientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Publicaciones Pendientes</h6>
        </div>
        <div class="card-body">
            @if($pendingItems->isEmpty())
                <p class="text-center">No hay publicaciones pendientes.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
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
                            @foreach($pendingItems as $item)
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
                                    <td>{{ Str::limit($item->content, 100) }}</td>
                                    <td>
                                        @if($item->news)
                                            <a href="{{ route('news.show', $item->news->slug) }}" target="_blank">
                                                {{ Str::limit($item->news->title, 50) }}
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
                                        
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#markPublishedModal{{ $item->id }}">
                                            <i class="fas fa-check"></i> Marcar Publicado
                                        </button>
                                        
                                        <form action="{{ route('admin.social-media.queue.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta publicación?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Modal para marcar como publicado -->
                                        <div class="modal fade" id="markPublishedModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="markPublishedModalLabel{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="markPublishedModalLabel{{ $item->id }}">Marcar como Publicado</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('admin.social-media.queue.mark-published', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="post_id">ID de la Publicación (opcional)</label>
                                                                <input type="text" class="form-control" id="post_id" name="post_id" placeholder="ID de la publicación en la red social">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="post_url">URL de la Publicación (opcional)</label>
                                                                <input type="url" class="form-control" id="post_url" name="post_url" placeholder="URL de la publicación en la red social">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">Marcar como Publicado</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $pendingItems->links() }}
            @endif
        </div>
    </div>

    <!-- Pestañas para publicaciones recientes y fallidas -->
    <ul class="nav nav-tabs" id="socialMediaTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="published-tab" data-toggle="tab" href="#published" role="tab" aria-controls="published" aria-selected="true">Publicaciones Recientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="failed-tab" data-toggle="tab" href="#failed" role="tab" aria-controls="failed" aria-selected="false">Publicaciones Fallidas</a>
        </li>
    </ul>
    
    <div class="tab-content" id="socialMediaTabsContent">
        <!-- Publicaciones Recientes -->
        <div class="tab-pane fade show active" id="published" role="tabpanel" aria-labelledby="published-tab">
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if($publishedItems->isEmpty())
                        <p class="text-center">No hay publicaciones recientes.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Red Social</th>
                                        <th>Contenido</th>
                                        <th>Noticia</th>
                                        <th>Fecha de Publicación</th>
                                        <th>Enlace</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($publishedItems as $item)
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
                                            <td>{{ Str::limit($item->content, 100) }}</td>
                                            <td>
                                                @if($item->news)
                                                    <a href="{{ route('news.show', $item->news->slug) }}" target="_blank">
                                                        {{ Str::limit($item->news->title, 50) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->published_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if($item->post_url)
                                                    <a href="{{ $item->post_url }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-external-link-alt"></i> Ver
                                                    </a>
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Publicaciones Fallidas -->
        <div class="tab-pane fade" id="failed" role="tabpanel" aria-labelledby="failed-tab">
            <div class="card shadow mb-4">
                <div class="card-body">
                    @if($failedItems->isEmpty())
                        <p class="text-center">No hay publicaciones fallidas.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Red Social</th>
                                        <th>Contenido</th>
                                        <th>Noticia</th>
                                        <th>Fecha</th>
                                        <th>Error</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($failedItems as $item)
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
                                            <td>{{ Str::limit($item->content, 100) }}</td>
                                            <td>
                                                @if($item->news)
                                                    <a href="{{ route('news.show', $item->news->slug) }}" target="_blank">
                                                        {{ Str::limit($item->news->title, 50) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No disponible</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="text-danger">{{ Str::limit($item->error_message, 100) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ $item->manual_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Reintentar
                                                </a>
                                                
                                                <form action="{{ route('admin.social-media.queue.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta publicación?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>




<!-- Modal para crear nueva publicación -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPostModalLabel">Crear Nueva Publicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.social-media.queue.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="network" class="form-label">Red Social</label>
                        <select class="form-select" id="network" name="network" required>
                            <option value="twitter">Twitter</option>
                            <option value="facebook">Facebook</option>
                            <option value="linkedin">LinkedIn</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required 
                                  placeholder="Escribe el contenido de tu publicación"></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/280 caracteres (límite para Twitter)
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="news_id" class="form-label">Noticia relacionada (opcional)</label>
                        <select class="form-select" id="news_id" name="news_id">
                            <option value="">Selecciona una noticia...</option>
                            @foreach($recentNews as $news)
                                <option value="{{ $news->id }}">{{ Str::limit($news->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>




    </div>
</div>


@endsection


@section('styles')
<style>
    /* Estilos para reducir el tamaño de la letra */
    .container-fluid {
        font-size: 0.9rem;
    }

    .table {
        font-size: 0.85rem;
    }

    .card-header h6 {
        font-size: 0.95rem;
    }

    .modal-body {
        font-size: 0.9rem;
    }

    .form-text, .form-label {
        font-size: 0.85rem;
    }

    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .alert {
        font-size: 0.85rem;
    }

    /* Para reducir el espacio entre filas de tabla */
    .table td, .table th {
        padding: 0.5rem;
    }

    /* Hacer el contenido más compacto */
    .card-body {
        padding: 1rem;
    }
</style>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contentTextarea = document.getElementById('content');
        const charCountSpan = document.getElementById('charCount');
        
        contentTextarea.addEventListener('input', function() {
            const charCount = this.value.length;
            charCountSpan.textContent = charCount;
            
            if (charCount > 280) {
                charCountSpan.classList.add('text-danger');
            } else {
                charCountSpan.classList.remove('text-danger');
            }
        });
    });
</script>
@endpush