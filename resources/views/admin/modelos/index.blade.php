@extends('admin.layouts.app')

@section('title', 'Modelos IA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Comparador de Modelos IA</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('modelos.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye me-1"></i>Ver portal
            </a>
            <a href="{{ route('admin.modelos.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nuevo modelo
            </a>
        </div>
    </div>

    @include('admin.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" style="font-size:.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th style="width:30px;">#</th>
                        <th>Modelo</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Acceso</th>
                        <th>Contexto</th>
                        <th>Precio input</th>
                        <th>MMLU</th>
                        <th>Destacado</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($models as $model)
                    <tr>
                        <td class="text-muted">{{ $model->sort_order }}</td>
                        <td class="fw-semibold">{{ $model->name }}</td>
                        <td>{{ $model->company }}</td>
                        <td><span class="badge bg-secondary">{{ $model->type }}</span></td>
                        <td>
                            <span class="badge {{ $model->access === 'open' ? 'bg-success' : 'bg-info text-dark' }}">
                                {{ $model->access }}
                            </span>
                        </td>
                        <td>{{ $model->context_window_label ?? '—' }}</td>
                        <td>{{ $model->price_input ? '$'.number_format($model->price_input,2) : ($model->has_free_tier ? 'Gratis' : '—') }}</td>
                        <td>{{ $model->score_mmlu ? $model->score_mmlu.'%' : '—' }}</td>
                        <td>
                            <span class="badge {{ $model->featured ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                                {{ $model->featured ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.modelos.toggle', $model) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $model->active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:.7rem;">
                                    {{ $model->active ? 'Activo' : 'Oculto' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.modelos.edit', $model) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.modelos.destroy', $model) }}" onsubmit="return confirm('¿Eliminar este modelo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">No hay modelos cargados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
