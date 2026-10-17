/* Run the actual notification worker with browser APIs mocked; no real push or OS alert is sent. */
(async () => {
 const assert=(ok,message)=>{if(!ok)throw new Error(message);};
 const handlers={},notifications=[],messages=[],opened=[];let installed=false,claimed=false;
 const browser={url:'https://app.example/1/admin/orders',async navigate(url){this.url=url;},async focus(){return this;},postMessage(data){messages.push(data);}};
 const worker={async skipWaiting(){installed=true;},location:{origin:'https://app.example'},addEventListener:(name,callback)=>{handlers[name]=callback;},
  registration:{async showNotification(title,options){notifications.push({title,options});}},
  clients:{async claim(){claimed=true;},async matchAll(){return[browser];},async openWindow(url){opened.push(url);}}};
 new Function('self',globalThis.merchantWorkerSource)(worker);
 let pending;
 handlers.install({waitUntil:promise=>{pending=promise;}});await pending;
 handlers.activate({waitUntil:promise=>{pending=promise;}});await pending;
 assert(installed&&claimed,'Worker should activate promptly and control the admin scope');
 handlers.push({data:{json:()=>({title:'Private Buyer',body:'private@example.com',url:'/1/admin/notifications/7/open',tag:'order-7'})},waitUntil:promise=>{pending=promise;}});await pending;
 assert(notifications.length===1,'Push must display a notification');
 assert(!JSON.stringify(notifications).includes('private@example.com')&&!JSON.stringify(notifications).includes('Private Buyer'),'Lock screen must not expose customer data');
 assert(messages[0].type==='merchant-order-notification','Foreground bell must be refreshed');
 assert(notifications[0].options.data.path==='/1/admin/notifications/7/open','Click must retain the authenticated order route');
 handlers.notificationclick({notification:{close(){},data:notifications[0].options.data},waitUntil:promise=>{pending=promise;}});await pending;
 assert(browser.url==='https://app.example/1/admin/notifications/7/open'&&opened.length===0,'Existing store window should be reused');
 handlers.push({data:{json:()=>({url:'https://attacker.example/steal'})},waitUntil:promise=>{pending=promise;}});await pending;
 assert(notifications[1].options.data.path==='/login','External payload links must not be opened');
 handlers.notificationclick({notification:{close(){},data:{path:'https://attacker.example/steal'}},waitUntil(){throw new Error('Unsafe target was accepted');}});
 handlers.push({data:{json(){throw new Error('Invalid JSON');}},waitUntil:promise=>{pending=promise;}});await pending;
 assert(notifications.length===3,'Malformed pushes must still produce a generic visible notification');
 document.body.textContent='PASS: worker privacy, authenticated order links, safe click targets and foreground refresh';
})().catch(error=>{document.body.textContent='FAIL: '+error.stack;});
