@extends('admin.layouts.app')

@section('title', 'Podcast — Episodios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Podcast</h1>
        <a href="{{ route('podcast.rss') }}" target="_blank" class="btn btn-sm btn-outline-warning">
            <i class="fas fa-rss me-1"></i> Ver RSS feed
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['ready'] }}</div>
                <div class="text-muted small">Listos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['pending'] }}</div>
                <div class="text-muted small">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-danger">{{ $stats['error'] }}</div>
                <div class="text-muted small">Con error</div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Artículo</th>
                            <th>Estado</th>
                            <th>Duración</th>
                            <th>Generado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($episodes as $episode)
                        <tr>
                            <td class="align-middle">
                                <div class="fw-semibold" style="max-width:340px;">
                                    {{ Str::limit($episode->news?->title ?? '—', 70) }}
                                </div>
                            </td>
                            <td class="align-middle">
                                @if($episode->status === 'ready')
                                    <span class="badge bg-success">Listo</span>
                                @elseif($episode->status === 'processing')
                                    <span class="badge bg-info">Procesando</span>
                                @elseif($episode->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @else
                                    <span class="badge bg-danger" title="{{ $episode->error_message }}">Error</span>
                                @endif
                            </td>
                            <td class="align-middle text-muted small">
                                {{ $episode->duration_formatted ?? '—' }}
                            </td>
                            <td class="align-middle text-muted small">
                                {{ $episode->generated_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="align-middle text-end">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    @if($episode->isReady())
                                        <a href="{{ $episode->audio_url }}" target="_blank"
                                           class="btn btn-sm btn-outline-primary" title="Escuchar">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.podcast.regenerate', $episode) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Re-generar">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.podcast.destroy', $episode) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este episodio?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No hay episodios todavía.<br>
                                <small>Se generan automáticamente al publicar un artículo.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $episodes->links() }}
    </div>
</div>
@endsection
