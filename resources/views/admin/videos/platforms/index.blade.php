@extends('admin.layouts.app')

@section('title', 'Plataformas de Videos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Plataformas de Videos</h1>
        
        <a href="{{ route('admin.videos.platforms.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus-circle"></i> Nueva Plataforma
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Plataformas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Código</th>
                            <th>Patrón de Embed</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($platforms as $platform)
                        <tr>
                            <td>{{ $platform->name }}</td>
                            <td><code>{{ $platform->code }}</code></td>
                            <td><code>{{ $platform->embed_pattern }}</code></td>
                            <td class="text-center">
                                @if($platform->is_active)
                                <span class="badge badge-success">Activa</span>
                                @else
                                <span class="badge badge-danger">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.videos.platforms.edit', $platform->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="if(confirm('¿Estás seguro de eliminar esta plataforma?')) document.getElementById('delete-form-{{ $platform->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $platform->id }}" action="{{ route('admin.videos.platforms.destroy', $platform->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay plataformas disponibles.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection