@extends('layouts.app')

@section('title', 'Política de Privacidad - ConocIA')
@section('meta_description', 'Política de privacidad de ConocIA. Conoce cómo recopilamos, utilizamos y protegemos tu información personal.')
@section('meta_keywords', 'privacidad, política de privacidad, datos personales, RGPD, protección de datos')

@section('content')
<div class="container py-5 animate-fade-in">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-lg-5">
                    <h1 class="mb-4">Política de Privacidad</h1>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <p class="mb-0">Última actualización: {{ $lastUpdated }}</p>
                                <p class="mb-0 small">Esta política de privacidad describe cómo ConocIA recopila, utiliza y protege tu información cuando utilizas nuestro sitio web.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">1. Información que recopilamos</h2>
                        <p>En ConocIA, podemos recopilar la siguiente información personal:</p>
                        <ul>
                            <li>Información de identificación personal (nombre, dirección de correo electrónico, etc.) cuando te suscribes a nuestro boletín.</li>
                            <li>Información demográfica y preferencias cuando participas en encuestas o concursos.</li>
                            <li>Datos de uso y navegación a través de cookies y tecnologías similares.</li>
                            <li>Información que proporciones al enviar investigaciones o artículos para su publicación.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">2. Cómo utilizamos tu información</h2>
                        <p>Utilizamos la información recopilada para:</p>
                        <ul>
                            <li>Personalizar tu experiencia y responder a tus necesidades individuales.</li>
                            <li>Mejorar nuestro sitio web y servicios.</li>
                            <li>Enviar correos electrónicos periódicos, como el boletín de noticias o actualizaciones.</li>
                            <li>Procesar transacciones y gestionar concursos o encuestas.</li>
                            <li>Administrar el contenido generado por los usuarios, como investigaciones o comentarios.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">3. Protección de tu información</h2>
                        <p>Implementamos diversas medidas de seguridad para mantener la seguridad de tu información personal cuando introduces, envías o accedes a tu información personal:</p>
                        <ul>
                            <li>Utilizamos encriptación segura para proteger datos sensibles transmitidos online.</li>
                            <li>Solo proporcionamos acceso a información personal a empleados que necesitan la información para realizar una tarea específica.</li>
                            <li>Los servidores y ordenadores que almacenan información personal se mantienen en un entorno seguro.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">4. Uso de cookies</h2>
                        <p>Nuestro sitio web utiliza cookies para mejorar tu experiencia. Puedes consultar nuestra <a href="{{ route('pages.cookies') }}">Política de Cookies</a> para obtener más información sobre cómo utilizamos estas tecnologías.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">5. Divulgación a terceros</h2>
                        <p>No vendemos, intercambiamos ni transferimos a terceros tu información personalmente identificable. Esto no incluye terceros de confianza que nos ayudan a operar nuestro sitio web o a llevar a cabo nuestro negocio, siempre que estas partes acuerden mantener esta información confidencial.</p>
                        <p>Podemos divulgar tu información cuando creamos que la divulgación es apropiada para cumplir con la ley, hacer cumplir las políticas de nuestro sitio o proteger nuestros derechos, propiedad o seguridad.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">6. Enlaces a sitios de terceros</h2>
                        <p>Ocasionalmente, a nuestra discreción, podemos incluir u ofrecer productos o servicios de terceros en nuestro sitio. Estos sitios de terceros tienen políticas de privacidad separadas e independientes. Por lo tanto, no tenemos responsabilidad por el contenido y las actividades de estos sitios.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">7. RGPD (Reglamento General de Protección de Datos)</h2>
                        <p>Para usuarios del Espacio Económico Europeo (EEE), ConocIA procesa datos personales de acuerdo con el RGPD, lo que significa que tienes derechos específicos sobre tus datos:</p>
                        <ul>
                            <li><strong>Derecho de acceso:</strong> Puedes solicitar una copia de tu información personal.</li>
                            <li><strong>Derecho de rectificación:</strong> Puedes solicitar que corrijamos información inexacta.</li>
                            <li><strong>Derecho al olvido:</strong> Puedes solicitar que eliminemos tus datos personales.</li>
                            <li><strong>Derecho a la limitación del tratamiento:</strong> Puedes solicitar la restricción del procesamiento de tus datos.</li>
                            <li><strong>Derecho a la portabilidad de los datos:</strong> Puedes solicitar una copia electrónica de tus datos.</li>
                            <li><strong>Derecho de oposición:</strong> Puedes oponerte al procesamiento de tus datos.</li>
                        </ul>
                        <p>Para ejercer alguno de estos derechos, por favor contáctanos a <a href="mailto:privacidad@conocia.cl">privacidad@conocia.cl</a>.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">8. Consentimiento</h2>
                        <p>Al utilizar nuestro sitio, consientes nuestra política de privacidad.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">9. Cambios en nuestra política de privacidad</h2>
                        <p>Si decidimos cambiar nuestra política de privacidad, publicaremos esos cambios en esta página y actualizaremos la fecha de modificación a continuación.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">10. Contáctanos</h2>
                        <p>Si tienes alguna pregunta sobre esta política de privacidad, puedes contactarnos a través de:</p>
                        <ul>
                            <li>Correo electrónico: <a href="mailto:privacidad@conocia.cl">privacidad@conocia.cl</a></li>
                            <li>Dirección: Santiago, Chile</li>
                            <li>Teléfono: +569 54083474</li>
                        </ul>
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
    });
</script>
@endpush