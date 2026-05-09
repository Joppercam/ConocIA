@extends('admin.layouts.app')

@section('title', 'Podcast — Episodios')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-podcast me-2 text-primary"></i> Podcast
        </h1>
        <a href="{{ url('/podcast.rss') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-rss me-1"></i> Ver RSS feed
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Stats -->
    <div class="row mb-4">
        @foreach([
            ['label'=>'Total', 'value'=>$stats['total'], 'color'=>'primary', 'icon'=>'podcast'],
            ['label'=>'Listos', 'value'=>$stats['ready'], 'color'=>'success', 'icon'=>'check-circle'],
            ['label'=>'En proceso', 'value'=>$stats['pending'], 'color'=>'warning', 'icon'=>'spinner'],
            ['label'=>'Errores', 'value'=>$stats['error'], 'color'=>'danger', 'icon'=>'exclamation-circle'],
        ] as $stat)
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-{{ $stat['color'] }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $stat['color'] }} text-uppercase mb-1">{{ $stat['label'] }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stat['value'] }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-{{ $stat['icon'] }} fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Tabla de episodios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Episodios</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Artículo</th>
                            <th>Estado</th>
                            <th>Duración</th>
                            <th>Generado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($episodes as $episode)
                        <tr>
                            <td>
                                <div class="fw-semibold" style="max-width:380px;">
                                    {{ \Illuminate\Support\Str::limit($episode->news->title ?? '—', 70) }}
                                </div>
                                @if($episode->news)
                                <small class="text-muted">
                                    <a href="{{ route('news.show', $episode->news->slug) }}" target="_blank">ver artículo</a>
                                </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeMap = ['ready'=>'success','processing'=>'warning','pending'=>'secondary','error'=>'danger'];
                                    $badge = $badgeMap[$episode->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $episode->status }}</span>
                                @if($episode->status === 'error' && $episode->error_message)
                                    <div class="text-danger small mt-1">{{ \Illuminate\Support\Str::limit($episode->error_message, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $episode->getDurationFormatted() ?: '—' }}</td>
                            <td>{{ $episode->generated_at ? $episode->generated_at->diffForHumans() : '—' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($episode->status === 'ready')
                                        <a href="{{ $episode->audio_url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Escuchar">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.podcast.regenerate', $episode) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Regenerar">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay episodios aún. Se generan automáticamente al publicar una noticia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($episodes->hasPages())
        <div class="card-footer">
            {{ $episodes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
