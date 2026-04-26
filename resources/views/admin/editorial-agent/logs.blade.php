@extends('admin.layouts.app')

@section('title', 'Bitácora del Agente Editorial')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <a href="{{ route('admin.editorial-agent.index') }}" class="text-decoration-none small">&larr; Volver al agente</a>
            <h1 class="h3 mt-2 mb-0">Bitácora del Agente Editorial</h1>
            <p class="text-muted mb-0">Registro operativo de ejecuciones, pausas, publicaciones y revisiones.</p>
        </div>
        <form action="{{ route('admin.editorial-agent.logs') }}" method="GET" class="d-flex gap-2">
            <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos los niveles</option>
                @foreach($levels as $level)
                    <option value="{{ $level }}" @selected(request('level') === $level)>{{ ucfirst($level) }}</option>
                @endforeach
            </select>
            <select name="event" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos los eventos</option>
                @foreach($events as $event)
                    <option value="{{ $event }}" @selected(request('event') === $event)>{{ str_replace('_', ' ', $event) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(isset($tableReady) && !$tableReady)
        <div class="alert alert-warning">
            La tabla de bitácora todavía no existe. Ejecuta las migraciones del deploy para activar esta vista.
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:150px;">Fecha</th>
                        <th style="width:110px;">Nivel</th>
                        <th style="width:190px;">Evento</th>
                        <th>Mensaje</th>
                        <th style="width:110px;">Tarea</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="small text-muted">{{ $log->occurred_at?->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <span class="badge {{ $log->level === 'error' ? 'bg-danger' : ($log->level === 'warning' ? 'bg-warning text-dark' : 'bg-info') }}">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </td>
                            <td><code>{{ $log->event }}</code></td>
                            <td>
                                <div>{{ $log->message }}</div>
                                @if(!empty($log->context))
                                    <details class="mt-2">
                                        <summary class="small text-muted">Ver contexto</summary>
                                        <pre class="bg-light border rounded p-2 small mt-2 mb-0">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td>
                                @if(isset($log->task) && $log->task)
                                    <a href="{{ route('admin.editorial-agent.show', $log->task) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Todavía no hay eventos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
@endsection
