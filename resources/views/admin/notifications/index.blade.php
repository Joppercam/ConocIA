{{-- resources/views/admin/notifications/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notificaciones</h1>
        
        @if($notifications->where('read_at', null)->count() > 0)
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Historial de notificaciones</h6>
            
            @if($notifications->count() > 0)
                <div class="btn-group">
                    <a href="{{ route('admin.notifications.index') }}?filter=all" class="btn btn-sm {{ request()->get('filter') != 'read' && request()->get('filter') != 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">Todas</a>
                    <a href="{{ route('admin.notifications.index') }}?filter=unread" class="btn btn-sm {{ request()->get('filter') == 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">No leídas</a>
                    <a href="{{ route('admin.notifications.index') }}?filter=read" class="btn btn-sm {{ request()->get('filter') == 'read' ? 'btn-primary' : 'btn-outline-primary' }}">Leídas</a>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ is_null($notification->read_at) ? 'list-group-item-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">
                                    @if($notification->type == 'comment')
                                        <i class="fas fa-comment text-info me-2"></i> Nuevo comentario
                                    @elseif($notification->type == 'subscription')
                                        <i class="fas fa-user-check text-primary me-2"></i> Nueva suscripción
                                    @elseif($notification->type == 'comment_approved')
                                        <i class="fas fa-check-circle text-success me-2"></i> Comentario aprobado
                                    @elseif($notification->type == 'comment_rejected')
                                        <i class="fas fa-times-circle text-danger me-2"></i> Comentario rechazado
                                    @elseif($notification->type == 'comment_reply')
                                        <i class="fas fa-reply text-success me-2"></i> Respuesta a comentario
                                    @else
                                        <i class="fas fa-bell text-warning me-2"></i> Notificación
                                    @endif
                                </h5>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>