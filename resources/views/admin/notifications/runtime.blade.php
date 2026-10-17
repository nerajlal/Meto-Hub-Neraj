@if(auth()->check() && auth()->user()->type === 'admin' && isset($currentTenant))
<script id="merchant-notifications-config" type="application/json">{!! json_encode([
 'tenant'=>(string)$currentTenant->id,'user'=>(string)auth()->id(),'feed'=>route('admin.notifications.feed'),'index'=>route('admin.notifications.index'),
 'readAll'=>route('admin.notifications.read-all'),'base'=>url('/'.$currentTenant->id.'/admin/notifications'),
 'scope'=>rtrim(url('/'.$currentTenant->id.'/admin'),'/').'/', 'deviceStatus'=>route('admin.notifications.device-status'),
 'subscribe'=>route('admin.notifications.subscribe'),'unsubscribe'=>route('admin.notifications.unsubscribe'),
 'pushConfigured'=>app(\App\Services\MerchantWebPush::class)->configured(),
 'publicKey'=>config('order_notifications.vapid.public_key'),'worker'=>url('/merchant-push-worker.js'),
 'csrf'=>csrf_token(),
],JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES) !!}</script>
<script src="{{ asset('js/merchant-notifications.js') }}?v={{ is_file(public_path('js/merchant-notifications.js')) ? filemtime(public_path('js/merchant-notifications.js')) : 1 }}" defer></script>
@endif
