@extends('admin.layouts.app')

@section('title', 'Agenda de Eventos IA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Agenda de Eventos IA</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('agenda.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye me-1"></i>Ver portal
            </a>
            <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nuevo evento
            </a>
        </div>
    </div>

    @include('admin.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" style="font-size:.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>Evento</th>
                        <th>Tipo</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Lugar</th>
                        <th>Online</th>
                        <th>Destacado</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td class="fw-semibold">{{ $event->title }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $event->type }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}</td>
                        <td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') : '—' }}</td>
                        <td>{{ $event->location ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $event->is_online ? 'bg-info text-dark' : 'bg-light text-muted' }}">
                                {{ $event->is_online ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $event->featured ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                                {{ $event->featured ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.agenda.toggle', $event) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $event->active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:.7rem;">
                                    {{ $event->active ? 'Activo' : 'Oculto' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.agenda.edit', $event) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.agenda.destroy', $event) }}" onsubmit="return confirm('¿Eliminar este evento?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay eventos cargados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
