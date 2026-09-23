<aside id="adminSidebar" class="sidebar d-none d-md-flex flex-column h-100" style="padding-top: 12px;">

    <nav class="flex-grow-1 overflow-auto py-2">
        <ul class="list-unstyled mb-0 px-2 d-flex flex-column gap-1">
            @if(auth()->check() && auth()->user()->type === 'delivery_boy')
                <li>
                    <a href="{{ route('delivery.dashboard', request()->route('tenant') ?? session('active_tenant_id') ?? 1) }}" class="sidebar-item {{ request()->routeIs('delivery.dashboard') || request()->routeIs('delivery.orders.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i>
                        <span>My Deliveries</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span>Home</span>
                    </a>
                </li>
            <li>
                <a href="{{ route('admin.orders') }}" class="sidebar-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <i class="fa-solid fa-inbox"></i>
                    <span>Orders</span>
                    @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                        <span class="ms-auto badge rounded-pill">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.collections') }}" class="sidebar-item {{ request()->routeIs('admin.collections*') ? 'active' : '' }}">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Collections</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.products') }}" class="sidebar-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bundles') }}" class="sidebar-item {{ request()->routeIs('admin.bundles*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box-archive"></i>
                    <span>Bundles</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers') }}" class="sidebar-item {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.carts') }}" class="sidebar-item {{ request()->routeIs('admin.carts') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Carted Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.analytics') }}" class="sidebar-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.discounts') }}" class="sidebar-item {{ request()->routeIs('admin.discounts*') ? 'active' : '' }}">
                    <i class="fa-solid fa-percent"></i>
                    <span>Discounts</span>
                </a>
            </li>
            <!-- <li>
                <a href="{{ route('admin.reviews') }}" class="sidebar-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i>
                    <span>Reviews</span>
                    <i class="fas fa-crown text-warning ms-auto" style="font-size: 10px;" title="Premium Feature"></i>
                </a>
            </li> -->

            <li>
                <a href="{{ route('admin.billing') }}" class="sidebar-item {{ request()->routeIs('admin.billing*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Billing</span>
                </a>
            </li>

            <li class="sidebar-heading">
                Settings
            </li>
            <li>
                 <a href="{{ route('admin.settings.slider') }}" class="sidebar-item {{ request()->routeIs('admin.settings.slider') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i>
                    <span>Hero Slider</span>
                </a>
            <!-- <li>
                 <a href="{{ route('admin.settings.home-products') }}" class="sidebar-item {{ request()->routeIs('admin.settings.home-products*') ? 'active' : '' }}">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>Home Products</span>
                </a>
            </li> -->
            <li>
                 <a href="{{ route('admin.settings.delivery-partners.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.delivery-partners*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>Delivery Partners</span>
                </a>
            </li>
            <li>
                 <a href="{{ route('admin.delivery-staff.index') }}" class="sidebar-item {{ request()->routeIs('admin.delivery-staff*') ? 'active' : '' }}">
                    <i class="fa-solid fa-motorcycle"></i>
                    <span>Delivery Staff</span>
                </a>
            </li>
             <li>
                  <a href="{{ route('admin.settings.theme') }}" class="sidebar-item {{ request()->routeIs('admin.settings.theme*') ? 'active' : '' }}">
                     <i class="fa-solid fa-palette"></i>
                     <span>Theme Selection</span>
                 </a>
             </li>
             @if(isset($currentTenant) && in_array(strtolower($currentTenant->theme ?? ''), ['editorial_cream', 'modern_minimal', 'v1', 'v4', 'ajmal', 'nurah']))
             <li>
                  <a href="{{ route('admin.settings.videos') }}" class="sidebar-item {{ request()->routeIs('admin.settings.videos*') ? 'active' : '' }}">
                     <i class="fa-solid fa-video"></i>
                     <span>Showcase Videos</span>
                 </a>
             </li>
             @endif
             <li>
                  <a href="{{ route('admin.settings.domain') }}" class="sidebar-item {{ request()->routeIs('admin.settings.domain*') ? 'active' : '' }}">
                     <i class="fa-solid fa-globe"></i>
                     <span>Domain Connection</span>
                 </a>
             </li>
             <li>
                  <a href="{{ route('admin.settings.payment') }}" class="sidebar-item {{ request()->routeIs('admin.settings.payment*') ? 'active' : '' }}">
                     <i class="fa-solid fa-credit-card"></i>
                     <span>Payment Gateways</span>
                 </a>
             </li>
             <li>
                  <a href="{{ route('admin.settings.storefront') }}" class="sidebar-item {{ request()->routeIs('admin.settings.storefront*') ? 'active' : '' }}">
                     <i class="fa-solid fa-file-lines"></i>
                     <span>Storefront Pages</span>
                 </a>
             </li>
             <li>
                  <a href="{{ route('admin.settings.checkout-fields') }}" class="sidebar-item {{ request()->routeIs('admin.settings.checkout-fields*') ? 'active' : '' }}">
                     <i class="fa-solid fa-cash-register"></i>
                     <span>Custom Checkout</span>
                 </a>
                </li>
            @endif
        </ul>

    </nav>
</aside>
