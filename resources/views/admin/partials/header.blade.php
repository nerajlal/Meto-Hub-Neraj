<header class="navbar navbar-expand sticky-top px-4 py-2 flex-shrink-0" style="height: 48px; background-color: #1a1a1a; border-bottom: none; color: #ffffff;">
    <button class="btn btn-link p-1 d-md-none me-3" onclick="toggleSidebar()" style="color: #ffffff !important;"><i class="fas fa-bars"></i></button>
    
    <!-- Shopify Brand Logo in Top Left -->
    <div class="d-flex align-items-center me-4 d-none d-md-flex">
        <!-- <i class="fa-brands fa-shopify text-white fs-4 me-2"></i> -->
        <span class="fw-bold text-white small" style="letter-spacing: -0.5px; font-size: 14px;">SLOT STORE</span>
    </div>

    <!-- Centered Search Box -->
    <div class="d-flex align-items-center justify-content-center flex-grow-1" style="max-width: 480px; margin: 0 auto;">
        <div class="input-group border rounded-3" style="background-color: #303030; border-color: #4a4a4a !important; overflow: hidden; height: 32px; max-width: 380px;">
            <span class="input-group-text bg-transparent border-0 text-white-50 pe-1" style="padding-top: 4px;"><i class="fas fa-search small"></i></span>
            <input type="text" class="form-control bg-transparent border-0 shadow-none ps-2" placeholder="Search" style="font-size: 13px; color: #ffffff; padding: 2px 0;">
            <span class="input-group-text bg-transparent border-0 text-white-50 px-2" style="font-size: 10px; cursor: pointer; opacity: 0.7;">⌘ K</span>
        </div>
    </div>

    <!-- Right Side Profile and Settings -->
    <div class="d-flex align-items-center gap-3 ms-auto">
        <a href="{{ $storefrontUrl }}" target="_blank" class="btn btn-sm d-none d-sm-flex align-items-center gap-2" style="background-color: #303030; border: 1px solid #4a4a4a; color: #ffffff; font-size: 11px; font-weight: 500; border-radius: 6px; padding: 4px 10px;">
            <i class="fas fa-external-link-alt" style="font-size: 9px;"></i>
            View Store
        </a>
        @php
            $tenantId = session('active_tenant_id') ?? auth()->user()->tenant_id ?? 1;
            
            $pendingOrdersCount = \App\Models\Order::where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->count();

            $outOfStockCount = \App\Models\ProductVariant::whereHas('product', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->where('status', 'active')->where('continue_selling_when_out_of_stock', false);
                })
                ->where('stock', '<=', 0)
                ->count();
                
            $hasNotifications = $pendingOrdersCount > 0 || $outOfStockCount > 0;
        @endphp
        
        <div class="dropdown">
            <button class="btn btn-link p-1 position-relative text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" style="color: #cccccc !important;">
                <i class="fas fa-bell"></i>
                @if($hasNotifications)
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-dark rounded-circle" style="margin-top: 5px; margin-left: -5px;">
                    <span class="visually-hidden">New alerts</span>
                </span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="width: 280px; font-size: 13px;">
                <li class="px-3 py-2 border-bottom">
                    <span class="fw-bold text-dark">Notifications</span>
                </li>
                
                @if(!$hasNotifications)
                <li class="px-3 py-4 text-center text-muted">
                    <i class="fas fa-bell-slash fs-4 mb-2 opacity-25"></i>
                    <p class="mb-0 small">No new notifications</p>
                </li>
                @else
                    @if($pendingOrdersCount > 0)
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center border-bottom" href="{{ route('admin.orders', ['status' => 'pending']) }}">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $pendingOrdersCount }} Pending Order(s)</div>
                                <div class="text-muted" style="font-size: 11px;">You have orders to process.</div>
                            </div>
                        </a>
                    </li>
                    @endif

                    @if($outOfStockCount > 0)
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('admin.products', ['status' => 'active']) }}">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="fw-medium text-dark">{{ $outOfStockCount }} Out of Stock</div>
                                <div class="text-muted" style="font-size: 11px;">Active variants need restocking.</div>
                            </div>
                        </a>
                    </li>
                    @endif
                @endif
            </ul>
        </div>
        
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex align-items-center justify-content-center rounded text-dark fw-bold" style="width: 28px; height: 28px; background-color: #00d2c4; font-size: 11px;">{{ strtoupper(substr($currentTenant->name ?? auth()->user()->name ?? 'DR', 0, 2)) }}</div>
                <span class="small fw-medium text-white-50 d-none d-sm-block" style="font-size: 12px;">{{ $currentTenant->name ?? 'Deity & Relics' }}</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li><a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-edit me-2 text-muted"></i> Edit Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item small text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="admin-logout-form" action="{{ route('admin.logout', ['tenant' => $currentTenant->id ?? auth()->user()->tenant_id]) }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
    <script src="{{ asset('js/admin-sidebar-toggle.js') }}" defer></script>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="profileModalLabel">Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="text-center mb-4 mt-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-user-edit text-success fs-4"></i>
                    </div>
                    <p class="text-muted small">Update your account details below.</p>
                </div>
                
                <form class="vstack gap-3">
                    <div>
                        <label for="profile_name" class="form-label small fw-medium text-secondary">Name</label>
                        <input type="text" class="form-control" id="profile_name" value="{{ auth()->user()->name ?? '' }}">
                    </div>
                    <div>
                        <label for="profile_email" class="form-label small fw-medium text-secondary">Email</label>
                        <input type="email" class="form-control" id="profile_email" value="{{ auth()->user()->email ?? '' }}">
                    </div>
                    <div>
                        <label for="password" class="form-label small fw-medium text-secondary">New Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Leave blank to keep current">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light py-2">
                <button type="button" class="btn btn-white border text-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm text-white">Save Changes</button>
            </div>
        </div>
    </div>
</div>
