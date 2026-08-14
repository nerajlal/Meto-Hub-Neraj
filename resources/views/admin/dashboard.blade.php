@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Dashboard</h1>
        <p class="text-muted small">Overview of your store's performance.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- <a href="{{ $storefrontUrl }}" target="_blank" class="btn btn-sm bg-shopify-green text-white d-none d-sm-flex align-items-center gap-2">
            <i class="fas fa-external-link-alt"></i>
            View Storefront
        </a> -->
        <span class="small text-muted bg-white border border-light px-3 py-2 rounded shadow-sm">
            <i class="far fa-calendar-alt me-2 text-secondary"></i> Today, {{ date('M d, Y') }}
        </span>
    </div>
</div>

<!-- Metrics Grid -->
<div class="row g-3 mb-4">
    <!-- Total Sales -->
    <!-- Total Sales -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm position-relative">
            <a href="{{ route('admin.analytics') }}" class="stretched-link"></a>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-2 bg-success bg-opacity-10 rounded">
                    <i class="fas fa-coins text-success"></i>
                </div>
                <span class="badge {{ $salesGrowth >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 small d-flex align-items-center">
                    <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($salesGrowth), 1) }}%
                </span>
            </div>
            <h3 class="small fw-medium text-muted mb-1">Total Sales</h3>
            <span class="fs-4 fw-bold text-dark">{{ $currentTenant->currency ?? '₹' }}{{ number_format($currentSales, 2) }}</span>
            <p class="small text-muted mt-2 mb-0">vs. {{ $currentTenant->currency ?? '₹' }}{{ number_format($prevSales, 2) }} last 30d</p>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm position-relative">
            <a href="{{ route('admin.orders') }}" class="stretched-link"></a>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-2 bg-primary bg-opacity-10 rounded">
                    <i class="fas fa-shopping-bag text-primary"></i>
                </div>
                <span class="badge {{ $ordersGrowth >= 0 ? 'bg-primary text-primary' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 small d-flex align-items-center">
                    <i class="fas fa-arrow-{{ $ordersGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($ordersGrowth), 1) }}%
                </span>
            </div>
            <h3 class="small fw-medium text-muted mb-1">Total Orders</h3>
            <span class="fs-4 fw-bold text-dark">{{ number_format($currentOrders) }}</span>
            <p class="small text-muted mt-2 mb-0">vs. {{ number_format($prevOrders) }} last 30d</p>
        </div>
    </div>

    <!-- Customers -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm position-relative">
            <a href="{{ route('admin.customers') }}" class="stretched-link"></a>
            <div class="d-flex justify-content-between align-items-start mb-3">
                 <div class="p-2 bg-info bg-opacity-10 rounded">
                    <i class="fas fa-users text-info"></i>
                </div>
                <span class="badge {{ $customersGrowth >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' }} bg-opacity-10 rounded-pill px-2 py-1 small d-flex align-items-center">
                    <i class="fas fa-arrow-{{ $customersGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($customersGrowth), 1) }}%
                </span>
            </div>
            <h3 class="small fw-medium text-muted mb-1">New Customers</h3>
            <span class="fs-4 fw-bold text-dark">{{ number_format($currentCustomers) }}</span>
            <p class="small text-muted mt-2 mb-0">vs. {{ number_format($prevCustomers) }} last 30d</p>
        </div>
    </div>

    <!-- Low Stock Items (Replaces AOV) -->
     <div class="col-12 col-md-6 col-lg-3">
        <div class="card p-3 h-100 border shadow-sm position-relative">
            <a href="{{ route('admin.products') }}" class="stretched-link"></a>
            <div class="d-flex justify-content-between align-items-start mb-3">
                 <div class="p-2 bg-danger bg-opacity-10 rounded">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                 <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small d-flex align-items-center">
                    Action Needed
                </span>
            </div>
            <h3 class="small fw-medium text-muted mb-1">Low Stock Items</h3>
            <span class="fs-4 fw-bold text-dark">{{ $lowStockCount }}</span>
            <p class="small text-muted mt-2 mb-0">Restock required</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-12 col-lg-8">
        <div class="card border shadow-sm h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold text-dark mb-0">Recent Orders</h2>
                <a href="{{ route('admin.orders') }}" class="small text-success text-decoration-none fw-medium">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-3 py-2 border-0 fw-medium">Order</th>
                            <th class="px-3 py-2 border-0 fw-medium">Customer</th>
                            <th class="px-3 py-2 border-0 fw-medium">Total</th>
                            <th class="px-3 py-2 border-0 fw-medium">Status</th>
                            <th class="px-3 py-2 border-0 fw-medium text-end">Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order->id) }}'">
                            <td class="px-3 py-3 fw-semibold text-dark">{{ $order->order_number }}</td>
                            <td class="px-3 py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-dark">{{ $order->customer_name }}</span>
                                    <span class="small text-muted">{{ $order->customer_email }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-dark">{{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-3 py-3">
                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'bg-warning text-warning',
                                        'processing' => 'bg-info text-info',
                                        'shipped' => 'bg-primary text-primary',
                                        'delivered' => 'bg-success text-success',
                                        'cancelled' => 'bg-danger text-danger',
                                        default => 'bg-secondary text-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} bg-opacity-10 px-2 py-1 rounded-pill fw-medium text-capitalize">{{ $order->status }}</span>
                            </td>
                            <td class="px-3 py-3 text-end">{{ $order->items->count() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-12 col-lg-4">
        <div class="card border shadow-sm h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold text-dark mb-0">Low Stock Alerts</h2>
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">3</span>
            </div>
            <div class="p-3 d-flex flex-column gap-3">
                @forelse($lowStockItems as $item)
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-{{ $item->stock == 0 ? 'danger' : 'warning' }} bg-opacity-10 text-{{ $item->stock == 0 ? 'danger' : 'warning' }}" style="width: 40px; height: 40px;">
                            <i class="fas fa-{{ $item->stock == 0 ? 'exclamation-circle' : 'box-open' }} small"></i>
                        </div>
                        <div>
                            <h4 class="small fw-medium text-dark mb-0">{{ $item->product ? $item->product->title : 'Unknown Product' }} @if($item->size) ({{ $item->size }}) @endif</h4>
                            <p class="small text-{{ $item->stock == 0 ? 'danger' : 'warning' }} fw-medium mb-0">{{ $item->stock == 0 ? 'Out of Stock' : 'Only ' . $item->stock . ' left' }}</p>
                        </div>
                    </div>
                    @if($item->product_id)
                    <a href="{{ route('admin.products.edit', $item->product_id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;">Restock</a>
                    @endif
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
