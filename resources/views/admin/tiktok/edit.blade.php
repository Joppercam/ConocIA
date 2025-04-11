@extends('admin.layouts.app')

@section('title', 'Editar Guión de TikTok')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Editar Guión de TikTok
                        <span class="badge badge-{{ $script->status === 'approved' ? 'success' : ($script->status === 'pending_review' ? 'warning' : ($script->status === 'published' ? 'info' : 'secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $script->status)) }}
                        </span>
                        <small class="text-muted ml-2">Score: {{ number_format($script->tiktok_score, 1) }}/100</small>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tiktok.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @include('admin.partials.alerts')

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Formulario de edición -->
                            <form action="{{ route('admin.tiktok.update', $script->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="script_content">Contenido del Guión</label>
                                    <textarea name="script_content" id="script_content" class="form-control @error('script_content') is-invalid @enderror" rows="10" required>{{ old('script_content', $script->script_content) }}</textarea>
                                    @error('script_content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="visual_suggestions">Sugerencias Visuales</label>
                                    <textarea name="visual_suggestions" id="visual_suggestions" class="form-control @error('visual_suggestions') is-invalid @enderror" rows="5">{{ old('visual_suggestions', $script->visual_suggestions) }}</textarea>
                                    @error('visual_suggestions')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="hashtags">Hashtags</label>
                                    <input type="text" name="hashtags" id="hashtags" class="form-control @error('hashtags') is-invalid @enderror" value="{{ old('hashtags', $script->hashtags) }}">
                                    @error('hashtags')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group d-none">
                                    <input type="hidden" name="status" value="{{ $script->status }}">
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Cambios
                                    </button>
                                </div>
                            </form>

                            <hr>

                            <!-- Opciones de cambio de estado -->
                            <div class="d-flex justify-content-between">
                                @if($script->status === 'draft')
                                    <form action="{{ route('admin.tiktok.update-status', $script->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="pending_review">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-paper-plane"></i> Enviar a Revisión
                                        </button>
                                    </form>
                                @endif

                                @if($script->status === 'pending_review')
                                    <div>
                                        <form action="{{ route('admin.tiktok.update-status', $script->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.tiktok.update-status', $script->id) }}" method="POST" class="d-inline ml-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if($script->status === 'approved')
                                    <form action="{{ route('admin.tiktok.update-status', $script->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="published">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-upload"></i> Marcar como Publicado
                                        </button>
                                    </form>
                                @endif
                            </div>

                            @if($script->status === 'published')
                                <hr>
                                <!-- Métricas para scripts publicados -->
                                <h4>Registrar Métricas</h4>
                                <form action="{{ route('admin.tiktok.record-metrics', $script->id) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tiktok_video_id">ID del Video en TikTok</label>
                                                <input type="text" name="tiktok_video_id" id="tiktok_video_id" class="form-control @error('tiktok_video_id') is-invalid @enderror" value="{{ $script->metrics->tiktok_video_id ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="views">Vistas</label>
                                                <input type="number" name="views" id="views" class="form-control @error('views') is-invalid @enderror" value="{{ $script->metrics->views ?? 0 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="likes">Likes</label>
                                                <input type="number" name="likes" id="likes" class="form-control @error('likes') is-invalid @enderror" value="{{ $script->metrics->likes ?? 0 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="comments">Comentarios</label>
                                                <input type="number" name="comments" id="comments" class="form-control @error('comments') is-invalid @enderror" value="{{ $script->metrics->comments ?? 0 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="shares">Compartidos</label>
                                                <input type="number" name="shares" id="shares" class="form-control @error('shares') is-invalid @enderror" value="{{ $script->metrics->shares ?? 0 }}" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="clicks_to_portal">Clics al Portal</label>
                                                <input type="number" name="clicks_to_portal" id="clicks_to_portal" class="form-control @error('clicks_to_portal') is-invalid @enderror" value="{{ $script->metrics->clicks_to_portal ?? 0 }}" min="0" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-chart-line"></i> Actualizar Métricas
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Información de la noticia -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Información de la Noticia</h5>
                                </div>
                                <div class="card-body">
                                    <h5>{{ $script->news->title ?? 'Sin título' }}</h5>
                                    
                                    @if($script->news)
                                        <p class="text-muted">
                                            <strong>Categoría:</strong> {{ $script->news->category->name ?? 'Sin categoría' }}<br>
                                            <strong>Publicado:</strong> {{ $script->news->published_at ? $script->news->published_at->format('d/m/Y H:i') : 'No publicado' }}<br>
                                            <strong>Vistas:</strong> {{ number_format($script->news->views ?? 0) }}
                                        </p>
                                        
                                        <div class="mb-3">
                                            <a href="{{ route('admin.news.edit', $script->news->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                <i class="fas fa-external-link-alt"></i> Ver Noticia
                                            </a>
                                        </div>
                                        
                                        <h6>Extracto:</h6>
                                        <div class="border p-2 bg-light mb-3" style="max-height: 200px; overflow-y: auto;">
                                            {{ Str::limit(strip_tags($script->news->content), 300) }}
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Noticia no encontrada o eliminada
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Estadísticas del guión (si está publicado) -->
                            @if($script->status === 'published' && isset($script->metrics))
                                <div class="card mt-3">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="card-title mb-0">Estadísticas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Vistas:</span>
                                            <strong>{{ number_format($script->metrics->views ?? 0) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Likes:</span>
                                            <strong>{{ number_format($script->metrics->likes ?? 0) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Comentarios:</span>
                                            <strong>{{ number_format($script->metrics->comments ?? 0) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Compartidos:</span>
                                            <strong>{{ number_format($script->metrics->shares ?? 0) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Clics al Portal:</span>
                                            <strong>{{ number_format($script->metrics->clicks_to_portal ?? 0) }}</strong>
                                        </div>
                                        
                                        @if(isset($script->metrics->views) && $script->metrics->views > 0)
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Ratio Engagement:</span>
                                                <strong>{{ number_format((($script->metrics->likes + $script->metrics->comments + $script->metrics->shares) / $script->metrics->views) * 100, 2) }}%</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>CTR al Portal:</span>
                                                <strong>{{ number_format(($script->metrics->clicks_to_portal / $script->metrics->views) * 100, 2) }}%</strong>
                                            </div>
                                        @endif
                                        
                                        @if($script->metrics->tiktok_video_id)
                                            <a href="https://www.tiktok.com/@tiktok/video/{{ $script->metrics->tiktok_video_id }}" class="btn btn-sm btn-outline-dark btn-block mt-2" target="_blank">
                                                <i class="fab fa-tiktok"></i> Ver en TikTok
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Información del sistema -->
                            <div class="card mt-3">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">Información del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>ID:</span>
                                        <strong>{{ $script->id }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Creado:</span>
                                        <strong>{{ $script->created_at->format('d/m/Y H:i') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Actualizado:</span>
                                        <strong>{{ $script->updated_at->format('d/m/Y H:i') }}</strong>
                                    </div>
                                    @if($script->published_at)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Publicado:</span>
                                            <strong>{{ $script->published_at->format('d/m/Y H:i') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Activar el editor para el contenido del script si tienes algún plugin de editor
        // Por ejemplo, si usas CKEditor:
        /*
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('script_content', {
                height: 300,
                toolbar: 'Basic'
            });
        }
        */
    });
</script>
@endpush