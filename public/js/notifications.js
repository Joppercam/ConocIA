// public/js/notifications.js

/**
 * Sistema de notificaciones
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const notificationBell = document.querySelector('.notification-bell');
    const notificationCount = document.querySelector('.notification-count');
    const notificationsContainer = document.querySelector('.notifications-container');
    
    // Si no existe el botón de notificaciones, no continuar
    if (!notificationBell) return;
    
    let lastNotificationId = 0;
    
    // Función para cargar notificaciones
    function loadNotifications() {
        fetch('/admin/notifications/get?last_id=' + lastNotificationId)
            .then(response => response.json())
            .then(data => {
                // Actualizar contador
                updateNotificationCounter(data.count);
                
                // Actualizar contenido del dropdown
                if (notificationsContainer) {
                    updateNotificationsDropdown(data.notifications);
                }
                
                // Si hay nuevas notificaciones desde la última carga, mostrar alerta
                if (data.new_notifications && data.new_notifications.length > 0) {
                    // Actualizar el último ID visto
                    if (data.new_notifications.length > 0) {
                        const maxId = Math.max(...data.new_notifications.map(n => n.id));
                        if (maxId > lastNotificationId) {
                            lastNotificationId = maxId;
                            
                            // Opcional: Reproducir sonido o mostrar alerta visual
                            showNewNotificationAlert();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error cargando notificaciones:', error);
            });
    }
    
    // Actualizar contador de notificaciones
    function updateNotificationCounter(count) {
        if (!notificationCount) return;
        
        if (count > 0) {
            notificationCount.textContent = count > 9 ? '9+' : count;
            notificationCount.style.display = 'inline-block';
        } else {
            notificationCount.textContent = '0';
            notificationCount.style.display = 'none';
        }
    }
    
    // Actualizar contenido del dropdown de notificaciones
    function updateNotificationsDropdown(notifications) {
        if (!notificationsContainer) return;
        
        // Limpiar contenedor
        notificationsContainer.innerHTML = '';
        
        if (notifications.length === 0) {
            notificationsContainer.innerHTML = `
                <div class="dropdown-item text-center py-3">
                    <span class="text-muted">No tienes notificaciones sin leer</span>
                </div>
            `;
            return;
        }
        
        // Crear elementos para cada notificación
        notifications.forEach(notification => {
            const item = createNotificationItem(notification);
            notificationsContainer.appendChild(item);
        });
        
        // Añadir enlace para ver todas
        const viewAllLink = document.createElement('div');
        viewAllLink.className = 'dropdown-item text-center';
        viewAllLink.innerHTML = `<a href="/admin/notifications" class="text-primary">Ver todas las notificaciones</a>`;
        notificationsContainer.appendChild(viewAllLink);
    }
    
    // Crear elemento HTML para una notificación
    function createNotificationItem(notification) {
        // Determinar icono y contenido según el tipo
        let icon, bgColor, message;
        
        switch (notification.type) {
            case 'comment':
                icon = 'fas fa-comment';
                bgColor = 'bg-info';
                message = `Nuevo comentario en: ${notification.data.article_title || 'Artículo'}`;
                break;
            case 'subscription':
                icon = 'fas fa-user-check';
                bgColor = 'bg-primary';
                message = `Nueva suscripción: ${notification.data.email || 'Usuario'}`;
                break;
            case 'comment_approved':
                icon = 'fas fa-check-circle';
                bgColor = 'bg-success';
                message = 'Tu comentario ha sido aprobado';
                break;
            case 'comment_rejected':
                icon = 'fas fa-times-circle';
                bgColor = 'bg-danger';
                message = 'Tu comentario ha sido rechazado';
                break;
            case 'comment_reply':
                icon = 'fas fa-reply';
                bgColor = 'bg-success';
                message = 'Respuesta a tu comentario';
                break;
            default:
                icon = 'fas fa-bell';
                bgColor = 'bg-warning';
                message = notification.data.message || 'Notificación del sistema';
        }
        
        // Crear elemento
        const div = document.createElement('div');
        div.className = 'dropdown-item d-flex align-items-center notification-item';
        div.setAttribute('data-id', notification.id);
        div.href = '/admin/notifications';
        
        // Añadir contenido HTML
        div.innerHTML = `
            <div class="me-3">
                <div class="rounded-circle ${bgColor} p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="${icon} text-white"></i>
                </div>
            </div>
            <div>
                <div class="small text-muted">${formatTimeAgo(notification.created_at)}</div>
                <span class="fw-bold">${message}</span>
            </div>
        `;
        
        // Al hacer clic, ir a la página de notificaciones y marcar como leída
        div.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Marcar como leída
            fetch(`/admin/notifications/${notification.id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            // Redirigir a la página de notificaciones
            window.location.href = '/admin/notifications';
        });
        
        return div;
    }
    
    // Función auxiliar para formatear tiempo relativo
    function formatTimeAgo(datetime) {
        const date = new Date(datetime);
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        
        if (diffSec < 60) {
            return `Hace ${diffSec} segundos`;
        } else if (diffMin < 60) {
            return `Hace ${diffMin} minutos`;
        } else if (diffHour < 24) {
            return `Hace ${diffHour} horas`;
        } else if (diffDay < 7) {
            return `Hace ${diffDay} días`;
        } else {
            return date.toLocaleDateString();
        }
    }
    
    // Mostrar alerta visual para nuevas notificaciones
    function showNewNotificationAlert() {
        // Opcional: añadir una animación o efecto al icono de notificación
        if (notificationBell) {
            notificationBell.classList.add('notification-animation');
            
            // Quitar la clase después de la animación
            setTimeout(() => {
                notificationBell.classList.remove('notification-animation');
            }, 1000);
        }
    }
    
    // Cargar notificaciones cuando se carga la página
    loadNotifications();
    
    // Configurar actualizaciones periódicas cada 30 segundos
    setInterval(loadNotifications, 30000);
    
    // Cargar notificaciones al hacer clic en el icono
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            loadNotifications();
        });
    }
    
    // Añadir estilos para la animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bellRing {
            0% { transform: rotate(0); }
            15% { transform: rotate(5deg); }
            30% { transform: rotate(-5deg); }
            45% { transform: rotate(4deg); }
            60% { transform: rotate(-4deg); }
            75% { transform: rotate(2deg); }
            85% { transform: rotate(-2deg); }
            92% { transform: rotate(1deg); }
            100% { transform: rotate(0); }
        }
        
        .notification-animation {
            animation: bellRing 0.8s ease;
        }
        
        .notification-bell:hover {
            color: #0d6efd;
            transition: color 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});