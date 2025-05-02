<!-- resources/views/admin/spotify/dashboard.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Integración con Spotify')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Integración con Spotify</h1>
        
        @if(!session()->has('spotify_access_token'))
            <a href="{{ route('admin.spotify.authorize') }}" class="btn btn-success">
                <i class="fab fa-spotify"></i> Conectar con Spotify
            </a>
        @else
            <span class="badge badge-success">
                <i class="fas fa-check-circle"></i> Conectado a Spotify
            </span>
        @endif
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session()->has('spotify_access_token'))
        <!-- Podcasts en Spotify -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Podcasts en Spotify</h6>
            </div>
            <div class="card-body">
                @if(count($spotifyShows) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Episodios</th>
                                    <th>Enlace</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spotifyShows->items as $show)
                                    <tr>
                                        <td>{{ $show->name }}</td>
                                        <td>{{ \Str::limit($show->description, 100) }}</td>
                                        <td>{{ $show->total_episodes }}</td>
                                        <td>
                                            <a href="{{ $show->external_urls->spotify }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fab fa-spotify"></i> Ver en Spotify
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No se encontraron podcasts en Spotify. Considera crear un podcast primero usando Spotify for Podcasters.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Podcasts pendientes de subir a Spotify -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Podcasts pendientes de subir a Spotify</h6>
            </div>
            <div class="card-body">
                @if(count($pendingPodcasts) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Duración</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPodcasts as $podcast)
                                    <tr>
                                        <td>{{ $podcast->title }}</td>
                                        <td>{{ $podcast->published_at->format('d/m/Y') }}</td>
                                        <td>{{ $podcast->formatted_duration }}</td>
                                        <td>
                                            <form action="{{ route('admin.spotify.upload', $podcast) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fab fa-spotify"></i> Subir a Spotify
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No hay podcasts pendientes de subir a Spotify.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <strong>Conecta tu cuenta de Spotify</strong>
            <p>Para subir podcasts a Spotify, necesitas conectar tu cuenta primero. Haz clic en el botón "Conectar con Spotify" arriba.</p>
        </div>
    @endif
</div>
@endsection