@extends('admin.layouts.app')

@section('title', 'Revisar propuesta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.editorial-agent.index') }}" class="text-decoration-none small">&larr; Volver al agente</a>
            <h1 class="h3 mt-2 mb-0">{{ $task->title }}</h1>
        </div>
        <span class="badge bg-light text-dark border">{{ $task->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Resumen</div>
                        <p class="mb-0">{{ $task->summary ?: 'Sin resumen disponible.' }}</p>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small text-uppercase fw-bold">Acción sugerida</div>
                        <p class="mb-0">{{ $task->suggested_action ?: 'Sin acción sugerida.' }}</p>
                    </div>
                    @if($task->content_url)
                        <a href="{{ $task->content_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                            Abrir contenido
                        </a>
                    @endif
                </div>
            </div>

            @if($task->payload || $task->source_urls)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Detalle técnico</h2>
                        @if($task->source_urls)
                            <div class="mb-3">
                                <div class="text-muted small text-uppercase fw-bold">Fuentes</div>
                                @foreach($task->source_urls as $url)
                                    <div><a href="{{ $url }}" target="_blank" rel="noopener">{{ $url }}</a></div>
                                @endforeach
                            </div>
                        @endif
                        @if($task->payload)
                            <pre class="bg-light border rounded p-3 small mb-0">{{ json_encode($task->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Tipo</div>
                        <div>{{ $task->task_type_label }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Prioridad</div>
                        <div>{{ $task->priority_label }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Creada</div>
                        <div>{{ $task->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($task->status === 'pending')
                        <form action="{{ route('admin.editorial-agent.approve', $task) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <textarea name="review_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="Nota opcional"></textarea>
                            <button class="btn btn-success w-100">Aprobar</button>
                        </form>
                        <form action="{{ route('admin.editorial-agent.reject', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-outline-danger w-100">Descartar</button>
                        </form>
                    @elseif($task->status === 'approved')
                        <form action="{{ route('admin.editorial-agent.complete', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-primary w-100">Marcar como ejecutada</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
