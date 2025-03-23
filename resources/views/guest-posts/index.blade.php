@extends('layouts.app')

@section('title', 'Colaboraciones - ConocIA')
@section('meta_description', 'Artículos y contenido escrito por nuestros colaboradores y expertos invitados en temas de tecnología e inteligencia artificial.')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-lg-8">
            <div class="mb-5">
                <h1 class="mb-3">Colaboraciones</h1>
                <p class="lead mb-4">Artículos y contenido escrito por nuestros colaboradores y expertos invitados en temas de tecnología e inteligencia artificial.</p>
                
                @auth
                    <div class="mb-4">
                        <a href="{{ route('guest-posts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Enviar mi colaboración
                        </a>
                    </div>
                @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> ¿Quieres compartir tu conocimiento? 
                        <a href="{{ route('login') }}" class="alert-link">Inicia sesión</a> para enviar tu colaboración.
                    </div>
                @endauth
            </div>

            @if($guestPosts->count() > 0)
                <div class="row">
                    @foreach($guestPosts as $post)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}" loading="lazy">
                            @else
                                <img src="{{ asset('images/default-post.jpg') }}" class="card-img-top" alt="{{ $post->title }}" loading="lazy">
                            @endif
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2">{{ $post->category->name }}</span>
                                    <small class="text-muted">{{ $post->created_at->format('d M, Y') }}</small>
                                </div>
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text">{{ $post->excerpt }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <a href="{{ route('guest-posts.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">Leer más</a>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-user-edit me-1"></i> {{ $post->user->name }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $guestPosts->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No hay colaboraciones disponibles por el momento. ¡Sé el primero en compartir tu conocimiento!
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Categorías</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($categories as $category)
                            <li class="mb-2">
                                <a href="{{ route('guest-posts.category', $category->slug) }}" class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-angle-right me-2"></i>{{ $category->name }}</span>
                                    <span class="badge bg-secondary rounded-pill">{{ $category->guest_posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>¿Por qué contribuir?</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Comparte tu conocimiento con nuestra comunidad</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Establécete como experto en tu campo</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Recibe feedback de otros profesionales</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Genera visibilidad para tus proyectos</li>
                    </ul>
                    <div class="mt-3">
                        @auth
                            <a href="{{ route('guest-posts.create') }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-edit me-2"></i>Enviar mi artículo
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar sesión para contribuir
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-pencil-alt me-2"></i>Guía para colaboradores</h5>
                </div>
                <div class="card-body">
                    <p>Antes de enviar tu colaboración, asegúrate de revisar nuestras pautas:</p>
                    <ul>
                        <li>El contenido debe ser original y no publicado anteriormente</li>
                        <li>Enfocado en temas de tecnología, IA o áreas relacionadas</li>
                        <li>Mínimo 800 palabras, máximo 2500</li>
                        <li>Incluye referencias y fuentes cuando sea necesario</li>
                    </ul>
                    <p class="mb-0 small">Todos los artículos son revisados por nuestro equipo editorial antes de ser publicados.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection