<!-- resources/views/columns/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-lg-8">
            <!-- Información del autor -->
            <div class="d-flex align-items-center mb-4">
                <img src="{{ asset($column->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}" 
                     class="rounded-circle me-3" width="60" height="60" 
                     alt="{{ $column->author->name }}"
                     onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                <div>
                    <h5 class="mb-0">{{ $column->author->name }}</h5>
                    <p class="text-muted mb-0">{{ $column->published_at->format('d F, Y') }} • {{ $column->reading_time }} min de lectura</p>
                </div>
            </div>
            
            <!-- Título y categoría -->
            <h1 class="mb-3">{{ $column->title }}</h1>
            @if($column->category)
            <div class="mb-4">
                <span class="badge bg-primary">{{ $column->category->name }}</span>
            </div>
            @endif
            
            <!-- Contenido -->
            <div class="content-wrapper mb-5">
                @if($column->excerpt)
                <div class="lead mb-4">
                    {{ $column->excerpt }}
                </div>
                @endif
                
                <div class="content article-content">
                    {!! $column->content !!}
                </div>
            </div>
            
            <!-- Compartir -->
            <div class="mb-5">
                <h5>Compartir</h5>
                <div class="d-flex gap-2">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('columns.show', $column->slug)) }}&text={{ urlencode($column->title) }}" 
                       class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('columns.show', $column->slug)) }}" 
                       class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('columns.show', $column->slug)) }}&title={{ urlencode($column->title) }}" 
                       class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                    <a href="mailto:?subject={{ $column->title }}&body={{ route('columns.show', $column->slug) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                </div>
            </div>
            
            <!-- Sobre el autor -->
            <div class="card mb-5">
                <div class="card-body">
                    <h5>Sobre el autor</h5>
                    <div class="d-flex">
                        <img src="{{ asset($column->author->avatar ?? 'storage/images/defaults/user-profile.jpg') }}" 
                             class="rounded-circle me-3" width="80" height="80" 
                             alt="{{ $column->author->name }}"
                             onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                        <div>
                            <h6>{{ $column->author->name }}</h6>
                            <p class="text-muted mb-2">{{ $column->author->bio ?? 'Columnista de ConocIA' }}</p>
                            <!-- Aquí podrías añadir links a redes sociales del autor -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sección de comentarios -->
            @include('components.comments', [
                'comments' => $column->comments ?? [],
                'commentableType' => 'App\\Models\\Column',
                'commentableId' => $column->id
            ])

        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Otras columnas del autor -->
            @if($authorColumns->count() > 0)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Más de {{ $column->author->name }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach($authorColumns as $authorColumn)
                        <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <a href="{{ route('columns.show', $authorColumn->slug) }}" class="text-decoration-none text-dark">
                                <h6 class="mb-1">{{ $authorColumn->title }}</h6>
                            </a>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $authorColumn->published_at->format('d/m/Y') }}</small>
                                <small class="text-muted">{{ $authorColumn->reading_time }} min</small>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Columnas relacionadas -->
            @if($relatedColumns->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Columnas relacionadas</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach($relatedColumns as $relatedColumn)
                        <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('columns.show', $relatedColumn->slug) }}" class="text-decoration-none text-dark">
                                            {{ $relatedColumn->title }}
                                        </a>
                                    </h6>
                                    <div class="d-flex align-items-center text-muted small">
                                        <span>{{ $relatedColumn->author->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $relatedColumn->published_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos para la barra lateral (sidebar) */
    .col-lg-4 {
        font-size: 0.8rem;
    }
    
    .col-lg-4 h5 {
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .col-lg-4 h6 {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .col-lg-4 .text-muted {
        font-size: 0.75rem;
    }
    
    /* Estilos para el contenido del artículo - Formato profesional con letra reducida */
    .article-content {
        font-size: 0.85rem;
        line-height: 1.5;
        color: #333;
        font-family: 'Arial', sans-serif;
        text-align: justify;
    }
    
    .article-content p {
        margin-bottom: 0.8rem;
    }
    
    .article-content h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.8rem;
        color: #222;
    }
    
    .article-content h3 {
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 1.2rem;
        margin-bottom: 0.6rem;
        color: #333;
    }
    
    .article-content ul, .article-content ol {
        margin-bottom: 0.8rem;
        padding-left: 1.5rem;
    }
    
    .article-content li {
        margin-bottom: 0.3rem;
    }
    
    .article-content blockquote {
        border-left: 2px solid #ccc;
        padding: 0.3rem 0 0.3rem 0.8rem;
        margin: 0.8rem 0;
        font-style: italic;
        color: #666;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        margin: 0.8rem 0;
    }
    
    .article-content a {
        color: #444;
        text-decoration: underline;
    }
    
    .article-content table {
        width: 100%;
        margin: 0.8rem 0;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    
    .article-content table th,
    .article-content table td {
        padding: 0.4rem;
        border: 1px solid #ddd;
    }
    
    .article-content table th {
        background-color: #f5f5f5;
    }
    
    /* Ajuste para la sección lead */
    .lead {
        font-size: 0.9rem;
        font-weight: normal;
        color: #555;
    }
    
    /* Estilos para comentarios */
    .comments-section .form-floating > .form-control {
        height: calc(3.5rem + 2px);
        line-height: 1.25;
    }
</style>
@endpush