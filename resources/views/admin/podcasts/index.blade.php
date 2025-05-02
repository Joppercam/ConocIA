@extends('admin.layouts.app')

@section('title', 'Administrar Podcasts')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Administrar Podcasts</h1>
        <div>
            <a href="{{ route('admin.podcasts.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Crear Podcast
            </a>
            <form action="{{ route('admin.podcasts.generate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-info shadow-sm">
                    <i class="fas fa-sync fa-sm text-white-50"></i> Generar Podcasts
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('command_output'))
        <div class="card mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Salida del proceso de generación</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap;">{{ session('command_output') }}</pre>
            </div>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Todos los Podcasts</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Noticia</th>
                            <th>Fecha de publicación</th>
                            <th>Duración</th>
                            <th>Reproducciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($podcasts as $podcast)
                            <tr>
                                <td>{{ $podcast->id }}</td>
                                <td>{{ $podcast->title }}</td>
                                <td>
                                    <a href="{{ route('admin.news.edit', $podcast->news_id) }}" target="_blank">
                                        Ver noticia
                                    </a>
                                </td>
                                <td>{{ $podcast->published_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $podcast->formatted_duration }}</td>
                                <td>{{ $podcast->play_count }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.podcasts.show', $podcast) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.podcasts.destroy', $podcast) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este podcast?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay podcasts disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $podcasts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection