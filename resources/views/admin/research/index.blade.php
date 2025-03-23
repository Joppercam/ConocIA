@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Investigaciones</h1>
        <a href="{{ route('admin.research.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nueva Investigación
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form action="{{ route('admin.research.index') }}" method="GET" class="row">
                <div class="col-md-4 mb-2">
                    <label for="search" class="form-label small">Buscar</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" value="{{ request('search') }}" placeholder="Título, contenido...">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="category" class="form-label small">Categoría</label>
                    <select class="form-control form-control-sm" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="status" class="form-label small">Estado</label>
                    <select class="form-control form-control-sm" id="status" name="status">
                        <option value="">Todos los estados</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de investigaciones -->
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.research.bulk-actions') }}" method="POST" id="bulk-form">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr class="small">
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="60">ID</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Autor</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($researches as $research)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input research-checkbox" type="checkbox" name="ids[]" value="{{ $research->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $research->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.research.edit', $research) }}" class="text-decoration-none">
                                            {{ Str::limit($research->title, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($research->research_type) }}</span>
                                    </td>
                                    <td>{{ $research->category->name ?? 'Sin categoría' }}</td>
                                    <td>{{ $research->author->name ?? 'Sin autor' }}</td>
                                    <td>
                                        @if($research->status == 'published')
                                            <span class="badge bg-success">Publicado</span>
                                        @elseif($research->status == 'draft')
                                            <span class="badge bg-secondary">Borrador</span>
                                        @elseif($research->status == 'pending')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif($research->status == 'rejected')
                                            <span class="badge bg-danger">Rechazado</span>
                                        @endif
                                    </td>
                                    <td>{{ $research->published_at ? $research->published_at->format('d/m/Y') : 'No publicado' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.research.edit', $research) }}" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.research.show', $research) }}" class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $research->id }}" 
                                                data-title="{{ $research->title }}"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay investigaciones disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($researches->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="bulk-actions">
                            <div class="input-group input-group-sm" style="width: 280px;">
                                <select name="action" class="form-select form-select-sm" id="bulk-action">
                                    <option value="">Acciones en lote</option>
                                    <option value="publish">Publicar seleccionados</option>
                                    <option value="draft">Mover a borrador</option>
                                    <option value="delete">Eliminar seleccionados</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary btn-sm" id="apply-bulk-action">Aplicar</button>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.research.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-export"></i> Exportar
                            </a>
                        </div>
                    </div>
                @endif
            </form>

            <div class="mt-3">
                {{ $researches->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title fs-6" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                ¿Estás seguro de que deseas eliminar la investigación "<span id="delete-title"></span>"?
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <form id="delete-form" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
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
        const checkboxes = document.querySelectorAll('.research-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
    
    // Modal de eliminación
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('delete-title').textContent = title;
            document.getElementById('delete-form').action = `/admin/research/${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Validar acciones en lote
    document.getElementById('bulk-form').addEventListener('submit', function(e) {
        const action = document.getElementById('bulk-action').value;
        const checkedBoxes = document.querySelectorAll('.research-checkbox:checked');
        
        if (action === '' || checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Por favor, selecciona una acción y al menos una investigación.');
        }
    });
</script>
@endpush