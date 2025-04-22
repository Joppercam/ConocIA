@extends('admin.layouts.app')

@section('title', 'Nueva Plataforma de Videos')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nueva Plataforma de Videos</h1>
        <a href="{{ route('admin.videos.platforms.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.videos.platforms.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="code">Código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                    <small class="form-text text-muted">
                        Código único para identificar la plataforma (ej: youtube, vimeo).
                    </small>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="embed_pattern">Patrón de Embed <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('embed_pattern') is-invalid @enderror" id="embed_pattern" name="embed_pattern" value="{{ old('embed_pattern') }}" required>
                    <small class="form-text text-muted">
                        Patrón de URL para incrustar videos (ej: https://www.youtube.com/embed/{id}).
                    </small>
                    @error('embed_pattern')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="api_key">API Key</label>
                    <input type="text" class="form-control @error('api_key') is-invalid @enderror" id="api_key" name="api_key" value="{{ old('api_key') }}">
                    @error('api_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="api_secret">API Secret</label>
                    <input type="password" class="form-control @error('api_secret') is-invalid @enderror" id="api_secret" name="api_secret">
                    @error('api_secret')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Plataforma activa</label>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Plataforma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection