self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  clients.claim();
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
  event.waitUntil(self.registration.showNotification(title, options));
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
