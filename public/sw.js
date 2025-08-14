const VERSION = 'v2025-08-14-1';

self.addEventListener('install', event => {
  // Força a ativação imediata do SW atualizado
  console.log('[SW] install', VERSION);
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  console.log('[SW] activate', VERSION);
  // Garante que o SW controle todas as abas imediatamente
  event.waitUntil(clients.claim());
});

self.addEventListener('push', event => {
  let data = {};
  if (event.data) {
    try { data = event.data.json(); } catch(e){ data = {body: event.data.text()}; }
  }
  const title = data.title || 'Nova notificação';
  const body = data.body || '';
  const options = {
    body,
    icon: '/favicon.ico',
    data: data.data || {}
  };
  event.waitUntil(Promise.all([
    self.registration.showNotification(title, options),
    // avisa todas as abas para recarregar contadores
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
      list.forEach(c => c.postMessage({ type: 'NOTIFICATIONS_UPDATED' }));
    })
  ]));
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  const targetUrl = '/gincana/' + (event.notification.data?.gincana_id || '');
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (let client of windowClients) {
        if ('focus' in client) return client.focus();
      }
      if (clients.openWindow) return clients.openWindow(targetUrl);
    })
  );
});

// Fetch handler pass-through (necessário para critérios de instalabilidade em alguns navegadores)
self.addEventListener('fetch', event => {
  // Não intercepta; apenas passa adiante a requisição
  event.respondWith(fetch(event.request));
});

// Suporta pular a fase de waiting quando a página solicitar
self.addEventListener('message', (event) => {
  if (!event.data) return;
  if (event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
