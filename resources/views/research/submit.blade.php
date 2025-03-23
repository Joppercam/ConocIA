@extends('layouts.app')

@section('title', 'Enviar Investigación - ConocIA')
@section('meta_description', 'Comparte tu investigación sobre inteligencia artificial y tecnología con nuestra comunidad de expertos y entusiastas.')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #ced4da;
        border-radius: 0.375rem;
    }
    .required-label::after {
        content: " *";
        color: red;
    }
    .tox-tinymce {
        border-radius: 0.375rem !important;
    }
    .form-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-section h3 {
        color: var(--primary-color);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }
    .upload-preview {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin-top: 0.5rem;
    }
    .research-tips {
        background-color: rgba(56, 182, 255, 0.1);
        border-left: 4px solid var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Enviar Investigación</h1>
            
            <div class="alert alert-info research-tips mb-4">
                <h5><i class="fas fa-lightbulb me-2"></i>Consejos para tu envío</h5>
                <ul class="mb-0">
                    <li>Asegúrate de que tu investigación sea original y relevante para nuestra comunidad.</li>
                    <li>Incluye un resumen claro y conciso que destaque tus hallazgos principales.</li>
                    <li>Proporciona información de contacto actualizada para posibles colaboraciones.</li>
                    <li>Todas las investigaciones pasan por un proceso de revisión antes de ser publicadas.</li>
                </ul>
            </div>
            
            <form action="{{ route('research.store') }}" method="POST" enctype="multipart/form-data" id="research-form">
                @csrf
                
                <!-- Información básica -->
                <div class="form-section">
                    <h3><i class="fas fa-info-circle me-2"></i>Información Básica</h3>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label required-label">Título de la investigación</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="research_type" class="form-label required-label">Tipo de investigación</label>
                        <select class="form-select @error('research_type') is-invalid @enderror" id="research_type" name="research_type" required>
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <option value="paper" {{ old('research_type') == 'paper' ? 'selected' : '' }}>Paper académico</option>
                            <option value="case_study" {{ old('research_type') == 'case_study' ? 'selected' : '' }}>Estudio de caso</option>
                            <option value="analysis" {{ old('research_type') == 'analysis' ? 'selected' : '' }}>Análisis</option>
                            <option value="review" {{ old('research_type') == 'review' ? 'selected' : '' }}>Revisión/Survey</option>
                        </select>
                        @error('research_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label required-label">Categoría</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Selecciona una categoría...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Etiquetas</label>
                        <select class="select2 form-control @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'selected' : '' }}>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Selecciona las etiquetas que mejor describan tu investigación.</div>
                        @error('tags')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Contenido de la investigación -->
                <div class="form-section">
                    <h3><i class="fas fa-file-alt me-2"></i>Contenido</h3>
                    
                    <div class="mb-3">
                        <label for="abstract" class="form-label required-label">Resumen / Abstract</label>
                        <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract" name="abstract" rows="4" required>{{ old('abstract') }}</textarea>
                        <div class="form-text">Un resumen conciso (100-250 palabras) que presente el objetivo, metodología y hallazgos principales.</div>
                        @error('abstract')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label required-label">Contenido completo</label>
                        <textarea class="form-control tinymce @error('content') is-invalid @enderror" id="content" name="content" rows="15">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="references" class="form-label">Referencias bibliográficas</label>
                        <textarea class="form-control @error('references') is-invalid @enderror" id="references" name="references" rows="4">{{ old('references') }}</textarea>
                        @error('references')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Archivos y multimedia -->
                <div class="form-section">
                    <h3><i class="fas fa-image me-2"></i>Archivos y Multimedia</h3>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen destacada</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        <div class="form-text">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB.</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="image-preview" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document" class="form-label">Documento completo (opcional)</label>
                        <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.doc,.docx">
                        <div class="form-text">Formatos aceptados: PDF, DOC, DOCX. Tamaño máximo: 10MB.</div>
                        @error('document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="form-section">
                    <h3><i class="fas fa-users me-2"></i>Información Adicional</h3>
                    
                    <div class="mb-3">
                        <label for="additional_authors" class="form-label">Autores adicionales</label>
                        <input type="text" class="form-control @error('additional_authors') is-invalid @enderror" id="additional_authors" name="additional_authors" value="{{ old('additional_authors') }}">
                        <div class="form-text">Nombres de otros autores separados por comas (si aplica).</div>
                        @error('additional_authors')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="institution" class="form-label">Institución / Afiliación</label>
                        <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}">
                        @error('institution')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="alert alert-warning mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms_acceptance" required>
                        <label class="form-check-label" for="terms_acceptance">
                            Confirmo que este contenido es original o tengo los derechos para publicarlo. Acepto los <a href="{{ route('pages.terms') }}" target="_blank">términos y condiciones</a> del sitio.
                        </label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="button" class="btn btn-outline-secondary me-md-2" onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn"><i class="fas fa-paper-plane me-2"></i>Enviar investigación</button>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Proceso de revisión</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-upload"></i></span>
                        </div>
                        <div>
                            <h6>1. Envío</h6>
                            <p class="small text-muted mb-0">Completa el formulario con toda la información necesaria.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-search"></i></span>
                        </div>
                        <div>
                            <h6>2. Revisión</h6>
                            <p class="small text-muted mb-0">Nuestro equipo editorial revisará tu investigación (2-5 días).</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-comments"></i></span>
                        </div>
                        <div>
                            <h6>3. Feedback</h6>
                            <p class="small text-muted mb-0">Recibirás comentarios y posibles solicitudes de cambios.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2"><i class="fas fa-check-circle"></i></span>
                        </div>
                        <div>
                            <h6>4. Publicación</h6>
                            <p class="small text-muted mb-0">Una vez aprobada, tu investigación será publicada en el portal.</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Preguntas frecuentes</h6>
                    
                    <div class="accordion accordion-flush" id="accordionFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    ¿Quién puede enviar investigaciones?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                                <div class="accordion-body small">
                                    Cualquier usuario registrado puede enviar investigaciones, aunque se valora especialmente el contenido de académicos, investigadores y profesionales del sector.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    ¿Cuánto tarda el proceso de revisión?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                                <div class="accordion-body small">
                                    Normalmente, el proceso de revisión toma entre 2 y 5 días hábiles, dependiendo del volumen de envíos y la complejidad de la investigación.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    ¿Puedo editar mi investigación después?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                                <div class="accordion-body small">
                                    Sí, puedes solicitar cambios o actualizaciones a tu investigación después de publicada, aunque estos también pasarán por un proceso de revisión.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2
        $('.select2').select2({
            placeholder: 'Selecciona etiquetas...',
            allowClear: true,
            tags: false
        });
        
        // Inicializar TinyMCE
        tinymce.init({
            selector: '.tinymce',
            height: 400,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | link image | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }'
        });
        
        // Previsualización de imagen
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('image-preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="upload-preview" alt="Vista previa">`;
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });
        
        // Confirmación antes de enviar
        document.getElementById('research-form').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const abstract = document.getElementById('abstract').value.trim();
            const termsAccepted = document.getElementById('terms_acceptance').checked;
            
            if (!title || !abstract || !termsAccepted) {
                e.preventDefault();
                alert('Por favor completa todos los campos requeridos y acepta los términos y condiciones.');
            }
        });
    });
</script>
@endpush