@extends('admin.layouts.app')
@section('title', 'Noticias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Detalles de la Noticia</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Noticias</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ver Noticia</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-primary">
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
                    <h1 class="h4 mb-3">{{ $news->title }}</h1>
                    
                    @if($news->featured_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $news->featured_image) }}" alt="{{ $news->title }}" class="img-fluid rounded">
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <h5>Resumen</h5>
                        <div class="bg-light p-3 rounded">
                            {{ $news->summary }}
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Contenido</h5>
                        <div class="content-preview">
                            {!! $news->content !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comentarios si los hay -->
            @if(isset($news->comments) && $news->comments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Comentarios ({{ $news->comments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($news->comments as $comment)
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
                                @if($news->status == 'published')
                                    <span class="badge bg-success">Publicado</span>
                                @else
                                    <span class="badge bg-secondary">Borrador</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Categoría:</span>
                            <span>{{ $news->category->name ?? 'Sin categoría' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Autor:</span>
                            <span>{{ $news->author->name ?? 'Sin autor' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Creado:</span>
                            <span>{{ $news->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Publicado:</span>
                            <span>{{ $news->published_at ? $news->published_at->format('d/m/Y H:i') : 'No publicado' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Vistas:</span>
                            <span>{{ $news->views_count ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Etiquetas -->
            @if(isset($news->tags) && $news->tags->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">Etiquetas</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($news->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Acciones -->
            <div class="card">
                <div class="card-header">Acciones</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <a href="{{ route('news.show', $news->slug) }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-eye me-2"></i> Ver en sitio
                        </a>
                        <form action="{{ route('admin.news.destroy', $news) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de eliminar esta noticia? Esta acción no se puede deshacer.')">
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