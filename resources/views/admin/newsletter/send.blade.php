@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Enviar Newsletter</h5>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Hay {{ $subscribersCount }} suscriptores activos que recibirán este newsletter.
                    </div>
                    
                    <form action="{{ route('admin.newsletter.send.post') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Asunto del correo</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject', 'Últimas noticias de ConocIA') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Sección de Noticias -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-newspaper me-2"></i>Noticias</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="news_count" class="form-label">Número de noticias a incluir</label>
                                    <select class="form-select @error('news_count') is-invalid @enderror" 
                                            id="news_count" name="news_count" required>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('news_count') == $i || (!old('news_count') && $i == 5) ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'noticia' : 'noticias' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('news_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Se enviarán las noticias más recientes según la cantidad seleccionada.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Investigaciones -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-flask me-2"></i>Investigaciones</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="include_research" name="include_research" value="1" {{ old('include_research') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_research">Incluir</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="research_count" class="form-label">Número de investigaciones a incluir</label>
                                    <select class="form-select @error('research_count') is-invalid @enderror" 
                                            id="research_count" name="research_count">
                                        @for ($i = 0; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('research_count') == $i || (!old('research_count') && $i == 2) ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'investigación' : 'investigaciones' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('research_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Columnas -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-pen-fancy me-2"></i>Columnas</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="include_columns" name="include_columns" value="1" {{ old('include_columns') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_columns">Incluir</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="columns_count" class="form-label">Número de columnas a incluir</label>
                                    <select class="form-select @error('columns_count') is-invalid @enderror" 
                                            id="columns_count" name="columns_count">
                                        @for ($i = 0; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('columns_count') == $i || (!old('columns_count') && $i == 2) ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'columna' : 'columnas' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('columns_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Newsletter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de la habilitación/deshabilitación de los selectores
        const researchCheckbox = document.getElementById('include_research');
        const researchCount = document.getElementById('research_count');
        
        const columnsCheckbox = document.getElementById('include_columns');
        const columnsCount = document.getElementById('columns_count');
        
        function updateFieldState() {
            researchCount.disabled = !researchCheckbox.checked;
            columnsCount.disabled = !columnsCheckbox.checked;
        }
        
        researchCheckbox.addEventListener('change', updateFieldState);
        columnsCheckbox.addEventListener('change', updateFieldState);
        
        // Inicializar estados
        updateFieldState();
    });
</script>
@endpush

@endsection