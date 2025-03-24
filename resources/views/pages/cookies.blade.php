@extends('layouts.app')

@section('title', 'Política de Cookies - ConocIA')
@section('meta_description', 'Política de cookies de ConocIA. Información sobre cómo utilizamos las cookies y tecnologías similares en nuestro sitio web.')
@section('meta_keywords', 'cookies, política de cookies, rastreo web, privacidad web, almacenamiento local')

@section('content')
<div class="container py-5 animate-fade-in">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-lg-5">
                    <h1 class="mb-4">Política de Cookies</h1>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <p class="mb-0">Última actualización: {{ $lastUpdated }}</p>
                                <p class="mb-0 small">Esta política explica cómo ConocIA utiliza cookies y tecnologías similares cuando visitas nuestro sitio web.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">1. ¿Qué son las cookies?</h2>
                        <p>Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo (ordenador, tableta o móvil) cuando visitas un sitio web. Las cookies se utilizan ampliamente para hacer que los sitios web funcionen de manera más eficiente, proporcionar información a los propietarios del sitio y mejorar la experiencia del usuario.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">2. Tipos de cookies que utilizamos</h2>
                        <p>En ConocIA utilizamos los siguientes tipos de cookies:</p>
                        
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo de cookie</th>
                                        <th>Descripción</th>
                                        <th>Duración</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Cookies esenciales</strong></td>
                                        <td>Necesarias para el funcionamiento básico del sitio. Te permiten navegar por el sitio y utilizar sus funciones.</td>
                                        <td>Sesión / Persistentes</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cookies de preferencias</strong></td>
                                        <td>Permiten que el sitio recuerde tus preferencias, como el idioma o la región, y proporcionan funciones mejoradas y más personales.</td>
                                        <td>1 año</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cookies de análisis</strong></td>
                                        <td>Nos ayudan a entender cómo interactúan los visitantes con el sitio, recopilando y reportando información de forma anónima.</td>
                                        <td>2 años</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cookies de marketing</strong></td>
                                        <td>Se utilizan para rastrear a los visitantes en los sitios web con el fin de mostrar anuncios relevantes y personalizados.</td>
                                        <td>30 días - 1 año</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">3. Cookies específicas que utilizamos</h2>
                        
                        <div class="accordion" id="cookiesAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingEssential">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEssential" aria-expanded="true" aria-controls="collapseEssential">
                                        Cookies Esenciales
                                    </button>
                                </h2>
                                <div id="collapseEssential" class="accordion-collapse collapse show" aria-labelledby="headingEssential" data-bs-parent="#cookiesAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Propósito</th>
                                                        <th>Duración</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>XSRF-TOKEN</td>
                                                        <td>Ayuda a proteger contra ataques CSRF (Cross-Site Request Forgery).</td>
                                                        <td>Sesión</td>
                                                    </tr>
                                                    <tr>
                                                        <td>conocia_session</td>
                                                        <td>Permite mantener la sesión del usuario activa mientras navega por el sitio.</td>
                                                        <td>2 horas</td>
                                                    </tr>
                                                    <tr>
                                                        <td>cookie_consent</td>
                                                        <td>Guarda tus preferencias sobre el uso de cookies en nuestro sitio.</td>
                                                        <td>1 año</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPreferences">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePreferences" aria-expanded="false" aria-controls="collapsePreferences">
                                        Cookies de Preferencias
                                    </button>
                                </h2>
                                <div id="collapsePreferences" class="accordion-collapse collapse" aria-labelledby="headingPreferences" data-bs-parent="#cookiesAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Propósito</th>
                                                        <th>Duración</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>theme_preference</td>
                                                        <td>Guarda tu preferencia de tema (claro/oscuro).</td>
                                                        <td>1 año</td>
                                                    </tr>
                                                    <tr>
                                                        <td>language</td>
                                                        <td>Guarda tu preferencia de idioma.</td>
                                                        <td>1 año</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingAnalytics">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnalytics" aria-expanded="false" aria-controls="collapseAnalytics">
                                        Cookies de Análisis
                                    </button>
                                </h2>
                                <div id="collapseAnalytics" class="accordion-collapse collapse" aria-labelledby="headingAnalytics" data-bs-parent="#cookiesAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Propósito</th>
                                                        <th>Duración</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>_ga</td>
                                                        <td>Utilizada por Google Analytics para distinguir usuarios únicos.</td>
                                                        <td>2 años</td>
                                                    </tr>
                                                    <tr>
                                                        <td>_gid</td>
                                                        <td>Utilizada por Google Analytics para identificar a los usuarios.</td>
                                                        <td>24 horas</td>
                                                    </tr>
                                                    <tr>
                                                        <td>_gat</td>
                                                        <td>Utilizada por Google Analytics para limitar la tasa de solicitudes.</td>
                                                        <td>1 minuto</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingMarketing">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMarketing" aria-expanded="false" aria-controls="collapseMarketing">
                                        Cookies de Marketing
                                    </button>
                                </h2>
                                <div id="collapseMarketing" class="accordion-collapse collapse" aria-labelledby="headingMarketing" data-bs-parent="#cookiesAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Propósito</th>
                                                        <th>Duración</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>_fbp</td>
                                                        <td>Utilizada por Facebook para ofrecer servicios publicitarios.</td>
                                                        <td>3 meses</td>
                                                    </tr>
                                                    <tr>
                                                        <td>fr</td>
                                                        <td>Permite a Facebook ofrecer publicidad personalizada.</td>
                                                        <td>3 meses</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">4. Tecnologías similares</h2>
                        <p>Además de las cookies, también podemos utilizar otras tecnologías similares para almacenar y acceder a datos en tu dispositivo:</p>
                        <ul>
                            <li><strong>Web beacons:</strong> Pequeñas imágenes transparentes que nos permiten, por ejemplo, conocer si has abierto un correo electrónico que te hemos enviado.</li>
                            <li><strong>Almacenamiento local HTML5:</strong> Permite a los sitios web almacenar datos en tu dispositivo de forma similar a las cookies, pero con mayor capacidad y sin transmitir datos al servidor cada vez que se solicita el sitio web.</li>
                            <li><strong>Almacenamiento de sesión:</strong> Similar al almacenamiento local, pero los datos se eliminan cuando finaliza la sesión del navegador.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">5. Gestión de cookies</h2>
                        <p>Puedes gestionar tus preferencias de cookies a través del banner de cookies que aparece cuando visitas nuestro sitio por primera vez. También puedes controlar y/o eliminar las cookies como desees. Para obtener más información sobre cómo hacerlo, puedes visitar <a href="https://www.aboutcookies.org/" target="_blank" rel="noopener noreferrer">aboutcookies.org</a>.</p>
                        <p>Puedes eliminar todas las cookies que ya están en tu dispositivo y configurar la mayoría de los navegadores para evitar que se coloquen. Sin embargo, si lo haces, es posible que tengas que ajustar manualmente algunas preferencias cada vez que visites un sitio y que algunos servicios y funcionalidades no funcionen.</p>
                        
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Navegador</th>
                                        <th>Guía para gestionar cookies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Google Chrome</td>
                                        <td><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Guía de Chrome</a></td>
                                    </tr>
                                    <tr>
                                        <td>Mozilla Firefox</td>
                                        <td><a href="https://support.mozilla.org/es/kb/habilitar-y-deshabilitar-cookies-sitios-web-rastrear-preferencias" target="_blank" rel="noopener noreferrer">Guía de Firefox</a></td>
                                    </tr>
                                    <tr>
                                        <td>Safari</td>
                                        <td><a href="https://support.apple.com/es-es/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Guía de Safari</a></td>
                                    </tr>
                                    <tr>
                                        <td>Microsoft Edge</td>
                                        <td><a href="https://support.microsoft.com/es-es/microsoft-edge/eliminar-las-cookies-en-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer">Guía de Edge</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">6. Cookies de terceros</h2>
                        <p>Algunos de nuestros partners pueden establecer cookies en tu dispositivo cuando visitas nuestro sitio. Estos socios incluyen servicios de análisis y redes publicitarias. Las cookies de terceros permiten a estas empresas recopilar información sobre tus visitas a diferentes sitios web, incluido el nuestro.</p>
                        <p>No controlamos las cookies de estos terceros. Te recomendamos revisar las políticas de privacidad y de cookies de estos terceros para obtener más información.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">7. Cambios en nuestra política de cookies</h2>
                        <p>Podemos actualizar esta política de cookies de vez en cuando. Te notificaremos cualquier cambio publicando la nueva política de cookies en esta página y, en caso de cambios significativos, te enviaremos una notificación.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">8. Contáctanos</h2>
                        <p>Si tienes alguna pregunta sobre nuestra política de cookies, por favor contáctanos a través de:</p>
                        <ul>
                            <li>Correo electrónico: <a href="mailto:privacidad@conocia.cl">privacidad@conocia.cl</a></li>
                            <li>Dirección: Santiago, Chile</li>
                            <li>Teléfono: +569 54083474</li>
                        </ul>
                    </div>
                    
                    <div class="mt-5">
                        <a href="#" id="openCookieSettings" class="btn btn-primary mb-2">
                            <i class="fas fa-sliders-h me-2"></i>Gestionar mis preferencias de cookies
                        </a>
                        <a href="{{ route('pages.privacy') }}" class="btn btn-outline-primary mb-2 ms-md-2">
                            <i class="fas fa-shield-alt me-2"></i>Ver política de privacidad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Marcar enlaces internos activos en el documento
        const links = document.querySelectorAll('.card-body a[href^="{{ url("/") }}"]');
        links.forEach(link => {
            link.classList.add('text-primary');
        });
        
        // Manejador para botón de gestión de cookies
        const cookieSettingsBtn = document.getElementById('openCookieSettings');
        if (cookieSettingsBtn) {
            cookieSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Aquí se puede implementar la apertura del banner de cookies
                alert('Funcionalidad para abrir el banner de preferencias de cookies. Integrar con tu solución de gestión de cookies.');
            });
        }
    });
</script>
@endpush