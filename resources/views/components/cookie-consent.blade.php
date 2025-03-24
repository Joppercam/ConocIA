<div id="cookie-consent-banner" class="position-fixed bottom-0 start-0 end-0 p-3" style="z-index: 1080; display: none;">
    <div class="container">
        <div class="card border-0 shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <h5><i class="fas fa-cookie-bite me-2 text-primary"></i>Utilizamos cookies</h5>
                        <p class="mb-0">
                            Este sitio utiliza cookies para mejorar tu experiencia de navegación, mostrar contenido personalizado y analizar el tráfico. 
                            Al hacer clic en "Aceptar todas", aceptas nuestro uso de cookies. Puedes configurar tus preferencias o rechazar las cookies no esenciales.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                            <button id="cookie-customize" class="btn btn-outline-primary">Personalizar</button>
                            <button id="cookie-accept-essential" class="btn btn-outline-secondary">Solo esenciales</button>
                            <button id="cookie-accept-all" class="btn btn-primary">Aceptar todas</button>
                        </div>
                        <div class="mt-2 text-center text-lg-end">
                            <a href="{{ route('pages.cookies') }}" class="text-decoration-none small">
                                <i class="fas fa-info-circle me-1"></i>Más información
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cookie-settings-modal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sliders-h me-2 text-primary"></i>Configuración de cookies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>
                    Puedes elegir qué categorías de cookies deseas permitir. Haz clic en cada categoría para obtener más información sobre las cookies que utilizamos y por qué.
                </p>
                
                <div class="list-group mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Cookies esenciales</h6>
                            <p class="mb-0 small text-muted">Necesarias para el funcionamiento básico del sitio. Siempre están activas.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-essential" checked disabled>
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Cookies de preferencias</h6>
                            <p class="mb-0 small text-muted">Permiten que el sitio recuerde tus preferencias, como el idioma o la región.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-preferences">
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Cookies de análisis</h6>
                            <p class="mb-0 small text-muted">Nos ayudan a entender cómo interactúan los visitantes con el sitio.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-analytics">
                        </div>
                    </div>
                    
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Cookies de marketing</h6>
                            <p class="mb-0 small text-muted">Se utilizan para rastrear a los visitantes en los sitios web con fines publicitarios.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-marketing">
                        </div>
                    </div>
                </div>
                
                <p class="small">
                    Para más información sobre las cookies que utilizamos y cómo puedes gestionarlas, visita nuestra <a href="{{ route('pages.cookies') }}">Política de Cookies</a>.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="cookie-save-preferences">Guardar preferencias</button>
            </div>
        </div>
    </div>
</div>

<!-- El script del gestor de cookies se carga desde un archivo externo -->
<script src="{{ asset('js/cookie-manager.js') }}" defer></script>