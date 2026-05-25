@extends('admin.layouts.app')

@section('title', $regulation->exists ? 'Editar regulación' : 'Nueva regulación')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.regulations.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h4 mb-0">
            {{ $regulation->exists ? 'Editar: ' . \Illuminate\Support\Str::limit($regulation->title, 60) : 'Nueva regulación' }}
        </h1>
        @if($regulation->exists)
            <a href="{{ route('regulacion.show', $regulation->slug) }}" target="_blank"
               class="btn btn-sm btn-outline-primary ms-auto">
                <i class="fas fa-external-link-alt me-1"></i>Ver en sitio
            </a>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $regulation->exists ? route('admin.regulations.update', $regulation) : route('admin.regulations.store') }}"
          method="POST">
        @csrf
        @if($regulation->exists) @method('PUT') @endif

        <div class="row g-4">

            {{-- Columna principal --}}
            <div class="col-lg-8">

                {{-- Identificación --}}
                <div class="card mb-4">
                    <div class="card-header py-2"><strong>Identificación</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror"
                                   value="{{ old('title', $regulation->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Slug (URL)</label>
                            <input type="text" name="slug" class="form-control form-control-sm @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $regulation->slug) }}" placeholder="se genera automáticamente del título">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Institución responsable <span class="text-danger">*</span></label>
                            <input type="text" name="institution" class="form-control form-control-sm @error('institution') is-invalid @enderror"
                                   value="{{ old('institution', $regulation->institution) }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold">URL fuente oficial</label>
                            <input type="url" name="source_url" class="form-control form-control-sm @error('source_url') is-invalid @enderror"
                                   value="{{ old('source_url', $regulation->source_url) }}" placeholder="https://...">
                        </div>
                    </div>
                </div>

                {{-- Resumen --}}
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <strong>Resumen corto</strong>
                        <small class="text-muted ms-2">Aparece en las cards del listado (2-3 líneas)</small>
                    </div>
                    <div class="card-body">
                        <textarea name="summary" rows="3"
                                  class="form-control form-control-sm @error('summary') is-invalid @enderror"
                                  required>{{ old('summary', $regulation->summary) }}</textarea>
                        @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Análisis normativo --}}
                <div class="card mb-4">
                    <div class="card-header py-2">
                        <strong>Análisis normativo</strong>
                        <small class="text-muted ms-2">¿Qué dice la ley? Acepta HTML</small>
                    </div>
                    <div class="card-body">
                        <textarea name="content" rows="18"
                                  class="form-control form-control-sm font-monospace @error('content') is-invalid @enderror"
                                  style="font-size:.8rem;">{{ old('content', $regulation->content) }}</textarea>
                        <div class="form-text">Use etiquetas HTML: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;</div>
                    </div>
                </div>

                {{-- Impacto laboral --}}
                <div class="card mb-4">
                    <div class="card-header py-2 d-flex align-items-center gap-2">
                        <i class="fas fa-briefcase text-primary"></i>
                        <strong>Impacto laboral</strong>
                        <small class="text-muted ms-1">¿Qué cambia en el trabajo? Acepta HTML</small>
                    </div>
                    <div class="card-body">
                        <textarea name="impact_laboral" rows="10"
                                  class="form-control form-control-sm font-monospace"
                                  style="font-size:.8rem;">{{ old('impact_laboral', $regulation->impact_laboral) }}</textarea>
                        <div class="form-text">Ej: cambios en selección de personal, automatización de empleos, nuevas profesiones, derechos laborales frente a decisiones automatizadas.</div>
                    </div>
                </div>

                {{-- Impacto económico --}}
                <div class="card mb-4">
                    <div class="card-header py-2 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-line text-success"></i>
                        <strong>Impacto económico</strong>
                        <small class="text-muted ms-1">¿Qué cambia en la economía? Acepta HTML</small>
                    </div>
                    <div class="card-body">
                        <textarea name="impact_economico" rows="10"
                                  class="form-control form-control-sm font-monospace"
                                  style="font-size:.8rem;">{{ old('impact_economico', $regulation->impact_economico) }}</textarea>
                        <div class="form-text">Ej: costos de cumplimiento para empresas, impacto en inversión extranjera, nuevas oportunidades de mercado, acceso a crédito.</div>
                    </div>
                </div>

                {{-- Impacto social --}}
                <div class="card mb-4">
                    <div class="card-header py-2 d-flex align-items-center gap-2">
                        <i class="fas fa-users text-warning"></i>
                        <strong>Impacto social</strong>
                        <small class="text-muted ms-1">¿Qué cambia para la sociedad? Acepta HTML</small>
                    </div>
                    <div class="card-body">
                        <textarea name="impact_social" rows="10"
                                  class="form-control form-control-sm font-monospace"
                                  style="font-size:.8rem;">{{ old('impact_social', $regulation->impact_social) }}</textarea>
                        <div class="form-text">Ej: protección de datos personales, sesgos algorítmicos, brechas digitales, derechos ciudadanos frente a sistemas de IA.</div>
                    </div>
                </div>

            </div>

            {{-- Columna lateral --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top:70px;">

                    {{-- Clasificación --}}
                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Clasificación</strong></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Ámbito <span class="text-danger">*</span></label>
                                <select name="scope" class="form-select form-select-sm" required>
                                    <option value="chile"         {{ old('scope', $regulation->scope) === 'chile'         ? 'selected' : '' }}>Chile</option>
                                    <option value="internacional" {{ old('scope', $regulation->scope) === 'internacional' ? 'selected' : '' }}>Internacional</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold">Estado <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="en_tramitacion" {{ old('status', $regulation->status) === 'en_tramitacion' ? 'selected' : '' }}>En tramitación</option>
                                    <option value="aprobada"       {{ old('status', $regulation->status) === 'aprobada'       ? 'selected' : '' }}>Aprobada</option>
                                    <option value="vigente"        {{ old('status', $regulation->status) === 'vigente'        ? 'selected' : '' }}>Vigente</option>
                                    <option value="rechazada"      {{ old('status', $regulation->status) === 'rechazada'      ? 'selected' : '' }}>Rechazada</option>
                                    <option value="propuesta"      {{ old('status', $regulation->status) === 'propuesta'      ? 'selected' : '' }}>Propuesta</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="card mb-3">
                        <div class="card-header py-2"><strong>Fechas</strong></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Fecha de ingreso/publicación</label>
                                <input type="date" name="date_introduced" class="form-control form-control-sm"
                                       value="{{ old('date_introduced', $regulation->date_introduced?->format('Y-m-d')) }}">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-semibold">Última actualización oficial</label>
                                <input type="date" name="date_updated" class="form-control form-control-sm"
                                       value="{{ old('date_updated', $regulation->date_updated?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save me-1"></i>
                                {{ $regulation->exists ? 'Guardar cambios' : 'Crear regulación' }}
                            </button>
                            <a href="{{ route('admin.regulations.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                                Cancelar
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@endsection
