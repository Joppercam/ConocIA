<!-- resources/views/news/show.blade.php -->
@extends('layouts.app')

@section('title', $article->title . ' - ConocIA')

@php
    use Illuminate\Support\Facades\Storage;
    
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * @param string|null $imageName Nombre de la imagen
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL de la imagen
     */
  
    /**
     * Obtiene la URL de la imagen de la noticia o una imagen predeterminada
     * @param string|null $imageName Nombre de la imagen
     * @param string $size Tamaño deseado: 'small', 'medium', o 'large'
     * @return string URL de la imagen
     */
    function getNewsImage($imageName, $size = 'medium') {
        // Si la imagen existe en el almacenamiento, devuelve su URL
        if ($imageName && Storage::disk('public')->exists('images/news/' . $imageName)) {
            return Storage::url('images/news/' . $imageName);
        }
        
        // Si no, devuelve la imagen predeterminada según el tamaño
        return Storage::url('images/defaults/news-default-' . $size . '.jpg');
    }
@endphp

@section('content')
<!-- Breadcrumbs -->
<div class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Noticias</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($article->title, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Contenido Principal (Izquierda) -->
        <div class="col-lg-8">
            <!-- Categoría y Título -->
            <div class="mb-3">
            <span class="badge bg-primary mb-2">
                @if(is_object($article->category))
                    {{ $article->category->name }}
                @else
                    {{ $article->category }}
                @endif
            </span>
                <h1 class="mb-2">{{ $article->title }}</h1>
                
                <!-- Autor y fecha -->
                <div class="d-flex align-items-center text-muted small mb-3">
                    <img src="{{ $article->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($article->author) . '&background=random' }}" 
                        class="rounded-circle me-2" width="24" height="24" alt="{{ $article->author }}">
                    <span>Por <a href="{{ route('authors.show', $article->author) }}" class="text-decoration-none">{{ $article->author }}</a></span>
                    <span class="mx-2">•</span>
                    <span><i class="far fa-calendar-alt me-1"></i> {{ $article->created_at->format('d M, Y') }}</span>
                    <span class="mx-2">•</span>
                    <span><i class="far fa-clock me-1"></i> {{ $article->reading_time }} min de lectura</span>
                </div>

                <!-- Compartir en redes sociales -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="small text-muted">Compartir:</span>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('news.show', $article)) }}&text={{ urlencode($article->title) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('news.show', $article)) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('news.show', $article)) }}&title={{ urlencode($article->title) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle" target="_blank" rel="noopener">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="mailto:?subject={{ $article->title }}&body={{ route('news.show', $article) }}" 
                    class="btn btn-sm btn-outline-secondary rounded-circle">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>

            <!-- Imagen principal -->
            <div class="mb-4">
                <img src="{{ getImageUrl($article->image, 'news', 'large') }}" class="img-fluid rounded w-100" alt="{{ $article->title }}" onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-large.jpg') }}';">
                @if($article->image_caption)
                    <p class="text-muted small mt-1 fst-italic">{{ $article->image_caption }}</p>
                @endif
            </div>

            <!-- Resumen -->
            <div class="card border-0 bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Resumen</h5>
                    <p class="card-text">{{ $article->summary }}</p>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="news-content mb-4">
                {!! $article->content !!}
            </div>
           

           <!-- Comentarios -->
            <div class="mb-4">
                <h4 class="mb-3">Comentarios</h4>
                
                <!-- Formulario de comentario -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Deja tu comentario</h5>
                        <form action="{{ url('/comments') }}" method="POST">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\News">
                            <input type="hidden" name="commentable_id" value="{{ $article->id }}">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="name" name="guest_name" value="{{ old('guest_name') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="guest_email" value="{{ old('guest_email') }}" required>
                                <div class="form-text">Tu email no será publicado.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comentario</label>
                                <textarea class="form-control" id="comment" name="content" rows="4" required>{{ old('content') }}</textarea>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="saveInfo" name="save_info" {{ old('save_info') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="saveInfo">
                                    Guardar mi nombre y email para la próxima vez que comente.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Publicar comentario</button>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de comentarios -->
                <div class="comments-list">
                    @forelse($article->comments ?? [] as $comment)
                        <div class="comment-item mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex mb-2">
                                <div class="comment-avatar me-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 20px;">
                                        {{ strtoupper(substr($comment->guest_name ?? 'A', 0, 1)) }}
                                    </div>
                                </div>
                                <div class="comment-meta">
                                    <h5 class="mb-1">{{ $comment->guest_name ?? 'Anónimo' }}</h5>
                                    <div class="text-muted small">
                                        <i class="far fa-clock me-1"></i> 
                                        {{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Hace algún tiempo' }}
                                    </div>
                                </div>
                            </div>
                            <div class="comment-content">
                                <p>{{ $comment->content ?? 'Sin contenido' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light">
                            <p class="mb-0">No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                        </div>
                    @endforelse
                </div>
            </div>



        </div>
        
        <!-- Sidebar (Derecha) -->
        <div class="col-lg-4">
            <!-- Artículos Relacionados -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Artículos Relacionados</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($relatedArticles as $relatedArticle)
                            <div class="col-md-6">
                                <div class="related-article d-flex">
                                    <div class="related-article-img me-3">
                                        <a href="{{ route('news.show', $relatedArticle->slug) }}">
                                            <img src="{{ getImageUrl($relatedArticle->image, 'news', 'small') }}" 
                                                class="img-fluid rounded" 
                                                alt="{{ $relatedArticle->title }}" 
                                                style="width: 100px; height: 70px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-small.jpg') }}';">
                                        </a>
                                    </div>
                                    <div class="related-article-content">
                                        <h6 class="mb-1">
                                            <a href="{{ route('news.show', $relatedArticle->slug) }}" class="text-decoration-none">
                                                {{ Str::limit($relatedArticle->title, 60) }}
                                            </a>
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $relatedArticle->created_at->format('d M, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No hay artículos relacionados disponibles.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Lo más leído -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line text-primary me-2"></i> Lo más leído
                    </h5>
                </div>
                <div class="card-body">
                    <div class="most-read-list">
                        @forelse($mostReadArticles as $index => $mostReadArticle)
                            <div class="most-read-item d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="most-read-number fs-4 me-3 text-primary fw-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="most-read-content">
                                    <a href="{{ route('news.show', $mostReadArticle->slug) }}" class="text-decoration-none">
                                        <h6 class="mb-1">{{ $mostReadArticle->title }}</h6>
                                    </a>
                                    <div class="d-flex align-items-center small text-muted">
                                        <span class="me-2">
                                            <i class="fas fa-folder-open me-1"></i>
                                            {{ $mostReadArticle->category->name }}
                                        </span>
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            {{ number_format($mostReadArticle->views) }} lecturas
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No hay artículos disponibles en este momento.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Newsletter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Suscríbete</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Recibe las últimas noticias y análisis sobre IA en tu correo.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm mb-1">
                            <input type="email" class="form-control form-control-sm" 
                                name="email" id="newsletter-email" 
                                placeholder="Tu correo electrónico" required>
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <p class="form-text text-muted small">
                            No compartimos tu información. Puedes darte de baja en cualquier momento.
                        </p>
                    </form>
                </div>
            </div>
            
           <!-- Tags populares -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Temas populares</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTags as $tag)
                            <a href="{{ route('news.by.tag', $tag->slug) }}" class="badge bg-primary text-decoration-none">
                                {{ $tag->name }} <span class="badge bg-light text-dark">{{ $tag->news_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección: También te puede interesar -->
<section class="py-4 bg-light">
    <div class="container">
        <h3 class="mb-4">También te puede interesar</h3>
        
        <div class="row g-4">
            
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Estilos para el contenido principal */
    .news-content {
        font-size: 1.05rem;
        line-height: 1.7;
    }
    
    .news-content p {
        margin-bottom: 1.5rem;
    }
    
    .news-content h2, .news-content h3, .news-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .news-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.375rem;
    }
    
    .news-content blockquote {
        border-left: 4px solid #0d6efd;
        padding-left: 1rem;
        margin-left: 0;
        color: #6c757d;
        font-style: italic;
    }
    
    .news-content pre, .news-content code {
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        padding: 0.2rem 0.4rem;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.875em;
    }
    
    .news-content pre {
        padding: 1rem;
        margin-bottom: 1.5rem;
        overflow-x: auto;
    }
    
    .news-content ul, .news-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .news-content a {
        color: #0d6efd;
        text-decoration: none;
    }
    
    .news-content a:hover {
        text-decoration: underline;
    }
    
    /* Estilos para el sidebar */
    .col-lg-4 .card-header h5 {
        font-size: 0.9rem;
    }
    
    .col-lg-4 .card-body {
        font-size: 0.85rem;
    }
    
    .col-lg-4 .card-body h6 {
        font-size: 0.85rem;
        line-height: 1.3;
    }
    
    .col-lg-4 .card-body .small {
        font-size: 0.75rem;
    }
    
    .related-article-content h6 {
        font-size: 0.8rem !important;
        line-height: 1.3;
    }
    
    .most-read-item .most-read-number {
        font-size: 1.1rem !important;
    }
    
    .most-read-content h6 {
        font-size: 0.8rem !important;
        margin-bottom: 0.2rem !important;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    .form-text {
        font-size: 0.7rem !important;
    }
</style>
@endpush