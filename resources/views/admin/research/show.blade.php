@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Detalles de la Investigación</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.research.index') }}">Investigaciones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ver Investigación</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.research.edit', $research) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Detalles principales -->
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="h4 mb-3">{{ $research->title }}</h1>
                    
                    @if($research->featured_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $research->featured_image) }}" alt="{{ $research->title }}" class="img-fluid rounded">
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h5>Resumen</h5>
                        <div class="bg-light p-3 rounded">
                            {{ $research->summary }}
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Contenido</h5>
                        <div class="content-preview">
                            {!! $research->content !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comentarios si los hay -->
            @if($research->comments && $research->comments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Comentarios ({{ $research->comments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($research->comments as $comment)
                            <div class="comment mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $comment->user->name ?? $comment->guest_name ?? 'Usuario anónimo' }}</strong>
                                        <span class="text-muted ms-2">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este comentario?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    {{ $comment->content }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <!-- Metadatos -->
            <div class="card mb-4">
                <div class="card-header">Información</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Estado:</span>
                            <span>
                                @if($research->status == 'published')
                                    <span class="badge bg-success">Publicado</span>
                                @elseif($research->status == 'draft')
                                    <span class="badge bg-secondary">Borrador</span>
                                @elseif($research->status == 'pending')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($research->status == 'rejected')
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tipo:</span>
                            <span class="badge bg-info">{{ ucfirst($research->research_type) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Categoría:</span>
                            <span>{{ $research->category->name ?? 'Sin categoría' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Autor:</span>
                            <span>{{ $research->author->name ?? 'Sin autor' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Creado:</span>
                            <span>{{ $research->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Publicado:</span>
                            <span>{{ $research->published_at ? $research->published_at->format('d/m/Y H:i') : 'No publicado' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Vistas:</span>
                            <span>{{ $research->views_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Descargas:</span>
                            <span>{{ $research->downloads_count ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Etiquetas -->
            <div class="card mb-4">
                <div class="card-header">Etiquetas</div>
                <div class="card-body">
                    @if($research->tags && $research->tags->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($research->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay etiquetas asignadas.</p>
                    @endif
                </div>
            </div>
            
            <!-- Archivo PDF -->
            @if($research->pdf_file)
                <div class="card mb-4">
                    <div class="card-header">Documento PDF</div>
                    <div class="card-body">
                        <a href="{{ asset('storage/' . $research->pdf_file) }}" target="_blank" class="btn btn-primary w-100">
                            <i class="fas fa-file-pdf me-2"></i> Ver documento
                        </a>
                    </div>
                </div>
            @endif
            
            <!-- Acciones -->
            <div class="card">
                <div class="card-header">Acciones</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.research.edit', $research) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <a href="{{ route('research.show', $research->slug) }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-eye me-2"></i> Ver en sitio
                        </a>
                        <form action="{{ route('admin.research.destroy', $research) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de eliminar esta investigación? Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash me-2"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .content-preview img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush