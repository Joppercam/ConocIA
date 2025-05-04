@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>Gestión de Categorías</h1>
        
        <div class="mb-4">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                Crear Nueva Categoría
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Color</th>
                            <th>Estado</th>
                            <th>Noticias</th>
                            <th>Investigaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>
                                    @if($category->color)
                                        <div style="width: 25px; height: 25px; border-radius: 50%; background-color: {{ $category->color }}; border: 1px solid #ccc;"></div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>{{ $category->news_count }}</td>
                                <td>{{ $category->researches_count }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info">
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    @if($category->is_active)
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de desactivar esta categoría?')">
                                                Desactivar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.categories.restore', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Estás seguro de activar esta categoría?')">
                                                Activar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay categorías registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection