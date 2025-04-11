@extends('admin.layouts.app')

@section('title', 'Ayuda - TikTok')

@section('styles')
<style>
    .workflow-step {
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .workflow-step h4 {
        margin-top: 0;
        margin-bottom: 15px;
    }
    .step-number {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        background-color: #4e73df;
        color: white;
        border-radius: 50%;
        margin-right: 10px;
    }
    .workflow-divider {
        height: 30px;
        border-left: 2px dashed #e3e6f0;
        margin-left: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Guía de Uso - Módulo TikTok</h1>
        <a href="{{ route('admin.tiktok.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Introducción -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">¿Qué es el módulo TikTok?</h6>
        </div>
        <div class="card-body">
            <p>
                El módulo TikTok es una herramienta diseñada para ayudar a crear contenido atractivo para TikTok
                a partir de los artículos publicados en nuestro portal de noticias. El sistema selecciona
                automáticamente los artículos con mayor potencial viral y genera guiones adaptados al formato
                de TikTok, facilitando la producción de videos que pueden dirigir tráfico hacia nuestro portal.
            </p>
        </div>
    </div>

    <!-- Flujo de trabajo -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Flujo de Trabajo</h6>
        </div>
        <div class="card-body">
            <!-- Paso 1: Selección -->
            <div class="workflow-step bg-light">
                <h4><span class="step-number">1</span> Selección de Artículos</h4>
                <p>
                    El sistema <strong>evalúa automáticamente</strong> los artículos del portal según diversos criterios:
                </p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-left-primary mb-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Actualidad</div>
                                <div class="text-gray-800">Los artículos más recientes tienen prioridad</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success mb-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase">Popularidad</div>
                                <div class="text-gray-800">Artículos con más visitas son priorizados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info mb-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Engagement</div>
                                <div class="text-gray-800">Se valoran comentarios y compartidos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning mb-2">
                            <div class="card-body py-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase">Viralidad</div>
                                <div class="text-gray-800">Potencial para generar engagement en TikTok</div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-3">
                    Puedes ver los artículos recomendados en la sección <a href="{{ route('admin.tiktok.recommendations') }}">"Recomendaciones"</a> o en el Dashboard.
                </p>
            </div>

            <div class="workflow-divider"></div>

            <!-- Paso 2: Generación -->
            <div class="workflow-step bg-light">
                <h4><span class="step-number">2</span> Generación de Guiones</h4>
                <p>
                    Hay dos formas de crear guiones para TikTok:
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5><i class="fas fa-magic text-primary mr-2"></i> Generación Automática</h5>
                                <p>
                                    El sistema utiliza OpenAI para generar automáticamente guiones adaptados al formato TikTok.
                                    Para generar, haz clic en el botón "Generar" junto a cualquier artículo recomendado.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5><i class="fas fa-edit text-primary mr-2"></i> Creación Manual</h5>
                                <p>
                                    También puedes crear guiones manualmente utilizando nuestro editor. 
                                    Para crear uno, haz clic en "Crear" desde la página de recomendaciones.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-2">
                    <i class="fas fa-info-circle"></i> <strong>Nota:</strong> El sistema genera guiones automáticamente dos veces al día (9:00 AM y 4:00 PM).
                </div>
            </div>

            <div class="workflow-divider"></div>

            <!-- Paso 3: Revisión -->
            <div class="workflow-step bg-light">
                <h4><span class="step-number">3</span> Revisión y Aprobación</h4>
                <p>
                    Una vez generado un guión, debe ser revisado por un editor antes de su producción:
                </p>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <i class="fas fa-file-alt fa-3x text-muted mb-2"></i>
                                <h6>Borrador</h6>
                                <small class="text-muted">Recién creado</small>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <i class="fas fa-hourglass-half fa-3x text-warning mb-2"></i>
                                <h6>Pendiente de Revisión</h6>
                                <small class="text-muted">Esperando aprobación</small>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                <h6>Aprobado</h6>
                                <small class="text-muted">Listo para producción</small>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                                <h6>Rechazado</h6>
                                <small class="text-muted">No cumple requisitos</small>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-3">
                    Los guiones pendientes de revisión aparecen en el Dashboard. Los editores pueden revisarlos,
                    editarlos si es necesario, y aprobarlos o rechazarlos según su criterio.
                </p>
            </div>

            <div class="workflow-divider"></div>

            <!-- Paso 4: Producción -->
            <div class="workflow-step bg-light">
                <h4><span class="step-number">4</span> Producción y Publicación</h4>
                <p>
                    Una vez aprobado, el guión está listo para la producción del video:
                </p>
                <ol>
                    <li>Utiliza el guión y las sugerencias visuales para crear el video de TikTok</li>
                    <li>Incluye los hashtags recomendados al publicar</li>
                    <li>Asegúrate de incluir un llamado a la acción dirigiendo al portal</li>
                    <li>Una vez publicado, marca el guión como "Publicado" en el sistema</li>
                </ol>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Importante:</strong> Recuerda incluir un enlace o código QR al artículo original para poder medir las conversiones.
                </div>
            </div>

            <div class="workflow-divider"></div>

            <!-- Paso 5: Métricas -->
            <div class="workflow-step bg-light">
                <h4><span class="step-number">5</span> Seguimiento de Métricas</h4>
                <p>
                    Una vez publicado el video, registra regularmente sus métricas:
                </p>
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-eye fa-2x text-primary mb-2"></i>
                                <h6>Visualizaciones</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                <h6>Likes</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-comment fa-2x text-info mb-2"></i>
                                <h6>Comentarios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-share fa-2x text-success mb-2"></i>
                                <h6>Compartidos</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-external-link-alt fa-2x text-warning mb-2"></i>
                                <h6>Clics al Portal</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-percentage fa-2x text-secondary mb-2"></i>
                                <h6>Tasa de Conversión</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-3">
                    Estas métricas te permitirán analizar el rendimiento de tus videos y optimizar tu estrategia.
                    Puedes ver estadísticas detalladas en la sección <a href="{{ route('admin.tiktok.stats') }}">"Estadísticas"</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Mejores prácticas -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mejores Prácticas</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-hourglass-half text-primary mr-2"></i> Duración Óptima</h5>
                            <p class="card-text">
                                Los videos de 30-60 segundos suelen tener mejor rendimiento. El guión debe tener entre 100-150 palabras.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-bolt text-primary mr-2"></i> Inicio Impactante</h5>
                            <p class="card-text">
                                Los primeros 3 segundos son cruciales. Comienza con una afirmación sorprendente o una pregunta provocativa.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-hashtag text-primary mr-2"></i> Hashtags Efectivos</h5>
                            <p class="card-text">
                                Usa una combinación de hashtags populares y específicos. No excedas los 4-5 hashtags por video.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-comment-dots text-primary mr-2"></i> Lenguaje Conversacional</h5>
                            <p class="card-text">
                                Utiliza un tono conversacional y directo. Habla como si estuvieras explicándole algo a un amigo.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-hand-point-right text-primary mr-2"></i> Llamado a la Acción</h5>
                            <p class="card-text">
                                Siempre termina con un llamado a la acción claro: "Visita nuestro portal para leer la historia completa".
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-calendar-alt text-primary mr-2"></i> Consistencia</h5>
                            <p class="card-text">
                                Mantén un calendario regular de publicaciones. La consistencia es clave para construir una audiencia.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asistencia -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">¿Necesitas ayuda?</h6>
        </div>
        <div class="card-body">
            <p>
                Si tienes dudas o problemas con el módulo TikTok, puedes contactar al equipo de soporte:
            </p>
            <ul>
                <li><strong>Email:</strong> soporte@tudominio.com</li>
                <li><strong>Slack:</strong> Canal #tiktok-soporte</li>
            </ul>
        </div>
    </div>
</div>
@endsection