/**
 * Cookie Manager para ConocIA
 * Maneja el consentimiento de cookies y la activación de servicios relacionados
 */

class CookieManager {
    constructor() {
        this.consentKey = 'conocia_cookie_consent';
        this.consentVersion = '1.0'; // Incrementar cuando cambie la política de cookies
        this.cookieOptions = {
            essential: true, // Siempre necesarias
            preferences: false,
            analytics: false,
            marketing: false
        };
        this.analyticsConfig = window.conociaAnalyticsConfig || {};
        this.analyticsLoaded = false;
        this.gtmLoaded = false;
        
        this.initListeners();
        this.checkConsent();
    }
    
    /**
     * Inicializa todos los event listeners
     */
    initListeners() {
        // Elementos del banner y modal
        const banner = document.getElementById('cookie-consent-banner');
        const customizeBtn = document.getElementById('cookie-customize');
        const acceptAllBtn = document.getElementById('cookie-accept-all');
        const acceptEssentialBtn = document.getElementById('cookie-accept-essential');
        const savePreferencesBtn = document.getElementById('cookie-save-preferences');
        const openSettingsBtn = document.getElementById('openCookieSettings');
        
        // Verificar que los elementos existen
        if (!banner || !customizeBtn || !acceptAllBtn || !acceptEssentialBtn || !savePreferencesBtn) {
            console.warn('Cookie banner elements not found');
            return;
        }
        
        // Configurar listeners para el banner principal
        customizeBtn.addEventListener('click', () => this.openPreferencesModal());
        acceptAllBtn.addEventListener('click', () => this.acceptAll());
        acceptEssentialBtn.addEventListener('click', () => this.acceptEssential());
        
        // Listener para guardar preferencias personalizadas
        savePreferencesBtn.addEventListener('click', () => this.savePreferences());
        
        // Botón adicional para abrir preferencias (en páginas de políticas)
        if (openSettingsBtn) {
            openSettingsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openPreferencesModal();
            });
        }
    }
    
    /**
     * Comprueba si existe consentimiento previo
     */
    checkConsent() {
        const banner = document.getElementById('cookie-consent-banner');
        if (!banner) return;
        
        try {
            const savedConsent = this.getConsentFromStorage();
            
            // Mostrar banner si no hay consentimiento previo o si ha cambiado la versión
            if (!savedConsent || savedConsent.version !== this.consentVersion) {
                banner.style.display = 'block';
            } else {
                // Si hay consentimiento válido, aplicar preferencias
                this.cookieOptions = {
                    ...this.cookieOptions,
                    ...savedConsent.preferences
                };
                this.applyConsent();
                this.loadPreferencesToUI();
            }
        } catch (error) {
            console.error('Error checking cookie consent:', error);
            banner.style.display = 'block';
        }
    }
    
    /**
     * Abre el modal de preferencias
     */
    openPreferencesModal() {
        // Cargar preferencias actuales en la UI
        this.loadPreferencesToUI();
        
        // Mostrar el modal
        const modal = document.getElementById('cookie-settings-modal');
        if (modal) {
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        } else {
            console.error('Cookie preferences modal not found');
        }
    }
    
    /**
     * Carga las preferencias actuales en la UI
     */
    loadPreferencesToUI() {
        const preferencesCheckbox = document.getElementById('cookie-preferences');
        const analyticsCheckbox = document.getElementById('cookie-analytics');
        const marketingCheckbox = document.getElementById('cookie-marketing');
        
        if (preferencesCheckbox) preferencesCheckbox.checked = this.cookieOptions.preferences;
        if (analyticsCheckbox) analyticsCheckbox.checked = this.cookieOptions.analytics;
        if (marketingCheckbox) marketingCheckbox.checked = this.cookieOptions.marketing;
    }
    
    /**
     * Guarda las preferencias de cookies seleccionadas por el usuario
     */
    savePreferences() {
        const preferencesCheckbox = document.getElementById('cookie-preferences');
        const analyticsCheckbox = document.getElementById('cookie-analytics');
        const marketingCheckbox = document.getElementById('cookie-marketing');
        
        this.cookieOptions = {
            essential: true, // Siempre activas
            preferences: preferencesCheckbox ? preferencesCheckbox.checked : false,
            analytics: analyticsCheckbox ? analyticsCheckbox.checked : false,
            marketing: marketingCheckbox ? marketingCheckbox.checked : false
        };
        
        this.saveConsent();
        this.applyConsent();
        
        // Cerrar el modal y el banner
        const modal = document.getElementById('cookie-settings-modal');
        if (modal) {
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) bootstrapModal.hide();
        }
        
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) banner.style.display = 'none';
    }
    
    /**
     * Acepta todas las cookies
     */
    acceptAll() {
        this.cookieOptions = {
            essential: true,
            preferences: true,
            analytics: true,
            marketing: true
        };
        
        this.saveConsent();
        this.applyConsent();
        
        // Ocultar el banner
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) banner.style.display = 'none';
    }
    
    /**
     * Acepta solo cookies esenciales
     */
    acceptEssential() {
        this.cookieOptions = {
            essential: true,
            preferences: false,
            analytics: false,
            marketing: false
        };
        
        this.saveConsent();
        this.applyConsent();
        
        // Ocultar el banner
        const banner = document.getElementById('cookie-consent-banner');
        if (banner) banner.style.display = 'none';
    }
    
    /**
     * Guarda el consentimiento en localStorage
     */
    saveConsent() {
        const consent = {
            version: this.consentVersion,
            timestamp: new Date().toISOString(),
            preferences: this.cookieOptions
        };
        
        localStorage.setItem(this.consentKey, JSON.stringify(consent));
    }
    
    /**
     * Obtiene el consentimiento guardado
     */
    getConsentFromStorage() {
        const consentData = localStorage.getItem(this.consentKey);
        return consentData ? JSON.parse(consentData) : null;
    }
    
    /**
     * Aplica el consentimiento activando scripts y servicios según corresponda
     */
    applyConsent() {
        console.log('Applying cookie consent:', this.cookieOptions);
        
        // Google Analytics
        if (this.cookieOptions.analytics) {
            this.loadGoogleAnalytics();
        } else {
            this.disableGoogleAnalytics();
        }
        
        // Scripts de marketing
        if (this.cookieOptions.marketing) {
            this.loadMarketingScripts();
        }
        
        // Guardar el tema preferido si se aceptaron las cookies de preferencias
        if (this.cookieOptions.preferences) {
            const currentTheme = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
        }
    }
    
    /**
     * Carga Google Analytics si está permitido
     */
    loadGoogleAnalytics() {
        if (!this.analyticsConfig.enabled || !this.cookieOptions.analytics) {
            return;
        }

        if (this.analyticsConfig.gtmContainerId) {
            this.loadGoogleTagManager(this.analyticsConfig.gtmContainerId);
        }

        const loaderId = this.analyticsConfig.gtagLoaderId || this.analyticsConfig.ga4MeasurementId || this.analyticsConfig.googleTagId;
        const measurementId = this.analyticsConfig.ga4MeasurementId || this.analyticsConfig.googleTagId;

        if (!loaderId || !measurementId) {
            return;
        }

        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function gtag(){ window.dataLayer.push(arguments); };

        window.gtag('consent', 'update', {
            analytics_storage: 'granted',
            ad_storage: this.cookieOptions.marketing ? 'granted' : 'denied',
            ad_user_data: this.cookieOptions.marketing ? 'granted' : 'denied',
            ad_personalization: this.cookieOptions.marketing ? 'granted' : 'denied'
        });

        if (!this.analyticsLoaded) {
            const script = document.createElement('script');
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(loaderId)}`;
            document.head.appendChild(script);

            window.gtag('js', new Date());
            window.gtag('config', measurementId, { anonymize_ip: true });
            this.analyticsLoaded = true;
        }
    }

    /**
     * Carga Google Tag Manager si existe un contenedor configurado.
     */
    loadGoogleTagManager(containerId) {
        if (this.gtmLoaded || !containerId) {
            return;
        }

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });

        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(containerId)}`;
        document.head.appendChild(script);
        this.gtmLoaded = true;
    }

    /**
     * Revoca el consentimiento de analytics para la sesión actual.
     */
    disableGoogleAnalytics() {
        if (typeof window.gtag === 'function') {
            window.gtag('consent', 'update', {
                analytics_storage: 'denied',
                ad_storage: 'denied',
                ad_user_data: 'denied',
                ad_personalization: 'denied'
            });
        }

        this.deleteCookieByPrefix('_ga');
        this.deleteCookieByPrefix('_gid');
        this.deleteCookieByPrefix('_gat');
    }

    /**
     * Borra cookies conocidas de Google Analytics en el dominio actual.
     */
    deleteCookieByPrefix(prefix) {
        document.cookie.split(';').forEach((cookie) => {
            const cookieName = cookie.split('=')[0].trim();
            if (!cookieName || !cookieName.startsWith(prefix)) {
                return;
            }

            const hostnameParts = window.location.hostname.split('.');
            const domains = [
                window.location.hostname,
                `.${window.location.hostname}`
            ];

            if (hostnameParts.length > 2) {
                domains.push(`.${hostnameParts.slice(-2).join('.')}`);
            }

            domains.forEach((domain) => {
                document.cookie = `${cookieName}=; Max-Age=0; path=/; domain=${domain}; SameSite=Lax`;
            });
            document.cookie = `${cookieName}=; Max-Age=0; path=/; SameSite=Lax`;
        });
    }
    
    /**
     * Carga scripts de marketing si está permitido
     */
    loadMarketingScripts() {
        if (this.cookieOptions.marketing) {
            console.log('Loading marketing scripts...');
            
            // Ejemplo de carga de Facebook Pixel (comentado para evitar ejecución real):
            /*
            !function(f,b,e,v,n,t,s){
                if(f.fbq)return;
                n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s);
            }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'XXXXXXXXXXXXXXX'); // Reemplazar con el ID real
            fbq('track', 'PageView');
            */
        }
    }
}

// Iniciar el gestor de cookies cuando se cargue el DOM
document.addEventListener('DOMContentLoaded', () => {
    window.cookieManager = new CookieManager();
});
