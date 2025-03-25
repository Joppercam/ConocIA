@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('research.index') }}">Investigaciones</a></li>
                    @php
                        // Acceso seguro a la propiedad type, funciona tanto con array como con objeto
                        $type = is_array($research) ? ($research['type'] ?? null) : ($research->type ?? null);
                        $title = is_array($research) ? ($research['title'] ?? 'Investigación') : ($research->title ?? 'Investigación');
                        $created_at = is_array($research) ? (isset($research['created_at']) ? \Carbon\Carbon::parse($research['created_at']) : now()) : ($research->created_at ?? now());
                        $views = is_array($research) ? ($research['views'] ?? 0) : ($research->views ?? 0);
                        $author = is_array($research) ? ($research['author'] ?? null) : ($research->author ?? null);
                        $image = is_array($research) ? ($research['image'] ?? null) : ($research->image ?? null);
                        $excerpt = is_array($research) ? ($research['excerpt'] ?? null) : ($research->excerpt ?? null);
                        $content = is_array($research) ? ($research['content'] ?? '') : ($research->content ?? '');
                        $citations = is_array($research) ? ($research['citations'] ?? null) : ($research->citations ?? null);
                        $id = is_array($research) ? ($research['id'] ?? 0) : ($research->id ?? 0);
                        $comments = is_array($research) ? ($research['comments'] ?? []) : ($research->comments ?? []);
                        $comments_count = is_array($research) ? ($research['comments_count'] ?? 0) : ($research->comments_count ?? 0);
                    @endphp
                    
                    @if($type)
                    <li class="breadcrumb-item"><a href="{{ route('research.type', $type) }}">{{ $type }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>

            <!-- Título y metadatos -->
            <h1 class="mb-3">{{ $title }}</h1>
            <div class="article-meta d-flex flex-wrap align-items-center mb-4 text-muted small">
                <div class="me-4 mb-2">
                    <i class="far fa-calendar-alt me-1"></i> {{ $created_at->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                </div>
                @if($type)
                <div class="me-4 mb-2">
                    <i class="fas fa-tag me-1"></i> 
                    <a href="{{ route('research.category', $research->category->slug) }}" class="text-decoration-none">{{ $research->category->name }}</a>
                </div>
                @endif
                <div class="me-4 mb-2">
                    <i class="far fa-eye me-1"></i> {{ number_format($views) }} lecturas
                </div>
                @if($author)
                <div class="mb-2">
                    <i class="far fa-user me-1"></i> {{ $author }}
                </div>
                @endif
            </div>

            <!-- Imagen destacada -->
            @if($image)
            <div class="article-featured-image mb-4">
                <img src="{{ asset('storage/' . $image) }}" alt="{{ $title }}" class="img-fluid rounded shadow-sm" onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/research-default-large.jpg') }}';">
            </div>
            @endif

            <!-- Resumen -->
            @if($excerpt)
            <div class="lead mb-4 p-3 bg-light rounded">
                {{ $excerpt }}
            </div>
            @endif

            <!-- Contenido del artículo -->
            <div class="article-content mb-5">
                {!! $content !!}
            </div>

            <!-- Citaciones -->
            @if($citations)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-quote-left text-primary me-2"></i>Citaciones</h5>
                </div>
                <div class="card-body">
                    {!! $citations !!}
                </div>
            </div>
            @endif

            <!-- Compartir -->
            <div class="article-share mb-5">
                <h5 class="mb-3"><i class="fas fa-share-alt me-2"></i>Compartir esta investigación</h5>
                <div class="d-flex flex-wrap">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                        <i class="fab fa-facebook-f me-1"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($title) }}" target="_blank" class="btn btn-outline-info me-2 mb-2">
                        <i class="fab fa-twitter me-1"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($title) }}" target="_blank" class="btn btn-outline-secondary me-2 mb-2">
                        <i class="fab fa-linkedin-in me-1"></i> LinkedIn
                    </a>
                    <a href="mailto:?subject={{ $title }}&body={{ urlencode(url()->current()) }}" class="btn btn-outline-dark mb-2">
                        <i class="fas fa-envelope me-1"></i> Email
                    </a>
                </div>
            </div>

            
            
            
            
            <!-- Comentarios -->
            <div class="comments-section mb-4">
                <!-- Mensaje de éxito para comentarios (aparecerá solo cuando session('comment_added') está presente) -->
                @if(session('comment_added'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        Tu comentario ha sido recibido y está pendiente de aprobación. Gracias por participar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        <i class="far fa-comments text-primary me-2"></i>
                        Comentarios
                        @php
                            // Contar solo comentarios aprobados
                            $approvedCount = is_array($comments) 
                                ? collect($comments)->where('status', 'approved')->count() 
                                : $comments->where('status', 'approved')->count();
                        @endphp
                        @if($approvedCount > 0)
                            <span class="badge bg-primary ms-2">{{ $approvedCount }}</span>
                        @endif
                    </h4>
                    
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#commentForm" aria-expanded="false" aria-controls="commentForm">
                        <i class="fas fa-plus me-1"></i> Añadir comentario
                    </button>
                </div>
                
                <!-- Formulario de comentario (colapsable) -->
                <div class="collapse mb-4" id="commentForm">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3 border-bottom pb-2">Deja tu comentario</h5>
                            <form action="{{ url('/comments') }}" method="POST" id="researchCommentForm">
                                @csrf
                                <input type="hidden" name="commentable_type" value="App\Models\Research">
                                <input type="hidden" name="commentable_id" value="{{ $id }}">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                                id="name" name="guest_name" placeholder="Tu nombre"
                                                value="{{ old('guest_name') ?? Cookie::get('comment_name') }}" required>
                                            <label for="name">Nombre</label>
                                            @error('guest_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('guest_email') is-invalid @enderror" 
                                                id="email" name="guest_email" placeholder="tu@email.com"
                                                value="{{ old('guest_email') ?? Cookie::get('comment_email') }}" required>
                                            <label for="email">Email</label>
                                            @error('guest_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Tu email no será publicado.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                id="comment" name="content" style="height: 120px" 
                                                placeholder="Escribe tu comentario aquí" required>{{ old('content') }}</textarea>
                                        <label for="comment">Comentario</label>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="saveInfo" name="save_info" 
                                            {{ old('save_info') || Cookie::has('comment_name') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="saveInfo">
                                            Guardar mi información para próximos comentarios
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Publicar comentario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de comentarios filtrados (solo aprobados) -->
                <div class="comments-list">
                    @php
                        // Filtrar solo los comentarios aprobados
                        $approvedComments = is_array($comments) 
                            ? collect($comments)->where('status', 'approved') 
                            : $comments->where('status', 'approved');
                    @endphp

                    @forelse($approvedComments as $comment)
                        <div class="comment-item card border-0 shadow-sm mb-3" id="comment-{{ is_array($comment) ? ($comment['id'] ?? '') : ($comment->id ?? '') }}">
                            <div class="card-body">
                                <div class="d-flex mb-2">
                                    <div class="comment-avatar me-3">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                            style="width: 48px; height: 48px; font-size: 18px;">
                                            @php
                                                $commentName = is_array($comment) ? ($comment['guest_name'] ?? 'A') : ($comment->guest_name ?? 'A');
                                                $commentContent = is_array($comment) ? ($comment['content'] ?? 'Sin contenido') : ($comment->content ?? 'Sin contenido');
                                                $commentCreatedAt = is_array($comment) ? (isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at']) : null) : ($comment->created_at ?? null);
                                            @endphp
                                            {{ strtoupper(substr($commentName, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="comment-meta flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0 fs-5">{{ $commentName }}</h5>
                                            <span class="text-muted small">
                                                <i class="far fa-clock me-1"></i> 
                                                {{ $commentCreatedAt ? $commentCreatedAt->locale('es')->diffForHumans() : 'Hace algún tiempo' }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-comment-dots me-1"></i> Comentario #{{ $loop->iteration }}
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-content mt-2 pt-2 border-top">
                                    <p class="mb-0">{{ $commentContent }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="far fa-comment-dots text-primary me-3 fs-4"></i>
                                <p class="mb-0">No hay comentarios aprobados todavía. ¡Sé el primero en comentar!</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>







        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Lo más leído -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-chart-line text-primary me-2"></i> Lo más leído
                    </h5>
                </div>
                <div class="card-body">
                    <div class="most-read-list">
                        @forelse($mostViewedResearch as $index => $mostRead)
                            @php
                                $mostReadTitle = is_array($mostRead) ? ($mostRead['title'] ?? 'Investigación') : ($mostRead->title ?? 'Investigación');
                                $mostReadId = is_array($mostRead) ? ($mostRead['id'] ?? 0) : ($mostRead->id ?? 0);
                                $mostReadType = is_array($mostRead) ? ($mostRead['type'] ?? null) : ($mostRead->type ?? null);
                                $mostReadViews = is_array($mostRead) ? ($mostRead['views'] ?? 0) : ($mostRead->views ?? 0);
                            @endphp
                            <div class="most-read-item d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="most-read-number fs-4 me-3 text-primary fw-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="most-read-content">
                                    <a href="{{ route('research.show', $mostReadId) }}" class="text-decoration-none">
                                        <h6 class="mb-1">{{ $mostReadTitle }}</h6>
                                    </a>
                                    <div class="d-flex align-items-center small text-muted">
                                        @if($mostReadType)
                                        <span class="me-2">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $mostReadType }}
                                        </span>
                                        @endif
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            {{ number_format($mostReadViews) }} lecturas
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No hay investigaciones disponibles en este momento.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Artículos Relacionados -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-link text-primary me-2"></i> Investigaciones Relacionadas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($relatedResearch as $relatedItem)
                            @php
                                $relatedImage = is_array($relatedItem) ? ($relatedItem['image'] ?? null) : ($relatedItem->image ?? null);
                                $relatedTitle = is_array($relatedItem) ? ($relatedItem['title'] ?? 'Investigación') : ($relatedItem->title ?? 'Investigación');
                                $relatedId = is_array($relatedItem) ? ($relatedItem['id'] ?? 0) : ($relatedItem->id ?? 0);
                                $relatedCreatedAt = is_array($relatedItem) ? (isset($relatedItem['created_at']) ? \Carbon\Carbon::parse($relatedItem['created_at']) : now()) : ($relatedItem->created_at ?? now());
                            @endphp
                            <li class="list-group-item px-3 py-3 border-0">
                                <div class="d-flex">
                                    <div class="me-3" style="min-width: 70px;">
                                        <a href="{{ route('research.show', $relatedId) }}">
                                            @if($relatedImage)
                                                <img src="{{ asset('storage/' . $relatedImage) }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $relatedTitle }}" 
                                                     style="width: 70px; height: 70px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/research-default-small.jpg') }}';">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 70px; height: 70px;">
                                                    <i class="fas fa-flask text-secondary fa-2x"></i>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold" style="line-height: 1.4;">
                                            <a href="{{ route('research.show', $relatedId) }}" class="text-decoration-none text-dark">
                                                {{ Str::limit($relatedTitle, 60) }}
                                            </a>
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $relatedCreatedAt->locale('es')->isoFormat('D MMM, YYYY') }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item px-3 py-3 text-center text-muted border-0">
                                <i class="fas fa-info-circle me-2"></i>No hay investigaciones relacionadas disponibles.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Tipos populares -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-tags text-primary me-2"></i> Tipos de investigación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTypes as $typeItem)
                            @php
                                $itemType = is_array($typeItem) ? ($typeItem['type'] ?? '') : ($typeItem->type ?? '');
                                $itemCount = is_array($typeItem) ? ($typeItem['count'] ?? 0) : ($typeItem->count ?? 0);
                            @endphp
                            <a href="{{ route('research.type', $itemType) }}" class="badge bg-primary text-white text-decoration-none p-2">
                                <i class="fas fa-tag me-1"></i>{{ $itemType }} 
                                <span class="badge bg-light text-dark ms-1">{{ $itemCount }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Estilos para mejorar la apariencia general -->
<style>
.comment-item {
    transition: all 0.3s ease;
}
.comment-item:hover {
    transform: translateX(5px);
}
.most-read-item, .list-group-item {
    transition: all 0.2s ease;
}
.most-read-item:hover, .list-group-item:hover {
    background-color: rgba(0,0,0,0.01);
}

/* Estilos para los comentarios */
.comments-section .form-floating > .form-control {
    height: calc(3.5rem + 2px);
    line-height: 1.25;
}

.comments-section .form-floating > label {
    padding: 1rem 0.75rem;
}

.comments-section .comment-item {
    transition: all 0.3s ease;
}

.comments-section .comment-item:hover {
    transform: translateY(-2px);
}

.comments-section .comment-avatar div {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Animación para nuevos comentarios */
@keyframes highlightComment {
    0% { background-color: rgba(13, 110, 253, 0.1); }
    100% { background-color: transparent; }
}

.comment-new {
    animation: highlightComment 2s ease-out;
}
</style>


<!-- Estilos mejorados para el contenido de investigación -->
<style>
    /* Estilos para mejorar el formato del contenido de investigación */
.article-content {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #333;
}

/* Encabezados */
.article-content h2 {
    font-size: 1.8rem;
    margin-top: 2.5rem;
    margin-bottom: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eaeaea;
}

.article-content h3 {
    font-size: 1.5rem;
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #333;
}

.article-content h4 {
    font-size: 1.25rem;
    margin-top: 1.8rem;
    margin-bottom: 0.8rem;
    font-weight: 600;
    color: #444;
}

/* Párrafos */
.article-content p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

/* Listas */
.article-content ul, 
.article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

/* Citas */
.article-content blockquote {
    margin: 1.5rem 0;
    padding: 1rem 1.5rem;
    border-left: 4px solid #0d6efd;
    background-color: #f8f9fa;
    font-style: italic;
    color: #555;
}

.article-content blockquote p:last-child {
    margin-bottom: 0;
}

/* Código */
.article-content pre,
.article-content code {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.9em;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.article-content code {
    padding: 0.2em 0.4em;
}

.article-content pre {
    padding: 1rem;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    border: 1px solid #eaeaea;
}

/* Enlaces */
.article-content a {
    color: #0d6efd;
    text-decoration: none;
    transition: color 0.2s ease;
}

.article-content a:hover {
    color: #0a58ca;
    text-decoration: underline;
}

/* Imágenes */
.article-content img {
    max-width: 100%;
    height: auto;
    margin: 1.5rem auto;
    display: block;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.article-content figure {
    margin: 2rem 0;
}

.article-content figure figcaption {
    text-align: center;
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.5rem;
    font-style: italic;
}

/* Tablas */
.article-content table {
    width: 100%;
    margin-bottom: 1.5rem;
    border-collapse: collapse;
}

.article-content table th,
.article-content table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}

.article-content table th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-align: left;
}

.article-content table tr:nth-child(even) {
    background-color: #f8f9fa;
}

/* Destacados y notas */
.article-content .note {
    background-color: #e7f5ff;
    border-left: 4px solid #0d6efd;
    padding: 1rem;
    margin: 1.5rem 0;
    border-radius: 0 4px 4px 0;
}

.article-content .warning {
    background-color: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    margin: 1.5rem 0;
    border-radius: 0 4px 4px 0;
}

/* Resolución móvil */
@media (max-width: 768px) {
    .article-content {
        font-size: 1rem;
    }
    
    .article-content h2 {
        font-size: 1.6rem;
    }
    
    .article-content h3 {
        font-size: 1.35rem;
    }
    
    .article-content h4 {
        font-size: 1.2rem;
    }
}
</style>

<!-- Estilos para reducir el tamaño de letra en toda la página -->
<style>
    /* Reducción general del tamaño de letra para toda la página de investigación */

/* Contenido principal */
.article-content {
    font-size: 0.95rem !important;
    line-height: 1.6;
}

.article-content h2 {
    font-size: 1.5rem !important;
}

.article-content h3 {
    font-size: 1.3rem !important;
}

.article-content h4 {
    font-size: 1.15rem !important;
}

.article-content p, 
.article-content li,
.article-content blockquote {
    font-size: 0.95rem !important;
}

.article-content pre, 
.article-content code {
    font-size: 0.85rem !important;
}

/* Título principal */
.col-lg-8 h1 {
    font-size: 1.8rem !important;
}

/* Metadatos del artículo */
.article-meta {
    font-size: 0.8rem !important;
}

/* Resumen o extracto */
.lead {
    font-size: 0.95rem !important;
}

/* Citaciones */
.card .card-header h5 {
    font-size: 1rem !important;
}

.card .card-body {
    font-size: 0.9rem !important;
}

/* Sidebar (columna derecha) */
.col-lg-4 .card-header h5 {
    font-size: 0.9rem !important;
}

.col-lg-4 .card-body {
    font-size: 0.85rem !important;
}

.col-lg-4 h6 {
    font-size: 0.85rem !important;
    line-height: 1.4;
}

.col-lg-4 .small, 
.col-lg-4 .most-read-content .small {
    font-size: 0.75rem !important;
}

.col-lg-4 .badge {
    font-size: 0.7rem !important;
}

.most-read-number {
    font-size: 1.2rem !important;
}

/* Comentarios */
.comments-section h4 {
    font-size: 1.2rem !important;
}

.comments-section h5 {
    font-size: 1rem !important;
}

.comments-section .card-body {
    font-size: 0.9rem !important;
}

.comments-section .comment-meta h5 {
    font-size: 0.95rem !important;
}

.comments-section .comment-meta .small, 
.comments-section .comment-content p {
    font-size: 0.85rem !important;
}

/* Breadcrumb */
.breadcrumb {
    font-size: 0.8rem !important;
}

/* Botones y enlaces */
.btn {
    font-size: 0.85rem !important;
}

/* Ajustes específicos */
.form-text {
    font-size: 0.75rem !important;
}

.form-check-label {
    font-size: 0.8rem !important;
}

/* Formularios */
.form-control, 
.form-control::placeholder, 
.form-floating label {
    font-size: 0.85rem !important;
}
</style>


@endpush

@push('scripts')
<!-- JavaScript para el formulario de comentarios -->
<!-- JavaScript para el formulario de comentarios -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar formulario automáticamente si hay errores de validación
        @if($errors->any())
            var commentForm = document.getElementById('commentForm');
            var bsCollapse = new bootstrap.Collapse(commentForm, {
                toggle: true
            });
        @endif
        
        // Si hay un mensaje de comentario añadido, scroll al principio de la página
        @if(session('comment_added'))
            // Hacemos scroll al principio de la página después de un pequeño retraso
            // para asegurar que la página se ha cargado completamente
            setTimeout(function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 100);
        @endif
    });
</script>
@endpush