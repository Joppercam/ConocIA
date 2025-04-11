@extends('admin.layouts.app')

@section('title', 'Crear Guión TikTok')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Crear Guión TikTok</h1>
        <div>
            <a href="{{ route('admin.tiktok.generate', $article->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-magic"></i> Generar Automáticamente
            </a>
            <a href="{{ route('admin.tiktok.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <!-- Información del artículo original -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Artículo Original</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $article->title }}</h5>
                    <p class="text-muted">
                        <strong>Categoría:</strong> {{ $article->category->name ?? 'N/A' }}<br>
                        <strong>Fecha:</strong> {{ $article->created_at->format('d/m/Y H:i') }}<br>
                        <strong>Autor:</strong> {{ $article->author ?? 'N/A' }}
                    </p>
                    
                    <hr>
                    
                    <div class="article-content">
                        {{ Str::limit($article->content, 500) }}
                        
                        @if(strlen($article->content) > 500)
                        <button class="btn btn-sm btn-link" type="button" data-toggle="modal" data-target="#fullArticleModal">
                            Ver artículo completo
                        </button>
                        
                        <!-- Modal para ver el artículo completo -->
                        <div class="modal fade" id="fullArticleModal" tabindex="-1" role="dialog" aria-labelledby="fullArticleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="fullArticleModalLabel">{{ $article->title }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {!! nl2br(e($article->content)) !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    @if($article->url)
                    <a href="{{ $article->url }}" target="_blank" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-external-link-alt"></i> Ver Artículo en el Portal
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Sugerencias y consejos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Consejos para Guiones TikTok</h6>
                </div>
                <div class="card-body">
                    <ul class="text-muted">
                        <li>Mantén el guión entre 100-150 palabras (30-60 segundos).</li>
                        <li>Comienza con un gancho impactante para captar la atención.</li>
                        <li>Incluye solo 2-3 puntos clave del artículo.</li>
                        <li>Usa un lenguaje conversacional, adecuado para audiencia joven.</li>
                        <li>Termina con un llamado a la acción, preferiblemente dirigiendo al portal.</li>
                        <li>Añade hashtags relevantes para maximizar alcance.</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Formulario de creación del guión -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Guión para TikTok</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tiktok.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="article_id" value="{{ $article->id }}">
                        
                        <!-- Contenido del guión -->
                        <div class="form-group">
                            <label for="script_content"><strong>Guión:</strong></label>
                            <textarea class="form-control @error('script_content') is-invalid @enderror" id="script_content" name="script_content" rows="8" required>{{ old('script_content') }}</textarea>
                            <small class="form-text text-muted">
                                Este guión debe tener una duración aproximada de 30-60 segundos (100-150 palabras).
                            </small>
                            @error('script_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Sugerencias visuales -->
                        <div class="form-group">
                            <label for="visual_suggestions"><strong>Sugerencias Visuales:</strong></label>
                            <textarea class="form-control @error('visual_suggestions') is-invalid @enderror" id="visual_suggestions" name="visual_suggestions" rows="4">{{ old('visual_suggestions') }}</textarea>
                            <small class="form-text text-muted">
                                Elementos visuales recomendados para acompañar el guión.
                            </small>
                            @error('visual_suggestions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Hashtags -->
                        <div class="form-group">
                            <label for="hashtags"><strong>Hashtags:</strong></label>
                            <input type="text" class="form-control @error('hashtags') is-invalid @enderror" id="hashtags" name="hashtags" value="{{ old('hashtags') }}">
                            <small class="form-text text-muted">
                                Hashtags recomendados para maximizar el alcance.
                            </small>
                            @error('hashtags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Estado inicial -->
                        <div class="form-group">
                            <label for="status"><strong>Estado:</strong></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Borrador</option>
                                <option value="pending_review" {{ old('status') == 'pending_review' ? 'selected' : '' }}>Pendiente de Revisión</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="form-group d-flex justify-content-between">
                            <button type="submit" name="save" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar como Borrador
                            </button>
                            
                            <button type="submit" name="submit" value="pending_review" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Enviar a Revisión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Contador de palabras para el guión
    $(document).ready(function() {
        function countWords(text) {
            return text.split(/\s+/).filter(function(word) { return word.length > 0; }).length;
        }
        
        function updateWordCount() {
            var text = $('#script_content').val();
            var wordCount = countWords(text);
            var status = '';
            
            if (wordCount < 80) {
                status = '<span class="text-danger">Muy corto</span>';
            } else if (wordCount > 150) {
                status = '<span class="text-danger">Muy largo</span>';
            } else {
                status = '<span class="text-success">Óptimo</span>';
            }
            
            $('#wordCounter').html('Palabras: ' + wordCount + ' - ' + status);
        }
        
        // Agregamos el contador debajo del textarea
        $('#script_content').after('<small id="wordCounter" class="form-text text-muted"></small>');
        
        // Actualizamos al cargar la página
        updateWordCount();
        
        // Actualizamos cuando cambia el contenido
        $('#script_content').on('input', updateWordCount);
        
        // Si se presiona el botón de "Enviar a Revisión", cambiar el estado
        $('button[name="submit"]').click(function() {
            $('#status').val('pending_review');
        });
    });
</script>
@endsection