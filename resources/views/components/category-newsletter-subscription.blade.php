<div class="category-newsletter-subscription py-2 bg-primary bg-opacity-10 border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <!-- Icono y texto -->
            <div class="col-lg-6 mb-2 mb-lg-0">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Mantente al día con noticias personalizadas</h5>
                        <p class="mb-0 text-muted small">Recibe contenido relevante sobre los temas que te interesan</p>
                    </div>
                </div>
            </div>
            
            <!-- Botón de suscripción -->
            <div class="col-lg-6 text-lg-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categorySubscriptionModal">
                    <i class="fas fa-bell me-1"></i> Suscribirme ahora
                </button>
                <span class="text-muted small ms-2 d-none d-md-inline">
                    <i class="fas fa-shield-alt me-1"></i> Sin spam, cancela cuando quieras
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Suscripción por Categorías -->
<div class="modal fade" id="categorySubscriptionModal" tabindex="-1" aria-labelledby="categorySubscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="categorySubscriptionModalLabel">
                    <i class="fas fa-envelope-open-text me-2"></i>Suscripción Personalizada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h5 class="mb-3">Personaliza tu suscripción</h5>
                    <p class="text-muted">Selecciona las categorías de tu interés para recibir noticias personalizadas directamente en tu correo electrónico.</p>
                </div>
                
                <form id="categoryNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="newsletterEmail" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="newsletterEmail" name="email" placeholder="tu@correo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="newsletterName" class="form-label">Nombre (opcional)</label>
                            <input type="text" class="form-control" id="newsletterName" name="name" placeholder="Tu nombre">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Selecciona tus categorías de interés:</label>
                        <div class="categories-container">
                            <div class="select-all-wrapper px-3 py-2 border-bottom">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllCategories">
                                    <label class="form-check-label fw-bold" for="selectAllCategories">
                                        Seleccionar todas las categorías
                                    </label>
                                </div>
                            </div>
                            <div class="categories-list">
                                @php
                                    // Obtener las categorías disponibles
                                    $categories = App\Models\Category::where('is_active', true)
                                    ->withCount('news')
                                    ->orderBy('news_count', 'desc')
                                    ->get();
                                @endphp
                                
                                @foreach($categories as $category)
                                <div class="category-item d-flex align-items-center px-3 py-2 border-bottom">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input category-checkbox" type="checkbox" name="categories[]" 
                                            value="{{ $category->id }}" id="category{{ $category->id }}">
                                        <label class="form-check-label d-flex align-items-center" for="category{{ $category->id }}">
                                            @if(function_exists('getCategoryIcon'))
                                            <i class="fas {{ $getCategoryIcon($category) }} me-2 text-primary"></i>
                                            @else
                                            <i class="fas fa-tag me-2 text-primary"></i>
                                            @endif
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                    <span class="category-count ms-auto text-muted">{{ $category->news_count ?? 0 }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="privacy_consent" id="privacyConsent" required>
                            <label class="form-check-label" for="privacyConsent">
                                Acepto recibir comunicaciones y he leído la <a href="{{ route('pages.privacy') }}" target="_blank">política de privacidad</a>
                            </label>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" id="submitSubscription" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-paper-plane me-1"></i> Suscribirme ahora
                        </button>
                    </div>
                    
                    <!-- Contenedor para mensajes de error/éxito -->
                    <div id="subscriptionResponse" class="mt-3"></div>
                    
                    <!-- Mensaje de error mejorado con opción de reintentar -->
                    <div id="connectionErrorContainer" class="alert alert-danger alert-dismissible fade show mt-3 d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="connectionErrorMessage">Error de conexión. Inténtalo de nuevo más tarde.</span>
                        </div>
                        <div class="mt-2">
                            <button type="button" id="retryButton" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-sync-alt me-1"></i> Reintentar
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                    <i class="fas fa-newspaper text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Últimas noticias</h6>
                                    <p class="mb-0 small text-muted">Recibe noticias sobre los temas que te interesan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                    <i class="fas fa-user-check text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Personalizado</h6>
                                    <p class="mb-0 small text-muted">Contenido según tus categorías de interés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                                    <i class="fas fa-lock text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Privacidad</h6>
                                    <p class="mb-0 small text-muted">Tu información está segura con nosotros</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el modal de suscripción */
.category-newsletter-subscription {
    transition: all 0.3s ease;
}

.categories-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}

.select-all-wrapper {
    background-color: rgba(0,0,0,0.02);
}

.categories-list {
    max-height: 250px;
    overflow-y: auto;
}

.category-item {
    transition: background-color 0.2s;
}

.category-item:hover {
    background-color: rgba(0,0,0,0.02);
}

.category-count {
    font-size: 0.75rem;
    background-color: #f0f0f0;
    padding: 2px 8px;
    border-radius: 12px;
    min-width: 26px;
    text-align: center;
}

.form-check-input:checked + .form-check-label {
    font-weight: 600;
    color: var(--bs-primary);
}

/* Estilos para el contenedor de error */
#connectionErrorContainer {
    border-left: 4px solid #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
}

/* Animación para mensajes de error/éxito */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert {
    animation: fadeInUp 0.3s ease-out forwards;
}
</style>

<script>
// Código mejorado para el formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryNewsletterForm');
    const responseDiv = document.getElementById('subscriptionResponse');
    
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar estado de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        
        // Limpiar mensajes anteriores
        responseDiv.innerHTML = '';
        
        // Recopilar datos del formulario
        const formData = new FormData(form);
        
        // Mostrar datos que se están enviando (para depuración)
        console.log('Enviando datos del formulario:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Enviar petición AJAX
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json' // Asegurarse de que Laravel devuelva JSON
            },
            body: formData
        })
        .then(response => {
            // Capturar el texto de respuesta para depuración
            return response.text().then(text => {
                try {
                    // Intentar parsear la respuesta como JSON
                    const data = JSON.parse(text);
                    return { ok: response.ok, status: response.status, data };
                } catch (e) {
                    // Si no es JSON, mostrar la respuesta en texto plano
                    console.error('Respuesta no es JSON válido:', text);
                    return { 
                        ok: false, 
                        status: response.status, 
                        data: { success: false, message: 'Respuesta del servidor no válida' } 
                    };
                }
            });
        })
        .then(({ ok, status, data }) => {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            if (ok && data.success) {
                // Respuesta exitosa
                responseDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                form.reset();
                
                // Opcional: Cerrar modal después de unos segundos
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('categorySubscriptionModal'));
                    if (modal) modal.hide();
                }, 3000);
            } else {
                // Error procesado por el servidor
                let errorMessage = data.message || 'Hubo un error al procesar tu solicitud.';
                
                // Si hay errores de validación
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat();
                    errorMessage += '<ul class="mb-0 mt-2">';
                    errorList.forEach(err => {
                        errorMessage += `<li>${err}</li>`;
                    });
                    errorMessage += '</ul>';
                }
                
                responseDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            responseDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div class="me-2"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <strong>Error de conexión</strong>
                            <p class="mb-0">No pudimos conectar con el servidor. Verifica tu conexión a internet e inténtalo de nuevo.</p>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-1"></i> Reintentar
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        });
    });
});
</script>