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
                    @if($research->type)
                    <li class="breadcrumb-item"><a href="{{ route('research.type', $research->type) }}">{{ $research->type }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $research->title }}</li>
                </ol>
            </nav>

            <!-- Título y metadatos -->
            <h1 class="mb-3">{{ $research->title }}</h1>
            <div class="article-meta d-flex flex-wrap align-items-center mb-4 text-muted small">
                <div class="me-4 mb-2">
                    <i class="far fa-calendar-alt me-1"></i> {{ $research->created_at->format('d M, Y') }}
                </div>
                @if($research->type)
                <div class="me-4 mb-2">
                    <i class="fas fa-tag me-1"></i> 
                    <a href="{{ route('research.type', $research->type) }}" class="text-decoration-none">{{ $research->type }}</a>
                </div>
                @endif
                <div class="me-4 mb-2">
                    <i class="far fa-eye me-1"></i> {{ number_format($research->views) }} lecturas
                </div>
                @if($research->author)
                <div class="mb-2">
                    <i class="far fa-user me-1"></i> {{ $research->author }}
                </div>
                @endif
            </div>

            <!-- Imagen destacada -->
            @if($research->image)
            <div class="article-featured-image mb-4">
                <img src="{{ asset('storage/' . $research->image) }}" alt="{{ $research->title }}" class="img-fluid rounded">
            </div>
            @endif

            <!-- Resumen -->
            @if($research->excerpt)
            <div class="lead mb-4">
                {{ $research->excerpt }}
            </div>
            @endif

            <!-- Contenido del artículo -->
            <div class="article-content mb-5">
                {!! $research->content !!}
            </div>

            <!-- Citaciones -->
            @if($research->citations)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Citaciones</h5>
                </div>
                <div class="card-body">
                    {!! $research->citations !!}
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
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($research->title) }}" target="_blank" class="btn btn-outline-info me-2 mb-2">
                        <i class="fab fa-twitter me-1"></i> Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($research->title) }}" target="_blank" class="btn btn-outline-secondary me-2 mb-2">
                        <i class="fab fa-linkedin-in me-1"></i> LinkedIn
                    </a>
                    <a href="mailto:?subject={{ $research->title }}&body={{ urlencode(url()->current()) }}" class="btn btn-outline-dark mb-2">
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
                                        @if($relatedItem->image)
                                            <a href="{{ route('research.show', $relatedItem->id) }}">
                                                <img src="{{ asset('storage/' . $relatedItem->image) }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $relatedItem->title }}" 
                                                     style="width: 100px; height: 70px; object-fit: cover;">
                                            </a>
                                        @else
                                            <a href="{{ route('research.show', $relatedItem->id) }}">
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 70px;">
                                                    <i class="fas fa-flask text-secondary fa-2x"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="related-article-content">
                                        <h6 class="mb-1">
                                            <a href="{{ route('research.show', $relatedItem->id) }}" class="text-decoration-none">
                                                {{ Str::limit($relatedItem->title, 60) }}
                                            </a>
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            {{ $relatedItem->created_at->format('d M, Y') }}
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
                <h4 class="mb-3">Comentarios ({{ $research->comments_count ?? 0 }})</h4>
                
                <!-- Formulario de comentario -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Deja tu comentario</h5>
                        <form action="{{ url('/comments') }}" method="POST">
                            @csrf
                            <input type="hidden" name="commentable_type" value="App\Models\Research">
                            <input type="hidden" name="commentable_id" value="{{ $research->id }}">
                            
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
                    @forelse($research->comments ?? [] as $comment)
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
                            <div class="most-read-item d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="most-read-number fs-4 me-3 text-primary fw-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="most-read-content">
                                    <a href="{{ route('research.show', $mostRead->id) }}" class="text-decoration-none">
                                        <h6 class="mb-1">{{ $mostRead->title }}</h6>
                                    </a>
                                    <div class="d-flex align-items-center small text-muted">
                                        @if($mostRead->type)
                                        <span class="me-2">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $mostRead->type }}
                                        </span>
                                        @endif
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            {{ number_format($mostRead->views) }} lecturas
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
                            <a href="{{ route('research.type', $typeItem->type) }}" class="badge bg-primary text-decoration-none">
                                {{ $typeItem->type }} <span class="badge bg-light text-dark">{{ $typeItem->count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection