@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>Crear Nueva Categoría</h1>
        
        <div class="mb-4">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                Volver al listado
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="slug">Slug (URL amigable)</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug') }}">
                        <small class="form-text text-muted">Dejar en blanco para generar automáticamente</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="search_terms">Términos de búsqueda</label>
                        <textarea class="form-control @error('search_terms') is-invalid @enderror" 
                                id="search_terms" name="search_terms" rows="3" placeholder="Términos separados por OR (ejemplo: IA OR inteligencia artificial OR machine learning)">{{ old('search_terms', $category->search_terms ?? '') }}</textarea>
                        <small class="form-text text-muted">Define los términos para buscar noticias. Usa OR entre términos para búsquedas alternativas.</small>
                        @error('search_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="color">Color</label>
                        <input type="color" class="form-control @error('color') is-invalid @enderror" 
                               id="color" name="color" value="{{ old('color', '#000000') }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Categoría activa
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Crear categoría</button>
                </form>
            </div>
        </div>
    </div>
@endsection