@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Investigaciones Pendientes de Aprobación</h1>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Autor</th>
                            <th>Fecha de envío</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPosts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>
                                    <a href="{{ route('admin.research.show', $post) }}">
                                        {{ Str::limit($post->title, 50) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($post->research_type) }}</span>
                                </td>
                                <td>{{ $post->category->name ?? 'Sin categoría' }}</td>
                                <td>{{ $post->author->name ?? 'Sin autor' }}</td>
                                <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.research.show', $post) }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success approve-btn" 
                                            data-id="{{ $post->id }}" 
                                            data-title="{{ $post->title }}"
                                            title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger reject-btn" 
                                            data-id="{{ $post->id }}" 
                                            data-title="{{ $post->title }}"
                                            title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay investigaciones pendientes de aprobación</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pendingPosts->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de aprobación -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Aprobar investigación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas aprobar la investigación "<span id="approve-title"></span>"?
                <p class="mt-2">La investigación será publicada inmediatamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="approve-form" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rechazo -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rechazar investigación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reject-form" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Estás a punto de rechazar la investigación "<span id="reject-title"></span>".</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                        <div class="form-text">Esta información será enviada al autor.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal de aprobación
    const approveButtons = document.querySelectorAll('.approve-btn');
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('approve-title').textContent = title;
            document.getElementById('approve-form').action = "{{ route('admin.invitados.approve', '') }}/" + id;
            
            const modal = new bootstrap.Modal(document.getElementById('approveModal'));
            modal.show();
        });
    });
    
    // Modal de rechazo
    const rejectButtons = document.querySelectorAll('.reject-btn');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            document.getElementById('reject-title').textContent = title;
            document.getElementById('reject-form').action = "{{ route('admin.invitados.reject', '') }}/" + id;
            
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        });
    });
</script>
@endpush