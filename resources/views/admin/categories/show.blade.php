@extends('admin.layouts.app')

@section('content')
<div class="container">
<h1>
Detalles de Categoría: {{ $category->name }}
@if(!$category->is_active)
<span class="badge bg-danger">Inactiva</span>
@endif
</h1>

    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            Volver al listado
        </a>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
            Editar
        </a>
        @if($category->is_active)
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de desactivar esta categoría?')">
                    Desactivar
                </button>
            </form>
        @else
            <form action="{{ route('admin.categories.restore', $category) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success" onclick="return confirm('¿Estás seguro de activar esta categoría?')">
                    Activar
                </button>
            </form>
        @endif
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Información</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9">{{ $category->name }}</dd>
                        
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9">{{ $category->slug }}</dd>
                        
                        <dt class="col-sm-3">Color:</dt>
                        <dd class="col-sm-9">
                            @if($category->color)
                                <div style="width: 25px; height: 25px; border-radius: 50%; background-color: {{ $category->color }}; border: 1px solid #ccc; display: inline-block; vertical-align: middle;"></div>
                                <span class="ms-2">{{ $category->color }}</span>
                            @else
                                -
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">{{ $category->description ?? 'Sin descripción' }}</dd>

                        <dt class="col-sm-3">Términos de búsqueda:</dt>
                        <dd class="col-sm-9">{{ $category->search_terms ?? 'Sin términos definidos' }}</dd>

                        <dt class="col-sm-3">Estado:</dt>
                        <dd class="col-sm-9">
                            @if($category->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Creado:</dt>
                        <dd class="col-sm-9">{{ $category->created_at->format('d/m/Y H:i') }}</dd>
                        
                        <dt class="col-sm-3">Actualizado:</dt>
                        <dd class="col-sm-9">{{ $category->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Estadísticas</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Noticias:</dt>
                        <dd class="col-sm-6">{{ $category->news_count }}</dd>
                        
                        <dt class="col-sm-6">Investigaciones:</dt>
                        <dd class="col-sm-6">{{ $category->researches_count }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Últimas Noticias</h5>
                    <a href="{{ route('admin.news.index', ['category' => $category->id]) }}" class="btn btn-sm btn-primary">Ver todas</a>
                </div>
                <div class="card-body">
                    @if($news->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($news as $item)
                                <li class="list-group-item">
                                    <a href="{{ route('admin.news.show', $item) }}">{{ $item->title }}</a>
                                    <br>
                                    <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No hay noticias en esta categoría.</p>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Últimas Investigaciones</h5>
                    <a href="{{ route('admin.research.index', ['category' => $category->id]) }}" class="btn btn-sm btn-primary">Ver todas</a>
                </div>
                <div class="card-body">
                    @if($researches->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($researches as $item)
                                <li class="list-group-item">
                                    <a href="{{ route('admin.research.show', $item) }}">{{ $item->title }}</a>
                                    <br>
                                    <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No hay investigaciones en esta categoría.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

