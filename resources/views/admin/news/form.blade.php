<!-- resources/views/admin/news/form.blade.php -->
@extends('admin.layouts.app')

@section('title', $news->exists ? 'Editar Noticia' : 'Crear Noticia')

@section('page_title', $news->exists ? 'Editar Noticia' : 'Crear Noticia')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<form action="{{ $news->exists ? route('admin.news.update', $news->id) : route('admin.news.store') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    @if($news->exists)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Principal</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $news->title) }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Amigable (Slug)</label>
                        <input type="text" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug', $news->slug) }}"
                               placeholder="Dejar en blanco para generar automáticamente">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Extracto</label>
                        <textarea 
                            class="form-control @error('excerpt') is-invalid @enderror" 
                            id="excerpt" 
                            name="excerpt" 
                            rows="3"
                            required>{{ old('excerpt', $news->excerpt) }}</textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <textarea 
                            class="form-control @error('content') is-invalid @enderror" 
                            id="content" 
                            name="content" 
                            rows="10"
                            required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SEO y Metadatos</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Descripción</label>
                        <textarea 
                            class="form-control @error('meta_description') is-invalid @enderror" 
                            id="meta_description" 
                            name="meta_description" 
                            rows="3">{{ old('meta_description', $news->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" 
                               class="form-control @error('meta_keywords') is-invalid @enderror" 
                               id="meta_keywords" 
                               name="meta_keywords" 
                               value="{{ old('meta_keywords', $news->meta_keywords) }}"
                               placeholder="Separar keywords con comas">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Configuraciones</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Categoría</label>
                        <select 
                            class="form-select @error('category_id') is-invalid @enderror" 
                            id="category_id" 
                            name="category_id"
                            required>
                            <option value="">Seleccionar Categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ (old('category_id', $news->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen Principal</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($news->image)
                            <div class="mt-2">
                                <img src="{{ $getImageUrl($news->image, 'news', 'medium') }}" 
                                     class="img-fluid rounded" 
                                     alt="Imagen actual"
                                     onerror="this.onerror=null; this.src='{{ asset('storage/images/defaults/news-default-medium.jpg') }}';">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">
                                        Eliminar imagen actual
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Etiquetas</label>
                        <select 
                            class="form-control" 
                            id="tags" 
                            name="tags[]" 
                            multiple="multiple">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" 
                                    {{ in_array($tag->id, old('tags', $news->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   {{ old('is_featured', $news->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Destacar Noticia
                            </label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                                   {{ old('is_published', $news->is_published ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publicar Inmediatamente
                            </label>
                        </div>
                    </div>

                    @if($news->exists)
                    <div class="mb-3">
                        <label class="form-label">Estadísticas</label>
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">Vistas:</small>
                                <strong>{{ $news->views }}</strong>
                            </div>
                            <div>
                                <small class="text-muted">Comentarios:</small>
                                <strong>{{ $news->comments_count }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 text-end">
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> 
            {{ $news->exists ? 'Actualizar Noticia' : 'Crear Noticia' }}
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/y7gn7np7foqprg37706om9j9ca8f9ulxd80quxbadv6a3gc8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        // Inicializar Select2 para tags
        $('#tags').select2({
            placeholder: 'Seleccionar etiquetas',
            tags: true,
            tokenSeparators: [',', ' ']
        });

        // Generar slug automáticamente
        $('#title').on('input', function() {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');
            $('#slug').val(slug);
        });

        // Inicializar TinyMCE para el editor de contenido
        tinymce.init({
            selector: '#content',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    });
</script>
@endsection