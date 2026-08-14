@extends('super_admin.layouts.app')

@section('title', $tenant->name . ' - Store Analytics')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-2">
        <a href="{{ route('super_admin.tenants') }}" class="text-secondary text-decoration-none">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 fw-bold text-dark mb-0">{{ $tenant->name }} Analytics</h1>
    </div>
    <p class="text-muted small">Comprehensive metrics, orders history, and inventory status for this storefront.</p>
</div>

<!-- Store Meta Information Cards -->
<div class="card border shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-12 col-md-3 border-end">
                <div class="text-uppercase small fw-bold text-muted mb-1">Domain</div>
                <div class="fw-bold text-primary">{{ $tenant->domain }}</div>
                <div class="text-muted extra-small mt-1">Created: {{ $tenant->created_at->format('M d, Y') }}</div>
            </div>
            <div class="col-12 col-md-3 border-end">
                <div class="text-uppercase small fw-bold text-muted mb-1">Store Owner</div>
                @if($tenant->admin)
                    <div class="fw-bold text-dark">{{ $tenant->admin->name }}</div>
                    <div class="text-muted small">{{ $tenant->admin->email }}</div>
                @else
                    <div class="text-muted small">No Admin Registered</div>
                @endif
            </div>
            <div class="col-12 col-md-3 border-end">
                <div class="text-uppercase small fw-bold text-muted mb-1">Current Plan</div>
                <span class="badge bg-success bg-opacity-10 text-success text-capitalize px-2 py-1 rounded fw-bold">{{ $tenant->plan ?? 'sprout' }}</span>
            </div>
            <div class="col-12 col-md-3">
                <div class="text-uppercase small fw-bold text-muted mb-1">Active Theme</div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary text-capitalize px-2 py-1 rounded fw-bold">{{ str_replace('_', ' ', $tenant->theme ?? 'aura_luxe') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Store Operational Metrics -->
<div class="row g-3 mb-4">
    <!-- Total Sales -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Sales</span>
                    <h3 class="fw-bold text-dark mb-0">{{ $currentTenant->currency ?? '₹' }}{{ number_format($totalSales, 2) }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                    <i class="fas fa-coins fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Orders</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Total Products</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalProducts) }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                    <i class="fas fa-box-open fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-uppercase small fw-bold text-muted d-block mb-1">Store Customers</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalCustomers) }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                    <i class="fas fa-users fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders table -->
    <div class="col-12 col-lg-8">
        <div class="card border shadow-sm h-100 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-dark fw-bold">Recent Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-3 py-2 border-0 fw-semibold">Order</th>
                            <th class="px-3 py-2 border-0 fw-semibold">Customer</th>
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
                            <td colspan="4" class="text-center py-4 text-muted small">No orders recorded for this store yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-12 col-lg-4">
        <div class="card border shadow-sm h-100 rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 text-dark fw-bold">Low Stock Alerts</h5>
            </div>
            <div class="p-3 d-flex flex-column gap-3">
                @forelse($lowStockItems as $item)
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-{{ $item->stock == 0 ? 'danger' : 'warning' }} bg-opacity-10 text-{{ $item->stock == 0 ? 'danger' : 'warning' }}" style="width: 40px; height: 40px;">
                            <i class="fas fa-{{ $item->stock == 0 ? 'exclamation-circle' : 'box-open' }} small"></i>
                        </div>
                        <div>
                            <h4 class="small fw-medium text-dark mb-0">{{ $item->product ? $item->product->title : 'Unknown' }} @if($item->size) ({{ $item->size }}) @endif</h4>
                            <p class="small text-{{ $item->stock == 0 ? 'danger' : 'warning' }} fw-medium mb-0">{{ $item->stock == 0 ? 'Out of Stock' : 'Only ' . $item->stock . ' left' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">
                    <i class="fas fa-check-circle text-success mb-2 fs-5"></i><br>
                    Inventory looks good!
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
