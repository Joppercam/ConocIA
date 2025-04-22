@extends('admin.layouts.app')

@section('title', 'Agregar Video')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Agregar Video</h1>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.videos.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="platform_id">Plataforma <span class="text-danger">*</span></label>
                            <select name="platform_id" id="platform_id" class="form-control @error('platform_id') is-invalid @enderror" required>
                                <option value="">Selecciona una plataforma</option>
                                @foreach($platforms as $platform)
                                <option value="{{ $platform->id }}" {{ old('platform_id') == $platform->id ? 'selected' : '' }}>
                                    {{ $platform->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('platform_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="external_id">ID del Video <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('external_id') is-invalid @enderror" id="external_id" name="external_id" value="{{ old('external_id') }}" required>
                            <small class="form-text text-muted">
                                Ejemplo: Para YouTube es el código después de v= en la URL (dQw4w9WgXcQ).
                            </small>
                            @error('external_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="categories">Categorías</label>
                    <select name="categories[]" id="categories" class="form-control select2" multiple>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tags">Etiquetas</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}" placeholder="Separa las etiquetas con comas">
                    <small class="form-text text-muted">
                        Introduce etiquetas separadas por comas (ej: política, economía, entrevista).
                    </small>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Video
                    </button>
                </div>
            </form>
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