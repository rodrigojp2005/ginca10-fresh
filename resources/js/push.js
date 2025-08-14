async function registerServiceWorker() {
  if (!('serviceWorker' in navigator)) return null;
  try {
  const swVersion = 'v2025-08-14-1';
    const reg = await navigator.serviceWorker.register('/sw.js?ver=' + swVersion);
    // Se houver SW aguardando, peça para pular waiting
    if (reg.waiting) {
      reg.waiting.postMessage({ type: 'SKIP_WAITING' });
    }
    reg.addEventListener('updatefound', () => {
      const nw = reg.installing;
      nw?.addEventListener('statechange', () => {
        if (nw.state === 'installed' && navigator.serviceWorker.controller) {
          reg.waiting?.postMessage({ type: 'SKIP_WAITING' });
        }
      });
    });
    return reg;
  } catch (e) {
    console.error('SW registration failed', e);
    return null;
  }
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  const rawData = atob(base64);
  const outputArray = new Uint8Array(rawData.length);
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

export async function initPush() {
  const vapidPublicKey = import.meta.env.VITE_VAPID_PUBLIC_KEY || window.APP_VAPID_KEY;
  if (!vapidPublicKey) { console.warn('VAPID public key ausente'); return; }
  const reg = await registerServiceWorker();
  if (!reg) return;
  let permission = Notification.permission;
  if (permission === 'default') permission = await Notification.requestPermission();
  if (permission !== 'granted') return;
  const existing = await reg.pushManager.getSubscription();
  // Se a VAPID mudou desde a última vez, re-subscreve para garantir compatibilidade
  const lastVapid = localStorage.getItem('vapid_pub');
  if (lastVapid !== vapidPublicKey && existing) {
    try { await existing.unsubscribe(); } catch (e) { /* noop */ }
  }
  if (await reg.pushManager.getSubscription()) {
    // Já inscrito e VAPID inalterado
    return;
  }
  const subscription = await reg.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
  });
  localStorage.setItem('vapid_pub', vapidPublicKey);
  // send to server
  await fetch('/push/subscribe', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
    body: JSON.stringify(subscription)
  });
}

// Auto init: se o load já ocorreu, executa; caso contrário, aguarda o load
const maybeStart = () => {
  if (window.LaravelIsAuthenticated) initPush();
};
if (document.readyState === 'complete') {
  setTimeout(maybeStart, 0);
} else {
  window.addEventListener('load', maybeStart);
}
