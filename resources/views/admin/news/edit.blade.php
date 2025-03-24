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
                            @error('summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Contenido -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido <span class="text-danger">*</span></label>
                            <textarea class="form-control editor @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content', $news->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Estado -->
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
                                @if($news->featured_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $news->featured_image) }}" alt="{{ $news->title }}" class="img-fluid" style="max-height: 200px;">
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
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/y7gn7np7foqprg37706om9j9ca8f9ulxd80quxbadv6a3gc8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
    
    // Inicializar TinyMCE para el editor de contenido
    tinymce.init({
        selector: '.editor',
        plugins: 'autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        height: 500,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
    
    // Generar slug automáticamente si el campo está vacío
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    // Solo actualizar el slug si está vacío o si no se ha tocado aún
    let slugEdited = false;
    
    slugInput.addEventListener('input', function() {
        slugEdited = true;
    });
    
    titleInput.addEventListener('keyup', function() {
        if (!slugEdited) {
            const title = this.value;
            const slug = title.toLowerCase()
                             .replace(/[^\w ]+/g, '')
                             .replace(/ +/g, '-');
            slugInput.value = slug;
        }
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