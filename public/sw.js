const CACHE_NAME = 'conocia-v1';
const OFFLINE_URL = '/offline.html';

// Assets estáticos que siempre se cachean al instalar
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/favicon/android-chrome-192x192.png',
    '/favicon/android-chrome-512x512.png',
];

// ─── Instalación ────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// ─── Activación ─────────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// ─── Fetch ───────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Solo interceptar peticiones GET del mismo origen
    if (request.method !== 'GET' || url.origin !== location.origin) return;

    // Ignorar admin, api, rutas de artisan y feed
    if (['/admin', '/api', '/_debugbar', '/feed'].some(p => url.pathname.startsWith(p))) return;

    // Archivos estáticos (CSS, JS, imágenes, fuentes) → cache first
    if (url.pathname.match(/\.(css|js|woff2?|ttf|eot|png|jpg|jpeg|gif|svg|ico|webp)$/)) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Páginas HTML → network first, cache fallback, offline como último recurso
    if (request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(cached => cached || caches.match(OFFLINE_URL))
                )
        );
    }
});

// ─── Push Notifications ─────────────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;

    let data = {};
    try { data = event.data.json(); } catch { data = { title: 'ConocIA', body: event.data.text() }; }

    const options = {
        body: data.body || 'Nueva noticia disponible',
        icon: '/favicon/android-chrome-192x192.png',
        badge: '/favicon/android-chrome-192x192.png',
        image: data.image || null,
        data: { url: data.url || '/' },
        actions: [
            { action: 'open', title: 'Leer ahora' },
            { action: 'close', title: 'Cerrar' },
        ],
        vibrate: [200, 100, 200],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'ConocIA', options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    if (event.action === 'close') return;

    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            const existing = list.find(c => c.url === url && 'focus' in c);
            if (existing) return existing.focus();
            return clients.openWindow(url);
        })
    );
});
