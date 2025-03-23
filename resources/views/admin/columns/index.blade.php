<!-- resources/views/admin/columns/index.blade.php -->
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Columnas de Opinión</h1>
        <a href="{{ route('admin.columns.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Columna
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Categoría</th>
                            <th>Destacado</th>
                            <th>Vistas</th>
                            <th>Fecha</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($columns as $column)
                        <tr>
                            <td>{{ $column->id }}</td>
                            <td>
                                <a href="{{ route('admin.columns.edit', $column) }}">
                                    {{ Str::limit($column->title, 40) }}
                                </a>
                            </td>
                            <td>{{ $column->author->name ?? 'N/A' }}</td>
                            <td>{{ $column->category->name ?? 'Sin categoría' }}</td>
                            <td class="text-center">
                                @if($column->featured)
                                <span class="badge bg-warning">Destacado</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $column->views }}</td>
                            <td>{{ $column->published_at ? $column->published_at->format('d/m/Y') : 'No publicado' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.columns.edit', $column) }}" class="btn btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('columns.show', $column->slug) }}" class="btn btn-info" title="Ver" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.columns.destroy', $column) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta columna?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay columnas disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $columns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection