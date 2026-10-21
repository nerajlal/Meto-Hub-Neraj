<aside id="adminSidebar" class="sidebar d-none d-md-flex flex-column h-100" style="padding-top:12px">
    <nav class="flex-grow-1 overflow-auto py-2" aria-label="Admin navigation">
        <ul class="list-unstyled mb-0 px-2 d-flex flex-column gap-1">
        @if(auth()->check() && auth()->user()->type === 'delivery_boy')
            <li><a href="{{ route('delivery.dashboard', request()->route('tenant') ?? session('active_tenant_id') ?? 1) }}" class="sidebar-item {{ request()->routeIs('delivery.dashboard', 'delivery.orders.*') ? 'active' : '' }}"><i class="fa-solid fa-list-check" aria-hidden="true"></i><span>My Deliveries</span></a></li>
        @else
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house" aria-hidden="true"></i><span>Home</span></a></li>
            <li><a href="{{ route('admin.orders') }}" class="sidebar-item {{ request()->routeIs('admin.orders', 'admin.orders.*') ? 'active' : '' }}"><i class="fa-solid fa-inbox" aria-hidden="true"></i><span>Orders</span>@if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)<span class="ms-auto badge rounded-pill">{{ $pendingOrdersCount }}</span>@endif</a></li>
            <li>
                @php($catalogActive = request()->routeIs('admin.products*', 'admin.collections*', 'admin.bundles*'))
                <details class="sidebar-catalog" @if($catalogActive) open @endif>
                    <summary class="sidebar-item {{ $catalogActive ? 'active' : '' }}"><i class="fa-solid fa-tags" aria-hidden="true"></i><span>Products</span><span class="sidebar-catalog-chevron ms-auto" aria-hidden="true">⌄</span></summary>
                    <ul class="sidebar-catalog-links list-unstyled">
                    @foreach(['admin.products' => 'All products', 'admin.collections' => 'Collections', 'admin.bundles' => 'Bundles'] as $destination => $label)
                        <li><a href="{{ route($destination) }}" class="{{ request()->routeIs($destination.'*') ? 'active' : '' }}" @if(request()->routeIs($destination.'*')) aria-current="page" @endif>{{ $label }}</a></li>
                    @endforeach
                    </ul>
                </details>
            </li>
            @foreach([
                ['admin.customers', 'Customers', 'user-group'],
                ['admin.carts', 'Carts', 'cart-shopping'],
                ['admin.discounts', 'Discounts', 'percent'],
                ['admin.analytics', 'Analytics', 'chart-simple'],
                ['admin.billing', 'Billing', 'file-invoice-dollar'],
                ['admin.settings.index', 'Settings', 'sliders']
            ] as [$destination, $label, $icon])
                @php($active = $destination === 'admin.settings.index' ? request()->routeIs('admin.settings.*', 'admin.delivery-staff.*', 'admin.notifications.*') : request()->routeIs($destination.'*'))
                <li><a href="{{ route($destination) }}" class="sidebar-item {{ $active ? 'active' : '' }}" @if($active) aria-current="{{ request()->routeIs($destination) ? 'page' : 'true' }}" @endif><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></a></li>
            @endforeach
        @endif
        </ul>
    </nav>
</aside>
<style>
#adminSidebar .sidebar-catalog summary{cursor:pointer;list-style:none}
#adminSidebar .sidebar-catalog summary::-webkit-details-marker{display:none}
#adminSidebar .sidebar-catalog-chevron{font-size:16px;transition:transform .15s}
#adminSidebar .sidebar-catalog[open] .sidebar-catalog-chevron{transform:rotate(180deg)}
#adminSidebar .sidebar-catalog-links{padding:4px 0 4px 44px;margin:0}
#adminSidebar .sidebar-catalog-links a{display:block;padding:10px 12px;border-radius:6px;font-size:12px;color:rgba(255,255,255,.8);text-decoration:none}
#adminSidebar .sidebar-catalog-links a:hover,#adminSidebar .sidebar-catalog-links a.active{color:#fff;background:rgba(0,0,0,.12)}
#adminSidebar :is(a,summary):focus-visible{outline:2px solid #fff;outline-offset:-3px}
</style>
