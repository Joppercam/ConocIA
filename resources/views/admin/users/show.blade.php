@extends('admin.layouts.app')

@section('title', $user->name)

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0">Perfil de usuario</h1>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Profile card --}}
        <div class="col-lg-4">
            <div class="card shadow-sm text-center mb-4">
                <div class="card-body py-4">
                    <img src="{{ asset($user->avatar ?? $user->profile_photo ?? 'images/defaults/user-profile.jpg') }}"
                         class="rounded-circle mb-3"
                         width="90" height="90"
                         style="object-fit:cover;border:3px solid #e9ecef;"
                         onerror="this.src='{{ asset('images/defaults/user-profile.jpg') }}';">
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <div class="text-muted small mb-2">{{ $user->email }}</div>
                    @php
                        $roleColors = ['admin'=>'danger','editor'=>'warning','author'=>'info','user'=>'secondary'];
                        $slug = $user->role?->slug ?? 'user';
                        $color = $roleColors[$slug] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} mb-3">{{ $user->role?->name ?? 'Sin rol' }}</span>

                    @if($user->bio)
                    <p class="text-muted small mb-3">{{ $user->bio }}</p>
                    @endif

                    <div class="d-flex justify-content-center gap-2">
                        @if($user->twitter)
                        <a href="https://twitter.com/{{ ltrim($user->twitter, '@') }}"
                           target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if($user->linkedin)
                        <a href="{{ $user->linkedin }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-0" style="font-size:.83rem;">Estado</td>
                            <td>
                                @if($user->is_active)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0" style="font-size:.83rem;">Columnas</td>
                            <td class="fw-semibold">{{ $user->columns->count() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0" style="font-size:.83rem;">Registrado</td>
                            <td style="font-size:.83rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Columns --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Columnas publicadas</h6>
                    <a href="{{ route('admin.columns.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Nueva columna
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($user->columns->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Título</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Vistas</th>
                                    <th>Fecha</th>
                                    <th class="pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->columns as $col)
                                <tr>
                                    <td class="ps-3" style="font-size:.88rem;">
                                        {{ Str::limit($col->title, 50) }}
                                        @if($col->featured)
                                        <span class="badge bg-warning ms-1" style="font-size:.65rem;">Destacado</span>
                                        @endif
                                    </td>
                                    <td style="font-size:.82rem;">{{ $col->category?->name ?? '—' }}</td>
                                    <td class="text-center" style="font-size:.82rem;">{{ number_format($col->views) }}</td>
                                    <td style="font-size:.82rem;color:#666;">{{ $col->published_at?->format('d/m/Y') }}</td>
                                    <td class="pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.columns.edit', $col) }}" class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('columns.show', $col->slug) }}" class="btn btn-outline-info" target="_blank" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-pen-fancy fa-2x mb-2 d-block opacity-25"></i>
                        Este usuario no tiene columnas publicadas aún.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
