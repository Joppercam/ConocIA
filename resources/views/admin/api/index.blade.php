@extends('admin.layouts.app')

@section('title', 'Ejecutar API de Noticias')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Ejecutar API de Noticias</h5>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Esta herramienta te permite ejecutar manualmente la obtención de noticias de IA.
                        <div class="mt-2 small">
                            <strong>Nota:</strong> El proceso se ejecutará en segundo plano y puede tardar varios minutos. Las imágenes se descargarán de forma asíncrona.
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.api.execute') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Categoría</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Selecciona una categoría</option>
                                
                                @if(isset($availableCategories) && !empty($availableCategories))
                                    @foreach($availableCategories as $groupName => $groupCategories)
                                        <optgroup label="{{ $groupName }}">
                                            @foreach($groupCategories as $category)
                                                <option value="{{ $category['slug'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @else
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="count" class="form-label">Cantidad de noticias</label>
                            <input type="number" class="form-control @error('count') is-invalid @enderror" 
                                   id="count" name="count" value="{{ old('count', 5) }}" min="1" max="10" required>
                            @error('count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Número de noticias a buscar (entre 1 y 10)
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="language" class="form-label">Idioma</label>
                            <select class="form-select @error('language') is-invalid @enderror" id="language" name="language" required>
                                <option value="es" {{ old('language') == 'es' ? 'selected' : '' }}>Español</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>Inglés</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-2"></i> Ejecutar API de Noticias
                            </button>
                            
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Volver al Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Programación automática</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary">
                        <i class="fas fa-clock me-2"></i> <strong>Ejecución automática:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Se ejecuta 2 veces al día (8:00 AM y 8:00 PM).</li>
                            <li>Busca 2 noticias por cada categoría.</li>
                            <li>El log de ejecución se guarda en: <code>storage/logs/fetch-all-news.log</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection