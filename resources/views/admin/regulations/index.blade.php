@extends('admin.layouts.app')

@section('title', 'Observatorio de Regulación')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Observatorio de Regulación IA</h1>
        <a href="{{ route('admin.regulations.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nueva regulación
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Chile --}}
    <div class="card mb-4">
        <div class="card-header py-2">
            <strong><i class="fas fa-flag me-1"></i>Regulaciones Chile</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Institución</th>
                            <th class="text-center">Análisis</th>
                            <th class="text-center">Impacto laboral</th>
                            <th class="text-center">Impacto económico</th>
                            <th class="text-center">Impacto social</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regulations->where('scope', 'chile') as $reg)
                        <tr>
                            <td style="max-width:280px;">
                                <a href="{{ route('regulacion.show', $reg->slug) }}" target="_blank"
                                   class="text-dark text-decoration-none small fw-semibold">
                                    {{ $reg->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $reg->status_color }}22;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}44;font-size:.7rem;">
                                    {{ $reg->status_label }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $reg->institution }}</td>
                            <td class="text-center">
                                @if($reg->content)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-circle text-secondary opacity-25"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_laboral)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-circle text-secondary opacity-25"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_economico)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-circle text-secondary opacity-25"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_social)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-circle text-secondary opacity-25"></i>
                                @endif
                            </td>
                            <td class="text-end" style="white-space:nowrap;">
                                <a href="{{ route('admin.regulations.edit', $reg) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.regulations.destroy', $reg) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta regulación?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Internacional --}}
    <div class="card">
        <div class="card-header py-2">
            <strong><i class="fas fa-globe me-1"></i>Regulaciones Internacionales</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Institución</th>
                            <th class="text-center">Análisis</th>
                            <th class="text-center">Impacto laboral</th>
                            <th class="text-center">Impacto económico</th>
                            <th class="text-center">Impacto social</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regulations->where('scope', 'internacional') as $reg)
                        <tr>
                            <td style="max-width:280px;">
                                <a href="{{ route('regulacion.show', $reg->slug) }}" target="_blank"
                                   class="text-dark text-decoration-none small fw-semibold">
                                    {{ $reg->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $reg->status_color }}22;color:{{ $reg->status_color }};border:1px solid {{ $reg->status_color }}44;font-size:.7rem;">
                                    {{ $reg->status_label }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $reg->institution }}</td>
                            <td class="text-center">
                                @if($reg->content) <i class="fas fa-check-circle text-success"></i>
                                @else <i class="fas fa-circle text-secondary opacity-25"></i> @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_laboral) <i class="fas fa-check-circle text-success"></i>
                                @else <i class="fas fa-circle text-secondary opacity-25"></i> @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_economico) <i class="fas fa-check-circle text-success"></i>
                                @else <i class="fas fa-circle text-secondary opacity-25"></i> @endif
                            </td>
                            <td class="text-center">
                                @if($reg->impact_social) <i class="fas fa-check-circle text-success"></i>
                                @else <i class="fas fa-circle text-secondary opacity-25"></i> @endif
                            </td>
                            <td class="text-end" style="white-space:nowrap;">
                                <a href="{{ route('admin.regulations.edit', $reg) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.regulations.destroy', $reg) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta regulación?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="small text-muted mt-2">
        <i class="fas fa-check-circle text-success me-1"></i>= contenido disponible
        &nbsp;&nbsp;
        <i class="fas fa-circle text-secondary opacity-25 me-1"></i>= pendiente de redacción
    </p>
</div>
@endsection
