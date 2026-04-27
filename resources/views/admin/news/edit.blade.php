@extends('admin.layouts.app')
@section('title', 'Noticias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Editar Noticia</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Noticias</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Noticia</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $news->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $news->slug) }}">
                            <div class="form-text">Dejar en blanco para generar automáticamente.</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" {{ $news->featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Destacar en página principal</label>
                                <div class="form-text">Las noticias destacadas aparecerán en el carrusel principal del inicio.</div>
                            </div>
                        </div>

                        
                        <!-- Resumen -->
                        <div class="mb-3">
                            <label for="summary" class="form-label">Resumen <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary" rows="3" required>{{ old('summary', $news->summary) }}</textarea>
                            <div class="form-text"><span id="summary-count">{{ strlen(old('summary', $news->summary ?? '')) }}</span>/155 caracteres sugeridos para snippet.</div>
                            @error('summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Contenido -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido <span class="text-danger">*</span></label>
                            <textarea class="form-control news-content-textarea @error('content') is-invalid @enderror" id="content" name="content" rows="18" required placeholder="<p>Escribe o pega aquí el contenido de la noticia...</p>">{{ old('content', $news->content) }}</textarea>
                            <div class="form-text">Puedes editar como texto normal o pegar HTML limpio con etiquetas como `<p>`, `<h2>` y `<ul>`.</div>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        @include('admin.news.partials.seo-assistant', ['news' => $news])

                        <div class="card mb-3">
                            <div class="card-header">Estado</div>
                            <div class="card-body">
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="draft" {{ old('status', $news->status) == 'draft' ? 'selected' : '' }}>Borrador</option>
                                    <option value="published" {{ old('status', $news->status) == 'published' ? 'selected' : '' }}>Publicado</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="mt-3">
                                    <label for="published_at" class="form-label">Fecha de publicación</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">Monetización</div>
                            <div class="card-body">
                                <label for="access_level" class="form-label">Acceso</label>
                                <select class="form-control @error('access_level') is-invalid @enderror" id="access_level" name="access_level">
                                    <option value="free" {{ old('access_level', $news->access_level ?? 'free') === 'free' ? 'selected' : '' }}>FREE</option>
                                    <option value="premium" {{ old('access_level', $news->access_level ?? 'free') === 'premium' ? 'selected' : '' }}>PREMIUM</option>
                                </select>
                                @error('access_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1" {{ old('is_premium', $news->is_premium) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_premium">Bloquear como contenido premium</label>
                                    <div class="form-text">El contenido premium queda disponible para usuarios PRO y BUSINESS.</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Categoría -->
                        <div class="card mb-3">
                            <div class="card-header">Categoría</div>
                            <div class="card-body">
                                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $news->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Etiquetas -->
                        <div class="card mb-3">
                            <div class="card-header">Etiquetas</div>
                            <div class="card-body">
                                <select class="form-control tags-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ (old('tags') && in_array($tag->id, old('tags'))) || (isset($news) && $news->tags && $news->tags->contains($tag->id)) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Imagen destacada -->
                        <div class="card mb-3">
                            <div class="card-header">Imagen destacada</div>
                            <div class="card-body">
                                @if($news->image)
                                    <div class="mb-2">
                                        <img src="{{ str_starts_with($news->image, 'http') ? $news->image : asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="img-fluid" style="max-height: 200px;">
                                    </div>
                                @endif
                                
                                <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
                                <div class="form-text">Recomendado: 1200 x 630 píxeles. Dejar en blanco para mantener la imagen actual.</div>
                                @error('featured_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2">
                                    <div id="image-preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar noticia</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .news-content-textarea {
        min-height: 520px;
        font-family: Georgia, "Times New Roman", serif;
        line-height: 1.65;
        resize: vertical;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Inicializar Select2 para las etiquetas
    $(document).ready(function() {
        $('.tags-select').select2({
            placeholder: 'Seleccionar etiquetas',
            allowClear: true,
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });
    
    // Vista previa de imagen
    document.getElementById('featured_image').addEventListener('change', function() {
        const file = this.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" class="img-fluid mt-2" style="max-height: 200px;">`;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
