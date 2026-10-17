(() => {
 'use strict';
 const configNode=document.getElementById('merchant-notifications-config');if(!configNode)return;
 let config;try{config=JSON.parse(configNode.textContent);}catch(_){return;}
 const badge=document.getElementById('merchant-unread-count'),items=document.getElementById('merchant-notification-items');
 const status=document.getElementById('merchant-push-status'),enable=document.getElementById('merchant-enable-push'),disable=document.getElementById('merchant-disable-push');
 let stopped=false,timer=null,first=true,lastId=0,busy=false;
 const setStatus=text=>{if(status)status.textContent=text;};
 document.querySelectorAll('[data-notification-time]').forEach(node=>{const date=new Date(node.dataset.notificationTime);if(!Number.isNaN(date.getTime()))node.textContent=new Intl.DateTimeFormat(undefined,{dateStyle:'medium',timeStyle:'short'}).format(date);});
 async function request(url,body=null,method='POST') {
  const response=await fetch(url,{method:body===null&&method==='POST'?'GET':method,credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json','X-CSRF-TOKEN':config.csrf,...(body!==null?{'Content-Type':'application/json'}:{})},...(body!==null?{body:JSON.stringify(body)}:{})});
  let data={};try{data=await response.json();}catch(_){}
  if(!response.ok){if([401,403,419].includes(response.status))stopped=true;throw new Error(data.message||'Unable to update notifications. Please try again.');}
  return data;
 }
 function toast(message){
  let panel=document.getElementById('merchant-order-toast');if(!panel){panel=document.createElement('a');panel.id='merchant-order-toast';panel.href=config.index;panel.className='alert alert-success shadow position-fixed';panel.style.cssText='right:16px;bottom:24px;z-index:1080;max-width:calc(100vw - 32px);text-decoration:none';panel.setAttribute('role','status');document.body.appendChild(panel);}
  panel.textContent=message;panel.hidden=false;setTimeout(()=>{panel.hidden=true;},7000);
 }
 async function poll(){
  clearTimeout(timer);if(stopped)return;
  try{
   const data=await request(config.feed);if(badge){badge.textContent=data.unread_count>99?'99+':String(data.unread_count);badge.hidden=!data.unread_count;}
   if(items){items.replaceChildren();if(!data.alerts.length){const empty=document.createElement('div');empty.className='p-3 small text-muted';empty.textContent='No order notifications yet.';items.appendChild(empty);}
    for(const alert of data.alerts){const link=document.createElement('a');link.className='dropdown-item py-2 border-bottom text-wrap'+(alert.unread?' bg-primary bg-opacity-10':'');link.href=alert.url;link.dataset.orderAlert=alert.id;const title=document.createElement('strong');title.className='d-block small text-dark';title.textContent=alert.title;const time=document.createElement('span');time.className='small text-muted';time.textContent=alert.time;link.append(title,time);items.appendChild(link);}
   }
   const newest=data.alerts[0]?.id||0;
   if(!first&&newest>lastId&&data.alerts.some(alert=>alert.id>lastId&&alert.unread)&&!document.hidden)toast('New order received. Open notifications to review.');
   lastId=Math.max(lastId,newest);first=false;
  }catch(error){if(items&&first)items.textContent=stopped?'Sign in again to view notifications.':'Notifications are temporarily unavailable.';}
  if(!stopped)timer=setTimeout(poll,document.hidden?60000:20000);
 }
 document.addEventListener('visibilitychange',()=>{if(!document.hidden)poll();});
 navigator.serviceWorker?.addEventListener('message',event=>{if(event.data?.type==='merchant-order-notification')poll();});
 document.querySelectorAll('[data-mark-notifications-read]').forEach(button=>button.addEventListener('click',async()=>{
  button.disabled=true;try{await request(config.readAll,{});document.querySelectorAll('[data-alert-read-badge]').forEach(b=>{b.className='badge bg-secondary';b.textContent='Read';});await poll();}catch(error){toast(error.message);}finally{button.disabled=false;}
 }));
 document.addEventListener('click',event=>{
  const link=event.target.closest?.('a[data-order-alert]');if(!link||event.defaultPrevented||event.ctrlKey||event.metaKey||event.shiftKey||event.button!==0)return;
  event.preventDefault();const id=link.dataset.orderAlert;
  request(`${config.base}/${id}/read`,{}).catch(()=>{}).finally(()=>{location.href=link.href;});
 });
 const fromAlert=new URL(location.href).searchParams.get('notification_alert');
 if(fromAlert&&/^[0-9]+$/.test(fromAlert))request(`${config.base}/${fromAlert}/read`,{}).then(()=>{const url=new URL(location.href);url.searchParams.delete('notification_alert');history.replaceState(history.state,'',url.href);poll();}).catch(()=>{});
 const isIOS=()=>/iPad|iPhone|iPod/.test(navigator.userAgent)||(navigator.platform==='MacIntel'&&navigator.maxTouchPoints>1);
 const installed=()=>window.matchMedia('(display-mode: standalone)').matches||navigator.standalone===true;
 const supported=()=>window.isSecureContext&&'serviceWorker'in navigator&&'PushManager'in window&&'Notification'in window;
 function keyBytes(key){const padding='='.repeat((4-key.length%4)%4),raw=atob((key+padding).replace(/-/g,'+').replace(/_/g,'/'));return Uint8Array.from(raw,c=>c.charCodeAt(0));}
 async function registration(){return navigator.serviceWorker.getRegistration(config.scope);}
 async function removeThisDevice(){
  if(!supported())return;const reg=await registration();const sub=await reg?.pushManager.getSubscription();if(!sub)return;
  try{await request(config.unsubscribe,{endpoint:sub.endpoint});}finally{await sub.unsubscribe();}
 }
 async function checkDevice(){
  if(!status)return;
  if(isIOS()&&!installed()){setStatus('On iPhone/iPad, add this page to your Home Screen and open it there to enable notifications.');if(enable)enable.disabled=true;return;}
  if(!supported()){setStatus('Device alerts need HTTPS (or localhost) and a browser that supports Web Push. Email and the notification bell still work.');if(enable)enable.disabled=true;return;}
  if(!config.pushConfigured||!config.publicKey){setStatus('Web Push is not configured on the server yet.');return;}
  if(Notification.permission==='denied'){setStatus('Notifications are blocked. Allow them in your browser/site settings, then return here.');return;}
  try{const reg=await registration(),sub=await reg?.pushManager.getSubscription();if(sub){const data=await request(config.deviceStatus,{endpoint:sub.endpoint});setStatus(data.enabled?'Device alerts are enabled for this store.':'This device is not connected to this store. Click Enable to connect it.');}else setStatus('Click Enable to allow order alerts on this device.');}catch(error){setStatus(error.message);}
 }
 enable?.addEventListener('click',async()=>{
  if(busy)return;if(!supported()){setStatus('This browser does not support device alerts here.');return;}if(isIOS()&&!installed()){setStatus('Add this page to the Home Screen, then open the installed app.');return;}
  busy=true;enable.disabled=true;
  try{
   // Request permission directly from this user gesture, before network/registration awaits.
   const permission=await Notification.requestPermission();if(permission!=='granted')throw new Error('Permission was not granted. You can change it in browser settings.');
   const reg=await navigator.serviceWorker.register(config.worker,{scope:config.scope});
   if(!reg.active)await new Promise((resolve,reject)=>{const worker=reg.installing||reg.waiting;if(!worker){reject(new Error('Notification worker is not ready. Try again.'));return;}const timeout=setTimeout(()=>reject(new Error('Notification worker activation timed out. Try again.')),15000);worker.addEventListener('statechange',()=>{if(worker.state==='activated'){clearTimeout(timeout);resolve();}});});
   let sub=await reg.pushManager.getSubscription();
   const wanted=keyBytes(config.publicKey);
   if(sub?.options?.applicationServerKey){const current=new Uint8Array(sub.options.applicationServerKey);if(current.length!==wanted.length||current.some((v,i)=>v!==wanted[i])){await sub.unsubscribe();sub=null;}}
   sub=sub||await reg.pushManager.subscribe({userVisibleOnly:true,applicationServerKey:wanted});
   await request(config.subscribe,sub.toJSON());setStatus('Device alerts enabled. New orders will appear in your notification bar.');
  }catch(error){setStatus(error.message||'Could not enable device alerts. Please retry.');}finally{busy=false;enable.disabled=false;}
 });
 disable?.addEventListener('click',async()=>{if(busy)return;busy=true;disable.disabled=true;try{await removeThisDevice();setStatus('Device alerts disabled on this browser.');}catch(error){setStatus(error.message);}finally{busy=false;disable.disabled=false;}});
 document.addEventListener('submit',event=>{
  const form=event.target;if(!(form instanceof HTMLFormElement)||!new URL(form.action,location.href).pathname.endsWith('/logout'))return;
  event.preventDefault();removeThisDevice().catch(()=>{}).finally(()=>form.submit());
 });
 poll();checkDevice();
})();
