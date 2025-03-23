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
                    <a href="{{ route('research.type', $type) }}" class="text-decoration-none">{{ $type }}</a>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="far fa-comments text-primary me-2"></i>Comentarios ({{ $comments_count }})</h4>
                </div>
                
                <!-- Formulario de comentario -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 d-flex align-items-center">
                            <i class="far fa-edit me-2 text-primary"></i>Deja tu comentario
                        </h5>
                        <form action="{{ url('/comments') }}" method="POST">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\Research">
                            <input type="hidden" name="commentable_id" value="{{ $id }}">
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="name" class="form-label">Nombre</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="guest_name" value="{{ old('guest_name') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="guest_email" value="{{ old('guest_email') }}" required>
                                    </div>
                                    <div class="form-text small text-muted">Tu email no será publicado.</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comentario</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-comment"></i></span>
                                    <textarea class="form-control" id="comment" name="content" rows="4" required>{{ old('content') }}</textarea>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="saveInfo" name="save_info" {{ old('save_info') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="saveInfo">
                                    Guardar mi nombre y email para la próxima vez que comente.
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane me-2"></i>Publicar comentario
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de comentarios -->
                <div class="comments-list">
                    @forelse($comments as $comment)
                        <div class="comment-item mb-4 p-3 border-start border-primary border-3 bg-light rounded shadow-sm">
                            <div class="d-flex mb-2">
                                <div class="comment-avatar me-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 20px;">
                                        @php
                                            $commentName = is_array($comment) ? ($comment['guest_name'] ?? 'A') : ($comment->guest_name ?? 'A');
                                            $commentContent = is_array($comment) ? ($comment['content'] ?? 'Sin contenido') : ($comment->content ?? 'Sin contenido');
                                            $commentCreatedAt = is_array($comment) ? (isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at']) : null) : ($comment->created_at ?? null);
                                        @endphp
                                        {{ strtoupper(substr($commentName, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="comment-meta">
                                    <h5 class="mb-1 fs-6 fw-bold">{{ $commentName }}</h5>
                                    <div class="text-muted small">
                                        <i class="far fa-clock me-1"></i> 
                                        {{ $commentCreatedAt ? $commentCreatedAt->locale('es')->diffForHumans() : 'Hace algún tiempo' }}
                                    </div>
                                </div>
                            </div>
                            <div class="comment-content mt-2 ps-5">
                                <p class="mb-0">{{ $commentContent }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light shadow-sm">
                            <p class="mb-0 text-center">
                                <i class="far fa-comments me-2 text-muted"></i>
                                No hay comentarios todavía. ¡Sé el primero en comentar!
                            </p>
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
            
            <!-- Artículos Relacionados (Movido aquí desde abajo) -->
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

<style>
/* Estilos para mejorar la apariencia */
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
</style>
@endsection