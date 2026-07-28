<aside id="adminSidebar" class="sidebar d-none d-md-flex flex-column bg-white border-end h-100">
    <div class="p-3 d-flex align-items-center justify-content-between border-bottom" style="height: 64px;">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center rounded bg-dark text-white fw-bold" style="width: 32px; height: 32px; font-size: 14px;">SA</div>
            <span class="fw-semibold text-secondary">Super Admin</span>
        </div>
    </div>

    <nav class="flex-grow-1 overflow-auto py-2">
        <ul class="list-unstyled mb-0 px-2 d-flex flex-column gap-1">
            <!-- Core section -->
            <li class="px-3 pt-2 pb-1 text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                Overview
            </li>
            <li>
                <a href="{{ route('super_admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-chart-line text-center" style="width: 20px;"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Tenant Management -->
            <li class="px-3 pt-3 pb-1 text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                Tenant Management
            </li>
            <li>
                <a href="{{ route('super_admin.tenants') }}" class="sidebar-item {{ request()->routeIs('super_admin.tenants') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-store text-center" style="width: 20px;"></i>
                    <span>All Stores</span>
                </a>
            </li>


            <!-- Platform Data -->
            <li class="px-3 pt-3 pb-1 text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                Platform Data
            </li>
            <li>
                <a href="{{ route('super_admin.orders') }}" class="sidebar-item {{ request()->routeIs('super_admin.orders') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-shopping-bag text-center" style="width: 20px;"></i>
                    <span>Global Orders</span>
                </a>
            </li>
            <li>
                <a href="{{ route('super_admin.customers') }}" class="sidebar-item {{ request()->routeIs('super_admin.customers') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-users text-center" style="width: 20px;"></i>
                    <span>Global Customers</span>
                </a>
            </li>

            <!-- System Settings -->
            <li class="px-3 pt-3 pb-1 text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                System & Config
            </li>
            <!-- <li>
                <a href="{{ route('super_admin.plans') }}" class="sidebar-item {{ request()->routeIs('super_admin.plans') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-sliders-h text-center" style="width: 20px;"></i>
                    <span>SaaS Plans</span>
                </a>
            </li> -->
            <li>
                <a href="{{ route('super_admin.global_images') }}" class="sidebar-item {{ request()->routeIs('super_admin.global_images') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-images text-center" style="width: 20px;"></i>
                    <span>Global Images</span>
                </a>
            </li>
            <li>
                <a href="{{ route('super_admin.status') }}" class="sidebar-item {{ request()->routeIs('super_admin.status') ? 'active' : '' }} d-flex align-items-center gap-3 px-3 py-2 rounded text-decoration-none text-secondary small">
                    <i class="fas fa-server text-center" style="width: 20px;"></i>
                    <span>System Status</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="p-3 border-top">
         <form method="POST" action="{{ route('super_admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>
