/* Notification-only worker: no fetch handler, offline page cache or third-party scripts. */
self.addEventListener('install', event => event.waitUntil(self.skipWaiting()));
self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));
self.addEventListener('push', event => {
 let payload={};try {payload=event.data?.json() || {};}catch(_) {}
 const path=typeof payload.url==='string' && /^\/[0-9]+\/admin\/notifications\/[0-9]+\/open$/.test(payload.url) ? payload.url : '/login';
 event.waitUntil(Promise.all([self.registration.showNotification('MetoHub · New order',{
  body:'Your store has received a new order. Sign in to review it.',tag:typeof payload.tag==='string'?payload.tag.slice(0,100):'metohub-order',
  icon:'/merchant-icon-192.png',badge:'/merchant-badge-96.png',data:{path},renotify:false,
 }),self.clients.matchAll({type:'window'}).then(clients=>clients.forEach(client=>client.postMessage({type:'merchant-order-notification'})))]));
});
self.addEventListener('notificationclick', event => {
 event.notification.close();
 const path=event.notification.data?.path || '/login';
 const target=new URL(path,self.location.origin);
 if (target.origin!==self.location.origin) return;
 event.waitUntil(self.clients.matchAll({type:'window',includeUncontrolled:true}).then(async clients=>{
  for (const client of clients) {
   if (new URL(client.url).origin===target.origin && new URL(client.url).pathname.startsWith(path.split('/admin/')[0]+'/admin')) {
    await client.navigate(target.href);return client.focus();
   }
  }
  return self.clients.openWindow(target.href);
 }));
});
