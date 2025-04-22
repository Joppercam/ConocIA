@extends('admin.layouts.app')

@section('title', 'Editar Video')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Editar Video</h1>
        <div>
            <a href="{{ route('videos.show', $video->id) }}" class="btn btn-sm btn-info" target="_blank">
                <i class="fas fa-eye"></i> Ver en sitio
            </a>
            <a href="{{ route('admin.videos.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.videos.update', $video->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="title">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $video->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $video->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="categories">Categorías</label>
                            <select name="categories[]" id="categories" class="form-control select2" multiple>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $videoCategories)) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tags">Etiquetas</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $videoTags) }}" placeholder="Separa las etiquetas con comas">
                            <small class="form-text text-muted">
                                Introduce etiquetas separadas por comas (ej: política, economía, entrevista).
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <input type="hidden" name="is_featured" value="0">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $video->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_featured">Marcar como destacado</label>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Video</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="img-fluid rounded">
                    </div>
                    
                    <div class="video-details">
                        <p><strong>Plataforma:</strong> {{ $video->platform->name }}</p>
                        <p><strong>ID Externo:</strong> {{ $video->external_id }}</p>
                        <p><strong>Publicado:</strong> {{ $video->published_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Duración:</strong> {{ $video->duration }}</p>
                        <p><strong>Vistas:</strong> {{ number_format($video->view_count) }}</p>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <a href="{{ $video->original_url }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Ver en {{ $video->platform->name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Selecciona las categorías",
            allowClear: true
        });
    });
</script>
@endpush
