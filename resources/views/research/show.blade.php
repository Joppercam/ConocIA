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
                <img src="{{ asset('storage/' . $image) }}" alt="{{ $title }}" class="img-fluid rounded">
            </div>
            @endif

            <!-- Resumen -->
            @if($excerpt)
            <div class="lead mb-4">
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
                    <h5 class="mb-0">Citaciones</h5>
                </div>
                <div class="card-body">
                    {!! $citations !!}
                </div>
            </div>
            @endif

            <!-- Compartir -->
            <div class="article-share mb-5">
                <h5 class="mb-3">Compartir esta investigación</h5>
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

            <!-- Artículos Relacionados -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Investigaciones Relacionadas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($relatedResearch as $relatedItem)
                            <div class="col-md-6">
                                <div class="related-article d-flex">
                                    <div class="related-article-img me-3">
                                        @php
                                            $relatedImage = is_array($relatedItem) ? ($relatedItem['image'] ?? null) : ($relatedItem->image ?? null);
                                            $relatedTitle = is_array($relatedItem) ? ($relatedItem['title'] ?? 'Investigación') : ($relatedItem->title ?? 'Investigación');
                                            $relatedId = is_array($relatedItem) ? ($relatedItem['id'] ?? 0) : ($relatedItem->id ?? 0);
                                            $relatedCreatedAt = is_array($relatedItem) ? (isset($relatedItem['created_at']) ? \Carbon\Carbon::parse($relatedItem['created_at']) : now()) : ($relatedItem->created_at ?? now());
                                        @endphp
                                        
                                        @if($relatedImage)
                                            <a href="{{ route('research.show', $relatedId) }}">
                                                <img src="{{ asset('storage/' . $relatedImage) }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $relatedTitle }}" 
                                                     style="width: 100px; height: 70px; object-fit: cover;">
                                            </a>
                                        @else
                                            <a href="{{ route('research.show', $relatedId) }}">
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 70px;">
                                                    <i class="fas fa-flask text-secondary fa-2x"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="related-article-content">
                                        <h6 class="mb-1">
                                            <a href="{{ route('research.show', $relatedId) }}" class="text-decoration-none">
                                                {{ Str::limit($relatedTitle, 60) }}
                                            </a>
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $relatedCreatedAt->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No hay investigaciones relacionadas disponibles.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Comentarios -->
            <div class="mb-4">
                <h4 class="mb-3">Comentarios ({{ $comments_count }})</h4>
                
                <!-- Formulario de comentario -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Deja tu comentario</h5>
                        <form action="{{ url('/comments') }}" method="POST">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\Research">
                            <input type="hidden" name="commentable_id" value="{{ $id }}">
                            
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
                    @forelse($comments as $comment)
                        <div class="comment-item mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
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
                                    <h5 class="mb-1">{{ $commentName }}</h5>
                                    <div class="text-muted small">
                                        <i class="far fa-clock me-1"></i> 
                                        {{ $commentCreatedAt ? $commentCreatedAt->locale('es')->diffForHumans() : 'Hace algún tiempo' }}
                                    </div>
                                </div>
                            </div>
                            <div class="comment-content">
                                <p>{{ $commentContent }}</p>
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

            <!-- Tipos populares -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tipos de investigación</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($popularTypes as $typeItem)
                            @php
                                $itemType = is_array($typeItem) ? ($typeItem['type'] ?? '') : ($typeItem->type ?? '');
                                $itemCount = is_array($typeItem) ? ($typeItem['count'] ?? 0) : ($typeItem->count ?? 0);
                            @endphp
                            <a href="{{ route('research.type', $itemType) }}" class="badge bg-primary text-decoration-none">
                                {{ $itemType }} <span class="badge bg-light text-dark">{{ $itemCount }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection