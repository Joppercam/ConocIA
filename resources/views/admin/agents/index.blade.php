@extends('admin.layouts.app')

@section('title', 'Agentes IA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Agentes de IA</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('agents.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye me-1"></i>Ver portal
            </a>
            <a href="{{ route('admin.agents.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nuevo agente
            </a>
        </div>
    </div>

    @include('admin.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" style="font-size:.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>Agente</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Stars GitHub</th>
                        <th>Auto</th>
                        <th>Destacado</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agents as $agent)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $agent->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($agent->tagline, 60) }}</div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $agent->category_label }}</span></td>
                        <td>
                            <span class="badge {{ $agent->type === 'open-source' ? 'bg-success' : 'bg-info text-dark' }}">
                                {{ $agent->type }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $agent->pricing_color }};">{{ $agent->pricing_label }}</span>
                        </td>
                        <td>{{ $agent->formatted_stars }}</td>
                        <td>
                            <span class="badge {{ $agent->auto_generated ? 'bg-info text-dark' : 'bg-light text-muted' }}">
                                {{ $agent->auto_generated ? 'IA' : 'Manual' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $agent->featured ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                                {{ $agent->featured ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.agents.toggle', $agent) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $agent->active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:.7rem;">
                                    {{ $agent->active ? 'Activo' : 'Oculto' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}" onsubmit="return confirm('¿Eliminar este agente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay agentes cargados. Ejecutá <code>php artisan agents:fetch</code></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $agents->links() }}</div>
</div>
@endsection
