@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Editar Columna</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.columns.update', $column->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $column->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug (opcional)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $column->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Déjalo en blanco para generarlo automáticamente basado en el título.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Extracto</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $column->excerpt) }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Contenido</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="15" required>{{ old('content', $column->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="author_id" class="form-label">Autor</label>
                                    <select class="form-select @error('author_id') is-invalid @enderror" 
                                            id="author_id" name="author_id" required>
                                        <option value="">Seleccionar autor</option>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" {{ old('author_id', $column->author_id) == $author->id ? 'selected' : '' }}>
                                                {{ $author->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('author_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoría</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Sin categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $column->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="published_at" class="form-label">Fecha de publicación</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                           id="published_at" name="published_at" value="{{ old('published_at', $column->published_at ? $column->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured', $column->featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">Destacar columna</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.columns.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar columna</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Audio --}}
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-headphones me-2 text-primary"></i>Audio de la columna</h5>
                    @if($column->audio_generated_at)
                    <span class="badge bg-success">Generado {{ $column->audio_generated_at->diffForHumans() }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($column->audio_path)
                        <div class="mb-3">
                            <audio controls class="w-100">
                                <source src="{{ $column->audio_path }}" type="audio/mpeg">
                            </audio>
                        </div>
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.columns.generate-audio', $column) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i>Regenerar audio
                                </button>
                            </form>
                            <form action="{{ route('admin.columns.delete-audio', $column) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar el audio de esta columna?')">
                                    <i class="fas fa-trash me-1"></i>Eliminar audio
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="text-muted mb-3">Esta columna todavía no tiene audio generado. Usá el botón para generarlo con OpenAI TTS (voz <em>nova</em>).</p>
                        <form action="{{ route('admin.columns.generate-audio', $column) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-microphone me-1"></i>Generar audio
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script para generar el slug a partir del título
    document.getElementById('title').addEventListener('blur', function() {
        const title = this.value;
        const slugField = document.getElementById('slug');
        
        if (slugField.value === '') {
            const slug = title.toLowerCase()
                              .replace(/[^a-z0-9]+/g, '-')
                              .replace(/(^-|-$)/g, '');
            slugField.value = slug;
        }
    });
    
    // Aquí podrías inicializar un editor WYSIWYG para el campo content
    // Por ejemplo, TinyMCE, CKEditor, etc.
</script>
@endpush