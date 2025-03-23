<!-- resources/views/submit-research.blade.php -->
@extends('layouts.app')

@section('title', 'Enviar Investigación - ConocIA')

@section('content')
    <!-- Header de la página -->
    <div class="bg-light py-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="mb-3">Envía tu investigación</h1>
                    <p class="lead text-muted">Comparte tu conocimiento y conviértete en un colaborador de ConocIA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal con formulario -->
    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-8 mb-5 mb-lg-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Formulario de envío</h3>
                        
                        <!-- Formulario de envío de investigación -->
                        <form action="#" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Información personal -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">Información personal</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre completo *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Correo electrónico *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="affiliation" class="form-label">Institución/Empresa</label>
                                    <input type="text" class="form-control" id="affiliation" name="affiliation">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Breve biografía (max. 200 palabras) *</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
                                    <div class="form-text">Incluye tu experiencia, especialización y trayectoria relacionada con el tema de tu investigación.</div>
                                </div>
                            </div>
                            
                            <!-- Información de la investigación -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">Detalles de la investigación</h5>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título de la investigación *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoría *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="" selected disabled>Selecciona una categoría</option>
                                        <option value="inteligencia-artificial">Inteligencia Artificial</option>
                                        <option value="machine-learning">Machine Learning</option>
                                        <option value="robotica">Robótica</option>
                                        <option value="computacion-cuantica">Computación Cuántica</option>
                                        <option value="ciberseguridad">Ciberseguridad</option>
                                        <option value="blockchain">Blockchain</option>
                                        <option value="otras">Otras tecnologías</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="summary" class="form-label">Resumen (max. 500 palabras) *</label>
                                    <textarea class="form-control" id="summary" name="summary" rows="5" required></textarea>
                                    <div class="form-text">Proporciona una descripción general de tu investigación, sus objetivos y principales hallazgos.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado de la investigación *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="" selected disabled>Selecciona una opción</option>
                                        <option value="en-progreso">En progreso</option>
                                        <option value="completada">Completada</option>
                                        <option value="publicada">Publicada en otro medio</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Documentos adjuntos (opcional)</label>
                                    <input class="form-control" type="file" id="attachments" name="attachments[]" multiple>
                                    <div class="form-text">Puedes adjuntar documentos, imágenes o archivos relevantes (PDF, DOCX, JPG, PNG). Máximo 5 archivos, 10MB por archivo.</div>
                                </div>
                            </div>
                            
                            <!-- Información adicional -->
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 mb-3">Información adicional</h5>
                                
                                <div class="mb-3">
                                    <label for="additional_info" class="form-label">Comentarios adicionales</label>
                                    <textarea class="form-control" id="additional_info" name="additional_info" rows="3"></textarea>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="original_content" name="original_content" required>
                                    <label class="form-check-label" for="original_content">
                                        Confirmo que este contenido es original o tengo los derechos para compartirlo *
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Acepto los términos y condiciones de publicación *
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Botón de envío -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">Enviar investigación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Barra lateral con información complementaria -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Proceso editorial</h5>
                        <div class="d-flex mb-3">
                            <div class="process-icon me-3">
                                <span class="bg-primary text-white">1</span>
                            </div>
                            <div>
                                <h6>Envío</h6>
                                <p class="text-muted small mb-0">Completa este formulario con tu propuesta.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="process-icon me-3">
                                <span class="bg-primary text-white">2</span>
                            </div>
                            <div>
                                <h6>Revisión</h6>
                                <p class="text-muted small mb-0">Nuestro equipo editorial evaluará tu contenido (5-7 días).</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="process-icon me-3">
                                <span class="bg-primary text-white">3</span>
                            </div>
                            <div>
                                <h6>Retroalimentación</h6>
                                <p class="text-muted small mb-0">Recibirás comentarios y sugerencias de mejora.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="process-icon me-3">
                                <span class="bg-primary text-white">4</span>
                            </div>
                            <div>
                                <h6>Publicación</h6>
                                <p class="text-muted small mb-0">Una vez aprobado, tu contenido será publicado en nuestra plataforma.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Consejos para el envío</h5>
                        <ul class="mb-0 ps-0" style="list-style-type: none;">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Asegúrate de que tu investigación sea relevante y actual.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Proporciona datos y referencias para respaldar tus afirmaciones.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Evita jerga excesivamente técnica, busca un equilibrio entre rigor y accesibilidad.
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Incluye elementos visuales (gráficos, diagramas) cuando sea posible.
                            </li>
                            <li>
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Cierra con conclusiones claras e implicaciones prácticas.
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">¿Tienes dudas?</h5>
                        <p class="mb-3">Si tienes preguntas sobre el proceso de envío, no dudes en contactarnos.</p>
                        <p class="mb-0">
                            <i class="fas fa-envelope me-2"></i> investigacion@conocia.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .process-icon span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-weight: 600;
    }
</style>
@endpush