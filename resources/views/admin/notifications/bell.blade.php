<div class="dropdown" id="merchant-notification-bell">
 <button class="btn btn-link p-1 position-relative text-white" type="button" data-bs-toggle="dropdown" aria-label="Order notifications" aria-expanded="false"><i class="fas fa-bell" aria-hidden="true"></i><span id="merchant-unread-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px" hidden>0</span></button>
 <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" style="width:min(340px,calc(100vw - 24px));max-height:75vh;overflow:auto">
 <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between gap-2"><strong class="text-dark">Order notifications</strong><button type="button" class="btn btn-link btn-sm p-0" data-mark-notifications-read>Mark all read</button></div>
 <div id="merchant-notification-items" aria-live="polite"><div class="p-3 small text-muted">Loading notifications…</div></div>
 @if(($pendingOrdersCount ?? 0)>0)<a class="dropdown-item border-top py-2 small" href="{{ route('admin.orders',['status'=>'pending']) }}">{{ $pendingOrdersCount }} pending order(s)</a>@endif
 @if(($outOfStockCount ?? 0)>0)<a class="dropdown-item border-top py-2 small" href="{{ route('admin.products',['status'=>'active']) }}">{{ $outOfStockCount }} out-of-stock variant(s)</a>@endif
 <a class="dropdown-item text-primary border-top py-2 small" href="{{ route('admin.notifications.index') }}">View all &amp; device settings</a>
 </div>
</div>
