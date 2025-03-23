{{-- resources/views/admin/comments/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', $title ?? 'Gestión de Comentarios')

@section('content')
<div class="container-fluid">
    <!-- Título y badges -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            {{ $title ?? 'Gestión de Comentarios' }}
        </h1>
        <div>
            <span class="badge bg-warning text-dark">
                Pendientes: {{ \App\Models\Comment::where('status', 'pending')->count() }}
            </span>
            <span class="badge bg-success">
                Aprobados: {{ \App\Models\Comment::where('status', 'approved')->count() }}
            </span>
            <span class="badge bg-danger">
                Rechazados: {{ \App\Models\Comment::where('status', 'rejected')->count() }}
            </span>
        </div>
    </div>

    <!-- Pestañas de navegación -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'all' ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
                Todos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'pending' ? 'active' : '' }}" href="{{ route('admin.comments.pending') }}">
                Pendientes <span class="badge bg-warning text-dark">{{ \App\Models\Comment::where('status', 'pending')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'approved' ? 'active' : '' }}" href="{{ route('admin.comments.approved') }}">
                Aprobados
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'rejected' ? 'active' : '' }}" href="{{ route('admin.comments.rejected') }}">
                Rechazados
            </a>
        </li>
    </ul>

    <!-- Mensajes de alerta -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Lista de comentarios -->
    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 15%">Autor</th>
                            <th style="width: 30%">Comentario</th>
                            <th style="width: 15%">En</th>
                            <th style="width: 15%">Fecha</th>
                            <th style="width: 10%">Estado</th>
                            <th style="width: 10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($comment->user)
                                            <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=random' }}"
                                                class="rounded-circle" width="40" height="40" alt="{{ $comment->user->name }}">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 16px;">
                                                {{ strtoupper(substr($comment->guest_name ?? 'A', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $comment->user ? $comment->user->name : $comment->guest_name }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $comment->user ? 'Usuario' : $comment->guest_email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    @if($comment->parent_id)
                                        <div class="mb-1 text-muted">
                                            <i class="fas fa-reply fa-flip-horizontal me-1"></i> 
                                            Respuesta a: {{ \App\Models\Comment::find($comment->parent_id)->guest_name ?? 'Comentario #'.$comment->parent_id }}
                                        </div>
                                    @endif
                                    {{ Str::limit($comment->content, 100) }}
                                </div>
                                
                                <!-- Formulario de respuesta (colapsable) -->
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}">
                                        <i class="fas fa-reply me-1"></i> Responder
                                    </button>
                                    
                                    <div class="collapse mt-2" id="replyForm{{ $comment->id }}">
                                        <form action="{{ route('admin.comments.reply', $comment->id) }}" method="POST">
                                            @csrf
                                            <div class="input-group">
                                                <textarea class="form-control form-control-sm" name="content" rows="2" required
                                                          placeholder="Escribe tu respuesta..."></textarea>
                                                <button class="btn btn-sm btn-primary" type="submit">Enviar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($comment->commentable)
                                    @php
                                        $commentable = $comment->commentable;
                                        $type = class_basename($comment->commentable_type);
                                        $title = $commentable->title ?? $commentable->name ?? 'Contenido #'.$comment->commentable_id;
                                        
                                        // Determinar la ruta de enlace según el tipo
                                        $url = '#';
                                        if ($type == 'News' && isset($commentable->slug)) {
                                            $url = route('news.show', $commentable->slug);
                                        } elseif ($type == 'Column' && isset($commentable->slug)) {
                                            $url = route('columns.show', $commentable->slug);
                                        }
                                    @endphp
                                    
                                    <span class="badge bg-info">{{ $type }}</span>
                                    <div class="small mt-1">
                                        <a href="{{ $url }}" target="_blank">
                                            {{ Str::limit($title, 30) }}
                                        </a>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">No disponible</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $comment->created_at->format('d/m/Y') }}</div>
                                <div class="small text-muted">{{ $comment->created_at->format('H:i') }}</div>
                                <div class="small">{{ $comment->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                @if($comment->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($comment->status == 'approved')
                                    <span class="badge bg-success">Aprobado</span>
                                @elseif($comment->status == 'rejected')
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($comment->status != 'approved')
                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" title="Aprobar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($comment->status != 'rejected')
                                        <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm" title="Rechazar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-comments fa-2x mb-3"></i>
                                    <p>No hay comentarios disponibles.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $comments->links() }}
    </div>
</div>
@endsection