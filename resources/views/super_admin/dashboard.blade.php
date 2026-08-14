@extends('super_admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Super Admin Dashboard</h1>
        <p class="text-muted small">Manage your tenants, monitor storefront performance, and track overall SaaS analytics.</p>
    </div>
    <a href="{{ route('super_admin.create_tenant') }}" class="btn btn-primary btn-sm rounded-3 px-3">
        <i class="fas fa-plus me-2"></i> Create New Tenant
    </a>
</div>

<!-- SaaS Metrics Grid -->
<div class="row g-3 mb-4">
    <!-- Total Active Stores -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Stores</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalStores) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <i class="fas fa-store fa-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <span>Active storefronts</span>
            </div>
        </div>
    </div>

    <!-- Platform GMV / Revenue -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Platform GMV</span>
                    <h3 class="fw-bold text-dark mb-0">{{ $currentTenant->currency ?? '₹' }}{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                    <i class="fas fa-indian-rupee-sign fa-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <span>Total platform sales</span>
            </div>
        </div>
    </div>

    <!-- Total Platform Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Orders</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <span>Orders across all tenants</span>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Customers</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalCustomers) }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                    <i class="fas fa-users fa-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <span>Registered store customers</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Active Tenants Table -->
    <div class="col-12 col-xl-8">
        <div class="card border shadow-sm h-100 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-dark fw-bold">Active Tenants</h5>
                <span class="badge bg-light text-secondary border">{{ count($tenants) }} registered</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-3 py-2 border-0 fw-semibold">ID</th>
                            <th class="px-3 py-2 border-0 fw-semibold">Site Name</th>
                            <th class="px-3 py-2 border-0 fw-semibold">Admin Info</th>
                            <th class="px-3 py-2 border-0 fw-semibold">Plan</th>
                            <th class="px-3 py-2 border-0 fw-semibold">Theme</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                        <tr>
                            <td class="px-3 py-3 fw-semibold text-dark">#{{ $tenant->id }}</td>
                            <td class="px-3 py-3">
                                <div class="fw-bold mb-0" style="font-size: 14px;">
                                    <a href="{{ route('super_admin.tenants.show', $tenant->id) }}" class="text-decoration-none text-primary hover-underline">{{ $tenant->name ?? 'N/A' }}</a>
                                </div>
                                <div class="text-muted small" style="font-size: 11px;">{{ $tenant->domain }}</div>
                            </td>
                            <td class="px-3 py-3">
                                @if($tenant->admin)
                                    <div class="text-dark fw-semibold small">{{ $tenant->admin->name }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $tenant->admin->email }}</div>
                                @else
                                    <div class="text-muted fw-semibold small">No Admin Registered</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <span class="badge bg-success bg-opacity-10 text-success small text-capitalize fw-bold">{{ $tenant->plan ?? 'sprout' }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary small text-capitalize fw-bold">{{ str_replace('_', ' ', $tenant->theme ?? 'aura_luxe') }}</span>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-users-slash fs-1 mb-3 opacity-25"></i>
                                    <p class="mb-0">No tenants found.</p>
                                    <a href="{{ route('super_admin.create_tenant') }}" class="btn btn-link btn-sm text-decoration-none mt-2">Create your first tenant</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Subscription Breakdown -->
    <div class="col-12 col-xl-4">
        <div class="card border shadow-sm h-100 rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-dark fw-bold">Plan Distribution</h5>
            </div>
            <div class="card-body">
                <div class="vstack gap-4">
                    <!-- Sprout Plan -->
                    @php 
                        $sproutPercentage = $totalStores > 0 ? ($planBreakdown['sprout'] / $totalStores) * 100 : 0;
                        $maisonPercentage = $totalStores > 0 ? ($planBreakdown['maison'] / $totalStores) * 100 : 0;
                        $heritagePercentage = $totalStores > 0 ? ($planBreakdown['heritage'] / $totalStores) * 100 : 0;
                    @endphp
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-secondary small text-uppercase">Sprout (Starter)</span>
                            <span class="fw-bold text-dark small">{{ $planBreakdown['sprout'] }} ({{ number_format($sproutPercentage, 0) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $sproutPercentage }}%" aria-valuenow="{{ $sproutPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Maison Plan -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-secondary small text-uppercase">Maison (Professional)</span>
                            <span class="fw-bold text-dark small">{{ $planBreakdown['maison'] }} ({{ number_format($maisonPercentage, 0) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $maisonPercentage }}%" aria-valuenow="{{ $maisonPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Heritage Plan -->
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-secondary small text-uppercase">Heritage (Enterprise)</span>
                            <span class="fw-bold text-dark small">{{ $planBreakdown['heritage'] }} ({{ number_format($heritagePercentage, 0) }}%)</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $heritagePercentage }}%" aria-valuenow="{{ $heritagePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-dark fw-bold mb-3">System Health Summary</h6>
                    <div class="vstack gap-2">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted"><i class="fas fa-check-circle text-success me-2"></i> Database Scoping</span>
                            <span class="badge bg-light text-success border">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted"><i class="fas fa-check-circle text-success me-2"></i> Multi-Tenancy Middleware</span>
                            <span class="badge bg-light text-success border">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted"><i class="fas fa-check-circle text-success me-2"></i> Automated Store Seeders</span>
                            <span class="badge bg-light text-success border">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Platform Orders Log -->
<div class="card border shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="mb-0 text-dark fw-bold">Recent Platform Orders</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="px-3 py-2 border-0 fw-semibold">Order ID</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Customer</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Tenant ID</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Payment</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Status</th>
                    <th class="px-3 py-2 border-0 fw-semibold text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td class="px-3 py-3 fw-bold text-dark">#{{ $order->order_number }}</td>
                    <td class="px-3 py-3">
                        <div class="text-dark fw-semibold small">{{ $order->customer_name }}</div>
                        <div class="text-muted small" style="font-size: 11px;">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-3 py-3">
                        <span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $order->tenant ? $order->tenant->name : 'Tenant #' . $order->tenant_id }}</span>
                    </td>
                    <td class="px-3 py-3 small text-uppercase text-muted">{{ $order->payment_method ?? 'COD' }}</td>
                    <td class="px-3 py-3">
                        @php
                            $statusColors = [
                                'pending' => 'bg-warning text-warning',
                                'processing' => 'bg-primary text-primary',
                                'shipped' => 'bg-info text-info',
                                'delivered' => 'bg-success text-success',
                                'cancelled' => 'bg-danger text-danger',
                            ];
                            $colorClass = $statusColors[strtolower($order->status)] ?? 'bg-secondary text-secondary';
                        @endphp
                        <span class="badge {{ $colorClass }} bg-opacity-10 px-2 py-1 rounded small fw-bold text-capitalize">{{ $order->status }}</span>
                    </td>
                    <td class="px-3 py-3 text-dark fw-bold text-end">{{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted small">No orders recorded on the platform yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
