@extends('layouts.app')

@section('title', 'Enviar Investigación - ConocIA')
@section('meta_description', 'Comparte tu investigación sobre inteligencia artificial y tecnología con nuestra comunidad de expertos y entusiastas.')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Reducción general del tamaño de letra */
    .research-form-container {
        font-size: 0.875rem; /* 14px */
    }
    
    /* Estilos para títulos más compactos */
    .research-form-container h1 {
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }
    
    .form-section h3 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }
    
    /* Estilos para secciones más compactas */
    .form-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    
    .form-section h3 {
        color: var(--primary-color);
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Estilos para campos de formulario más compactos */
    .form-control, .form-select {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-text {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    /* Estilos para espaciado de elementos */
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
    
    /* Estilos para select2 */
    .select2-container--default .select2-selection--multiple {
        border-color: #ced4da;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        min-height: 35px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: 0.75rem;
        padding: 2px 6px;
    }
    
    /* Campo requerido */
    .required-label::after {
        content: " *";
        color: red;
    }
    
    /* Editor TinyMCE */
    .tox-tinymce {
        border-radius: 0.375rem !important;
    }
    
    /* Vista previa de imagen */
    .upload-preview {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin-top: 0.5rem;
    }
    
    /* Consejos de investigación */
    .research-tips {
        background-color: rgba(56, 182, 255, 0.1);
        border-left: 4px solid var(--primary-color);
        font-size: 0.875rem;
        padding: 0.75rem;
    }
    
    .research-tips h5 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .research-tips ul {
        padding-left: 1.5rem;
    }
    
    /* Mejoras para la barra lateral */
    .sidebar-card {
        top: 2rem; 
        max-height: calc(100vh - 4rem); 
        overflow-y: auto;
        font-size: 0.85rem;
    }
    
    .sidebar-card .card-header h5 {
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    
    .sidebar-card h6 {
        font-size: 0.95rem;
    }
    
    .sidebar-card .small, 
    .sidebar-card .small p {
        font-size: 0.8rem;
    }
    
    .sidebar-card .badge {
        font-size: 0.7rem;
    }
    
    /* Estilos para el acordeón */
    .accordion-button {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }
    
    .accordion-body {
        padding: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4 research-form-container">
    <div class="row">
        <div class="col-lg-8">
            <h1>Enviar Investigación</h1>
            
            <div class="alert alert-info research-tips mb-3">
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
                

                    <!-- Agregar esta sección para usuarios no autenticados -->

                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Información de Contacto</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Como no has iniciado sesión, necesitamos información adicional para contactarte.
                            <a href="{{ route('login') }}" class="alert-link">Inicia sesión</a> si ya tienes una cuenta.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_name" class="form-label">Nombre completo *</label>
                                <input type="text" class="form-control @error('author_name') is-invalid @enderror" 
                                    id="author_name" name="author_name" value="{{ old('author_name') }}" required>
                                @error('author_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="author_email" class="form-label">Correo electrónico *</label>
                                <input type="email" class="form-control @error('author_email') is-invalid @enderror" 
                                    id="author_email" name="author_email" value="{{ old('author_email') }}" required>
                                @error('author_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Te notificaremos a este correo cuando tu investigación sea revisada.</div>
                            </div>
                        </div>
                        
                        <!-- Si has instalado reCAPTCHA, descomenta esto
                        <div class="mt-3">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        -->
                    </div>
                </div>
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        
                        <div class="col-md-6 mb-3">
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Etiquetas</label>
                        @if(isset($tags) && $tags->count() > 0)
                            <select class="select2 form-control @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'selected' : '' }}>{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Selecciona las etiquetas que mejor describan tu investigación.</div>
                        @else
                            <div class="input-group">
                                <input type="text" class="form-control" id="new_tags" name="new_tags" placeholder="Ingresa etiquetas separadas por comas">
                                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                            </div>
                            <div class="form-text">Ingresa etiquetas separadas por comas (ej: inteligencia artificial, machine learning, ética).</div>
                        @endif
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
                        <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract" name="abstract" rows="3" required>{{ old('abstract') }}</textarea>
                        <div class="form-text">Un resumen conciso (100-250 palabras) que presente el objetivo, metodología y hallazgos principales.</div>
                        @error('abstract')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label required-label">Contenido completo</label>
                        <textarea class="form-control tinymce @error('content') is-invalid @enderror" id="content" name="content" rows="12">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="references" class="form-label">Referencias bibliográficas</label>
                        <textarea class="form-control @error('references') is-invalid @enderror" id="references" name="references" rows="3">{{ old('references') }}</textarea>
                        @error('references')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Archivos y multimedia -->
                <div class="form-section">
                    <h3><i class="fas fa-image me-2"></i>Archivos y Multimedia</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Imagen destacada</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <div class="form-text">Formatos: JPG, PNG, GIF. Máx: 2MB.</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="image-preview" class="mt-2"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="document" class="form-label">Documento completo (opcional)</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.doc,.docx">
                            <div class="form-text">Formatos: PDF, DOC, DOCX. Máx: 10MB.</div>
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="form-section">
                    <h3><i class="fas fa-users me-2"></i>Información Adicional</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="additional_authors" class="form-label">Autores adicionales</label>
                            <input type="text" class="form-control @error('additional_authors') is-invalid @enderror" id="additional_authors" name="additional_authors" value="{{ old('additional_authors') }}">
                            <div class="form-text">Nombres separados por comas.</div>
                            @error('additional_authors')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="institution" class="form-label">Institución / Afiliación</label>
                            <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}">
                            @error('institution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms_acceptance" required>
                        <label class="form-check-label" for="terms_acceptance">
                            Confirmo que este contenido es original o tengo los derechos para publicarlo. Acepto los <a href="{{ route('pages.terms') }}" target="_blank">términos y condiciones</a> del sitio.
                        </label>
                    </div>
                </div>






                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-md-2" onclick="window.history.back();">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="submit-btn"><i class="fas fa-paper-plane me-2"></i>Enviar investigación</button>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm position-sticky sidebar-card">
                <div class="card-header bg-primary text-white py-2">
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
                    
                    <hr class="my-2">
                    
                    <h6 class="mb-2">Preguntas frecuentes</h6>
                    
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
            height: 350,
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
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
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
        
        // Script para mejorar el comportamiento de la barra lateral durante el desplazamiento
        const sidebarCard = document.querySelector('.position-sticky');
        
        if (sidebarCard) {
            // Ajustar altura máxima basada en la ventana actual
            function adjustSidebarHeight() {
                const windowHeight = window.innerHeight;
                const offsetTop = 80; // Espacio para el menú superior
                const offsetBottom = 20; // Margen inferior
                sidebarCard.style.maxHeight = `${windowHeight - offsetTop - offsetBottom}px`;
            }
            
            // Llamar al ajuste inicial
            adjustSidebarHeight();
            
            // Ajustar altura cuando cambie el tamaño de la ventana
            window.addEventListener('resize', adjustSidebarHeight);
            
            // Opcionalmente, agregar desplazamiento suave dentro de la barra lateral
            const sidebarLinks = sidebarCard.querySelectorAll('a[href^="#"]');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        }
    });
</script>
@endpush