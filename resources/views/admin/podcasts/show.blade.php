@extends('admin.layouts.app')

@section('title', 'Detalles del Podcast')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Podcast</h1>
        <a href="{{ route('admin.podcasts.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Podcast</h6>
                </div>
                <div class="card-body">
                    <h4>{{ $podcast->title }}</h4>
                    <p class="text-muted">
                        <strong>ID:</strong> {{ $podcast->id }}<br>
                        <strong>Fecha de publicación:</strong> {{ $podcast->published_at->format('d/m/Y H:i') }}<br>
                        <strong>Duración:</strong> {{ $podcast->formatted_duration }}<br>
                        <strong>Reproducciones:</strong> {{ $podcast->play_count }}
                    </p>
                    
                    <div class="my-4">
                        <h5>Reproductor de audio</h5>
                        <audio controls class="w-100">
                            <source src="{{ asset('storage/' . $podcast->audio_path) }}" type="audio/mpeg">
                            Su navegador no soporta la reproducción de audio.
                        </audio>
                    </div>
                    
                    <div class="my-4">
                        <h5>Información del Resumen</h5>
                        <div class="border p-3 rounded">
                            <h6>{{ $podcast->title }}</h6>
                            <p class="mb-1">
                                <small class="text-muted">Resumen diario publicado: {{ $podcast->published_at->format('d/m/Y') }}</small>
                            </p>
                            <hr>
                            <div class="alert alert-info">
                                Este podcast es un resumen diario que contiene las noticias más importantes del día anterior.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones</h6>
                </div>
                <div class="card-body">
                    <a href="{{ asset('storage/' . $podcast->audio_path) }}" class="btn btn-success btn-block" download="{{ Str::slug($podcast->title) }}.mp3">
                        <i class="fas fa-download"></i> Descargar archivo de audio
                    </a>
                    
                    <a href="{{ route('podcasts.show', $podcast->id) }}" class="btn btn-info btn-block mt-2" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Ver en sitio público
                    </a>
                    
                    <hr>
                    
                    <form action="{{ route('admin.podcasts.destroy', $podcast) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('¿Está seguro de eliminar este podcast? Esta acción no se puede deshacer.')">
                            <i class="fas fa-trash"></i> Eliminar podcast
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Metadatos</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ruta de archivo
                            <span class="text-muted small">{{ $podcast->audio_path }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tamaño de archivo
                            <span class="badge badge-primary badge-pill">
                                @php
                                    try {
                                        $size = Storage::size('public/' . $podcast->audio_path);
                                        echo round($size / 1024 / 1024, 2) . ' MB';
                                    } catch (\Exception $e) {
                                        echo 'N/A';
                                    }
                                @endphp
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Creado
                            <span>{{ $podcast->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Última modificación
                            <span>{{ $podcast->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection