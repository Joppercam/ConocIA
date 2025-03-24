<!-- resources/views/components/comments.blade.php -->
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
                <form action="{{ url('/comments') }}" method="POST" id="commentForm">
                    @csrf
                    <input type="hidden" name="commentable_type" value="{{ $commentableType }}">
                    <input type="hidden" name="commentable_id" value="{{ $commentableId }}">
                    
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