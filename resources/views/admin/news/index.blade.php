@extends('admin.layouts.app')
@section('title', 'Noticias')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Noticias</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Noticia
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.news.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Título, contenido...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Todos los estados</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de noticias -->
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.news.bulk-actions') }}" method="POST" id="bulk-form">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="60">ID</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Autor</th>
                                <th width="80">Destacado</th>  <!-- Nueva columna -->
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $article)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input article-checkbox" type="checkbox" name="ids[]" value="{{ $article->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $article->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.news.edit', $article) }}">
                                            {{ Str::limit($article->title, 35) }}
                                        </a>
                                    </td>
                                    <td>{{ $article->category->name ?? 'Sin categoría' }}</td>
                                    <td>{{ $article->author->name ?? 'Sin autor' }}</td>
                                    <td class="text-center">  <!-- Nueva celda -->
                                        @if($article->featured)
                                            <span class="badge bg-warning">Destacado</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->status == 'published')
                                            <span class="badge bg-success">Publicado</span>
                                        @else
                                            <span class="badge bg-secondary">Borrador</span>
                                        @endif
                                    </td>
                                    <td>{{ $article->published_at ? $article->published_at->format('d/m/Y') : 'No publicado' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.news.show', $article) }}" class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $article->id }}" 
                                                data-title="{{ $article->title }}"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div> 
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay noticias disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($news->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="bulk-actions">
                            <div class="input-group" style="width: 300px;">
                                <select name="action" class="form-control" id="bulk-action">
                                    <option value="">Acciones en lote</option>
                                    <option value="publish">Publicar seleccionados</option>
                                    <option value="draft">Mover a borrador</option>
                                    <option value="feature">Destacar seleccionados</option>  <!-- Nueva opción -->
                                    <option value="unfeature">Quitar destacado</option>      <!-- Nueva opción -->
                                    <option value="delete">Eliminar seleccionados</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary" id="apply-bulk-action">Aplicar</button>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.news.export') }}" class="btn btn-success">
                                <i class="fas fa-file-export"></i> Exportar
                            </a>
                        </div>
                    </div>
                @endif
            </form>

            <div class="mt-4">
                {{ $news->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar la noticia "<span id="delete-title"></span>"?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="delete-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Seleccionar/deseleccionar todos
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.article-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
    
    // Modal de eliminación
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('delete-title').textContent = title;
            document.getElementById('delete-form').action = "{{ route('admin.news.destroy', '') }}/" + id;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Validar acciones en lote
    document.getElementById('bulk-form').addEventListener('submit', function(e) {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.article-checkbox:checked');
        
        if (action === '' || checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Por favor, selecciona una acción y al menos una noticia.');
        }
    });
</script>
@endpush
@push('styles')
<style>
    /* Reducir tamaño de letra en la tabla */
    .table {
        font-size: 0.85rem;
    }
    
    /* Ajustar espaciado en las celdas */
    .table td, .table th {
        padding: 0.5rem;
    }
    
    /* Estilo para badge de destacado */
    .badge-featured {
        background-color: #FFC107;
        color: #212529;
    }
</style>
@endpush