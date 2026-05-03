@extends('admin.layouts.app')

@section('title', 'Agente Editorial')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Agente Editorial</h1>
            <p class="text-muted mb-0">Propuestas generadas para revisar, aprobar y ejecutar con control editorial.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.editorial-agent.create-news') }}" class="btn btn-primary btn-sm">Solicitar noticia</a>
            <a href="{{ route('admin.editorial-agent.logs') }}" class="btn btn-outline-secondary btn-sm">Ver bitácora</a>
            <form action="{{ route('admin.editorial-agent.index') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pending" @selected($status === 'pending')>Pendientes</option>
                    <option value="approved" @selected($status === 'approved')>Aprobadas</option>
                    <option value="completed" @selected($status === 'completed')>Ejecutadas</option>
                    <option value="rejected" @selected($status === 'rejected')>Descartadas</option>
                    <option value="all" @selected($status === 'all')>Todas</option>
                </select>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if(isset($tableReady) && !$tableReady)
        <div class="alert alert-warning">
            La tabla del agente editorial todavia no existe. Ejecuta las migraciones del deploy para activar esta seccion.
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pendientes</div>
                    <div class="h4 mb-0">{{ $counts['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Aprobadas</div>
                    <div class="h4 mb-0">{{ $counts['approved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ejecutadas</div>
                    <div class="h4 mb-0">{{ $counts['completed'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Descartadas</div>
                    <div class="h4 mb-0">{{ $counts['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <form id="bulkEditorialForm" action="{{ route('admin.editorial-agent.bulk-action') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                @method('PATCH')
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Acción masiva</label>
                    <select name="bulk_action" class="form-select form-select-sm" required>
                        <option value="">Seleccionar acción</option>
                        <option value="approve">Aprobar seleccionadas</option>
                        <option value="complete">Marcar ejecutadas</option>
                        <option value="reject">Descartar seleccionadas</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Nota opcional</label>
                    <input type="text" name="review_notes" class="form-control form-control-sm" maxlength="2000" placeholder="Ej: aprobado por lote, duplicadas, ya revisadas...">
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-sm btn-primary" id="bulkSubmitButton" disabled>
                        Aplicar a seleccionadas
                    </button>
                </div>
            </form>
            <div class="small text-muted mt-2">
                <span id="selectedCount">0</span> seleccionada(s). Al aprobar borradores de noticia se crean como borradores, no se publican.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:42px;">
                            <input type="checkbox" class="form-check-input" id="selectAllTasks" aria-label="Seleccionar todas las propuestas visibles">
                        </th>
                        <th>Propuesta</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Creada</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="form-check-input editorial-task-checkbox"
                                       name="task_ids[]"
                                       value="{{ $task->id }}"
                                       form="bulkEditorialForm"
                                       aria-label="Seleccionar {{ $task->title }}">
                            </td>
                            <td>
                                <a href="{{ route('admin.editorial-agent.show', $task) }}" class="fw-semibold text-decoration-none">
                                    {{ $task->title }}
                                </a>
                                @if($task->summary)
                                    <div class="text-muted small">{{ Str::limit($task->summary, 120) }}</div>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $task->task_type_label }}</span></td>
                            <td>
                                <span class="badge {{ $task->priority === 'high' ? 'bg-danger' : ($task->priority === 'low' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td>{{ $task->status_label }}</td>
                            <td class="text-muted small">{{ $task->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.editorial-agent.show', $task) }}" class="btn btn-sm btn-outline-primary">Revisar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay propuestas para este filtro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $tasks->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllTasks');
            const checkboxes = Array.from(document.querySelectorAll('.editorial-task-checkbox'));
            const selectedCount = document.getElementById('selectedCount');
            const submitButton = document.getElementById('bulkSubmitButton');
            const form = document.getElementById('bulkEditorialForm');

            function updateBulkState() {
                const checked = checkboxes.filter((checkbox) => checkbox.checked).length;
                selectedCount.textContent = checked;
                submitButton.disabled = checked === 0;

                if (selectAll) {
                    selectAll.checked = checked > 0 && checked === checkboxes.length;
                    selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkState();
                });
            }

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateBulkState));

            if (form) {
                form.addEventListener('submit', function (event) {
                    const checked = checkboxes.filter((checkbox) => checkbox.checked).length;
                    const action = form.querySelector('[name="bulk_action"]').value;

                    if (checked === 0 || !action) {
                        event.preventDefault();
                        return;
                    }

                    const label = action === 'approve' ? 'aprobar' : (action === 'complete' ? 'marcar como ejecutadas' : 'descartar');
                    if (!confirm(`Vas a ${label} ${checked} propuesta(s). ¿Continuar?`)) {
                        event.preventDefault();
                    }
                });
            }

            updateBulkState();
        });
    </script>
@endsection
