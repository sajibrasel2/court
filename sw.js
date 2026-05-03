const CACHE_NAME = 'amar-mamla-v3';

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    // API calls: network first
    if (event.request.url.includes('api.php')) {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
        return;
    }
    // All other requests: network first, fallback to cache
    event.respondWith(
        fetch(event.request).then(response => {
            const clone = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
            return response;
        }).catch(() => caches.match(event.request))
    );
});

// Push notification handler
self.addEventListener('push', event => {
    let data = { title: 'আমার মামলা', body: 'আপনার নতুন নোটিফিকেশন আছে', icon: 'icon-192.png' };
    if (event.data) {
        try { data = event.data.json(); } catch(e) {}
    }
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon || 'icon-192.png',
            badge: 'icon-192.png',
            vibrate: [200, 100, 200],
            tag: 'case-reminder',
            data: { url: data.url || '/' }
        })
    );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        self.clients.matchAll({ type: 'window' }).then(clients => {
            for (const client of clients) {
                if ('focus' in client) return client.focus();
            }
            return self.clients.openWindow(event.notification.data.url || '/');
        })
    );
});
