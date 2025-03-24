@extends('layouts.app')

@section('title', 'Términos de Uso - ConocIA')
@section('meta_description', 'Términos y condiciones de uso de ConocIA. Conoce las normas que rigen el uso de nuestro sitio web y servicios.')
@section('meta_keywords', 'términos de uso, condiciones, normas, legal, acuerdo, servicios')

@section('content')
<div class="container py-5 animate-fade-in">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-lg-5">
                    <h1 class="mb-4">Términos y Condiciones de Uso</h1>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <p class="mb-0">Última actualización: {{ $lastUpdated }}</p>
                                <p class="mb-0 small">Por favor, lee atentamente estos términos antes de utilizar ConocIA.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h2 class="h4">1. Aceptación de los términos</h2>
                        <p>Al acceder y utilizar ConocIA, aceptas estar legalmente obligado por estos Términos y Condiciones de Uso. Si no estás de acuerdo con alguno de estos términos, no debes utilizar este sitio.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">2. Cambios en los términos</h2>
                        <p>ConocIA se reserva el derecho de modificar estos términos en cualquier momento. Los cambios serán efectivos inmediatamente después de su publicación en esta página. Tu uso continuado del sitio después de cualquier cambio constituye tu aceptación de los nuevos términos.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">3. Uso del sitio</h2>
                        <p>Como usuario de ConocIA, aceptas:</p>
                        <ul>
                            <li>No utilizar el sitio de manera ilegal o de cualquier manera que pueda dañar, deshabilitar, sobrecargar o deteriorar el sitio.</li>
                            <li>No intentar acceder a áreas restringidas del sitio, sistemas informáticos o redes conectadas al sitio.</li>
                            <li>No recopilar información de otros usuarios sin su consentimiento.</li>
                            <li>No utilizar el sitio para publicar, transmitir o distribuir material difamatorio, obsceno, ilegal o que viole los derechos de terceros.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">4. Cuenta de usuario</h2>
                        <p>Si creas una cuenta en ConocIA, eres responsable de mantener la confidencialidad de tu cuenta y contraseña, así como de restringir el acceso a tu ordenador. Aceptas ser responsable de todas las actividades que ocurran bajo tu cuenta o contraseña.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">5. Contenido del sitio</h2>
                        <p>Todo el contenido presentado en ConocIA, incluyendo texto, gráficos, logotipos, imágenes, así como la compilación de estos (es decir, la colección, organización y montaje), es propiedad de ConocIA o sus proveedores de contenido y está protegido por las leyes de derechos de autor y propiedad intelectual.</p>
                        <p>El contenido no debe ser copiado, reproducido, modificado, republicado, cargado, publicado, transmitido o distribuido de ninguna manera sin nuestro consentimiento previo por escrito, excepto que puedes descargar, mostrar y/o imprimir una copia del material del sitio exclusivamente para tu uso personal, no comercial.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">6. Contenido generado por usuarios</h2>
                        <p>Al enviar cualquier contenido a ConocIA (incluyendo investigaciones, comentarios, o cualquier otra contribución):</p>
                        <ul>
                            <li>Garantizas que eres el propietario del contenido o que tienes el derecho legal de publicarlo.</li>
                            <li>Concedes a ConocIA una licencia mundial, no exclusiva, libre de regalías para usar, reproducir, modificar, adaptar, publicar, traducir, distribuir y mostrar dicho contenido.</li>
                            <li>Aceptas que este contenido no es confidencial y que ConocIA no tiene obligación de mantener su confidencialidad.</li>
                        </ul>
                        <p>ConocIA se reserva el derecho de eliminar cualquier contenido que infrinja estos términos o que consideremos inapropiado.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">7. Envío de investigaciones</h2>
                        <p>Si envías una investigación a través de nuestra plataforma:</p>
                        <ul>
                            <li>Debes ser el autor original o tener los derechos necesarios para publicar la investigación.</li>
                            <li>La investigación debe ser original y no estar publicada previamente en otros medios (a menos que tengas los derechos para republirla).</li>
                            <li>ConocIA se reserva el derecho de editar, modificar o rechazar cualquier investigación enviada.</li>
                            <li>Debes divulgar cualquier conflicto de intereses relacionado con la investigación.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">8. Enlaces a sitios de terceros</h2>
                        <p>ConocIA puede contener enlaces a sitios web de terceros. Estos enlaces son proporcionados únicamente para tu conveniencia. No tenemos control sobre el contenido y las prácticas de estos sitios y no somos responsables de sus políticas de privacidad o prácticas. La inclusión de cualquier enlace no implica respaldo por parte de ConocIA.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">9. Limitación de responsabilidad</h2>
                        <p>ConocIA y sus colaboradores no serán responsables de ningún daño directo, indirecto, incidental, especial o consecuente que resulte del uso o la imposibilidad de usar el sitio o los servicios, de cualquier contenido publicado en el sitio o de la conducta de cualquier usuario, ya sea en línea o fuera de línea.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">10. Descargo de garantías</h2>
                        <p>El sitio y sus contenidos se proporcionan "tal cual" y "según disponibilidad", sin garantías de ningún tipo, ya sean expresas o implícitas. ConocIA no garantiza que el sitio sea ininterrumpido, seguro o libre de errores, virus u otros componentes dañinos.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">11. Indemnización</h2>
                        <p>Aceptas indemnizar, defender y mantener indemne a ConocIA, sus funcionarios, directores, empleados, agentes y terceros, por cualquier reclamación, responsabilidad, daño, pérdida y gasto, incluyendo honorarios legales razonables y costos, relacionados con o derivados de tu uso del sitio, tu violación de estos Términos o tu violación de cualquier derecho de terceros.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">12. Ley aplicable</h2>
                        <p>Estos términos se regirán e interpretarán de acuerdo con las leyes de España, sin dar efecto a ningún principio de conflictos de leyes. Cualquier disputa que surja en relación con estos términos estará sujeta a la jurisdicción exclusiva de los tribunales de Madrid, España.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">13. Terminación</h2>
                        <p>ConocIA se reserva el derecho, a su sola discreción, de terminar tu acceso al sitio y a los servicios relacionados o cualquier parte de los mismos en cualquier momento, sin previo aviso.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2 class="h4">14. Contáctanos</h2>
                        <p>Si tienes alguna pregunta sobre estos términos, por favor contáctanos a través de:</p>
                        <ul>
                            <li>Correo electrónico: <a href="mailto:legal@conocia.cl">legal@conocia.cl</a></li>
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