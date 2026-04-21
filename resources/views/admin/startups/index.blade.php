@extends('admin.layouts.app')

@section('title', 'Startups IA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Startups de IA</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('startups.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye me-1"></i>Ver portal
            </a>
            <a href="{{ route('admin.startups.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>Nueva startup
            </a>
        </div>
    </div>

    @include('admin.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" style="font-size:.85rem;">
                <thead class="table-dark">
                    <tr>
                        <th>Startup</th>
                        <th>Sector</th>
                        <th>Etapa</th>
                        <th>Funding</th>
                        <th>País</th>
                        <th>Auto</th>
                        <th>Destacada</th>
                        <th>Activa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($startups as $startup)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $startup->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($startup->tagline, 60) }}</div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $startup->sector ?? '—' }}</span></td>
                        <td>
                            @if($startup->stage)
                            <span class="badge" style="background:{{ $startup->stage_color }};">{{ $startup->stage_label }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $startup->funding_label }}</td>
                        <td>{{ $startup->country ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $startup->auto_generated ? 'bg-info text-dark' : 'bg-light text-muted' }}">
                                {{ $startup->auto_generated ? 'IA' : 'Manual' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $startup->featured ? 'bg-warning text-dark' : 'bg-light text-muted' }}">
                                {{ $startup->featured ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.startups.toggle', $startup) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $startup->active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:.7rem;">
                                    {{ $startup->active ? 'Activa' : 'Oculta' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.startups.edit', $startup) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.startups.destroy', $startup) }}" onsubmit="return confirm('¿Eliminar esta startup?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay startups cargadas. Ejecutá <code>php artisan startups:fetch</code></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $startups->links() }}</div>
</div>
@endsection
