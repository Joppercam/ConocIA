@extends('admin.layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Usuarios</h1>
            <p class="text-muted small mb-0">{{ $users->total() }} usuarios registrados</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo usuario
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar por nombre o email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['search','role']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Usuario</th>
                            <th>Rol</th>
                            <th class="text-center">Columnas</th>
                            <th class="text-center">Estado</th>
                            <th>Registrado</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($user->avatar ?? $user->profile_photo ?? 'storage/images/defaults/user-profile.jpg') }}"
                                         class="rounded-circle flex-shrink-0"
                                         width="36" height="36"
                                         style="object-fit:cover;"
                                         onerror="this.src='{{ asset('storage/images/defaults/user-profile.jpg') }}';">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.9rem;">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size:.78rem;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleColors = ['admin'=>'danger','editor'=>'warning','author'=>'info','user'=>'secondary'];
                                    $slug = $user->role?->slug ?? 'user';
                                    $color = $roleColors[$slug] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $user->role?->name ?? 'Sin rol' }}</span>
                            </td>
                            <td class="text-center">
                                @if($user->columns_count > 0)
                                <a href="{{ route('admin.columns.index') }}?author={{ $user->id }}"
                                   class="badge bg-primary text-decoration-none">
                                    {{ $user->columns_count }}
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td style="font-size:.82rem;color:#666;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn btn-outline-info" title="Ver perfil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar"
                                                onclick="return confirm('¿Eliminar a {{ addslashes($user->name) }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
