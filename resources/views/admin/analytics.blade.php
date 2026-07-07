@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-dark">Analytics</h1>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.analytics.export', ['period' => request('period', '30_days')]) }}" class="btn btn-white border shadow-sm fw-medium d-flex align-items-center gap-2" style="font-size: 13px; border-radius: 6px;">
            <i class="fa-solid fa-file-export fs-6"></i> Export Report
        </a>
        <div class="dropdown">
        <div class="d-flex align-items-center gap-2 border rounded bg-white px-3 py-1 shadow-sm text-sm cursor-pointer hover-bg-light dropdown-toggle" data-bs-toggle="dropdown">
            <i class="far fa-calendar text-secondary"></i>
            <span class="text-dark">{{ $periodLabel }}</span>
        </div>
        <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
            <li><a class="dropdown-item small {{ $period == '7_days' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.analytics', ['period' => '7_days']) }}">Last 7 days</a></li>
            <li><a class="dropdown-item small {{ $period == '30_days' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.analytics', ['period' => '30_days']) }}">Last 30 days</a></li>
            <li><a class="dropdown-item small {{ $period == '90_days' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.analytics', ['period' => '90_days']) }}">Last 90 days</a></li>
            <li><a class="dropdown-item small {{ $period == 'year' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.analytics', ['period' => 'year']) }}">Last Year</a></li>
        </ul>
    </div>
    </div>
</div>

<!-- Key Metrics (Normal) -->
<div class="row g-3 mb-4">
    <!-- Total Sales -->
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('admin.analytics.show', 'sales') }}" class="card border shadow-sm p-3 text-decoration-none h-100 hover-shadow transition-base">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h6 text-muted text-uppercase small fw-bold mb-0">Total Sales</h3>
            </div>
            <div class="h3 fw-bold text-dark mb-1">₹{{ number_format($totalSales, 2) }}</div>
            <div class="small {{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                 <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($salesGrowth), 1) }}%
            </div>
            <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"></div>
            </div>
        </a>
    </div>

    <!-- Total Orders -->
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('admin.analytics.show', 'orders') }}" class="card border shadow-sm p-3 text-decoration-none h-100 hover-shadow transition-base">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h6 text-muted text-uppercase small fw-bold mb-0">Total Orders</h3>
            </div>
            <div class="h3 fw-bold text-dark mb-1">{{ number_format($totalOrders) }}</div>
            <div class="small {{ $ordersGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                 <i class="fas fa-arrow-{{ $ordersGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($ordersGrowth), 1) }}%
            </div>
             <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-purple" role="progressbar" style="width: 55%"></div>
            </div>
        </a>
    </div>

    <!-- Average Order Value -->
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('admin.analytics.show', 'aov') }}" class="card border shadow-sm p-3 text-decoration-none h-100 hover-shadow transition-base">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h6 text-muted text-uppercase small fw-bold mb-0">Avg. Order Value</h3>
            </div>
            <div class="h3 fw-bold text-dark mb-1">₹{{ number_format($avgOrderValue, 2) }}</div>
            <div class="small {{ $aovGrowth >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                 <i class="fas fa-arrow-{{ $aovGrowth >= 0 ? 'up' : 'down' }} me-1"></i> {{ number_format(abs($aovGrowth), 1) }}%
            </div>
             <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"></div>
            </div>
        </a>
    </div>

    <!-- Online Store Sessions (Static for now) -->
    <div class="col-12 col-md-6 col-lg-3">
        <a href="{{ route('admin.analytics.show', 'sessions') }}" class="card border shadow-sm p-3 text-decoration-none h-100 hover-shadow transition-base">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h6 text-muted text-uppercase small fw-bold mb-0">Sessions</h3>
            </div>
            <div class="h3 fw-bold text-dark mb-1">--</div>
            <div class="small text-muted fw-medium">
                 Not tracked
            </div>
             <div class="progress mt-3" style="height: 4px;">
                <div class="progress-bar bg-indigo" role="progressbar" style="width: 0%"></div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    
    <!-- Top Products -->
    <div class="col-12 col-lg-8">
        <div class="card border shadow-sm">
            <div class="card-header bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
                <h3 class="h6 fw-semibold text-secondary mb-0">Top Selling Products</h3>
                <span class="small text-muted">Last 30 days</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small fw-medium text-muted border-bottom">
                        <tr>
                            <th class="px-3 py-3">Product</th>
                            <th class="px-3 py-3 text-end">Units Sold</th>
                            <th class="px-3 py-3 text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($topProducts as $product)
                        <tr>
                            <td class="px-3 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px; overflow:hidden;">
                                        @if($product->product && $product->product->main_image_url)
                                            <img src="{{ $product->product->main_image_url }}" alt="{{ $product->name }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <i class="fas fa-image text-secondary opacity-50"></i>
                                        @endif
                                    </div>
                                    <span class="fw-medium text-dark">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-end text-secondary">{{ $product->total_qty }}</td>
                            <td class="px-3 py-3 text-end fw-medium text-dark">₹{{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No sales data found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- <div class="card-footer bg-white border-top p-2 text-center">
                 <a href="{{ route('admin.products', ['sort' => 'best_selling']) }}" class="small text-decoration-none">View all products</a>
            </div> -->
        </div>
    </div>

    <!-- Store Overview -->
    <div class="col-12 col-lg-4">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-light border-bottom p-3">
                <h3 class="h6 fw-semibold text-secondary mb-0">Store Overview</h3>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10" style="width: 40px; height: 40px;">
                            <i class="fas fa-tag text-primary small"></i>
                        </div>
                        <span class="small text-secondary">Active Products</span>
                    </div>
                    <span class="fw-bold text-dark">{{ $totalProducts }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10" style="width: 40px; height: 40px;">
                            <i class="fas fa-layer-group text-success small"></i>
                        </div>
                        <span class="small text-secondary">Collections</span>
                    </div>
                    <span class="fw-bold text-dark">{{ $totalCollections }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-info bg-opacity-10" style="width: 40px; height: 40px;">
                            <i class="fas fa-users text-info small"></i>
                        </div>
                        <span class="small text-secondary">Customers</span>
                    </div>
                    <span class="fw-bold text-dark">{{ $totalCustomers }}</span>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-warning bg-opacity-10" style="width: 40px; height: 40px;">
                            <i class="fas fa-boxes text-warning small"></i>
                        </div>
                        <span class="small text-secondary">Total Inventory</span>
                    </div>
                    <span class="fw-bold text-dark">{{ number_format($totalInventory) }} units</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Second Analytics Row -->
<div class="row g-3">

    <!-- Order Status Breakdown -->
    <div class="col-12 col-md-6">
        <div class="card border shadow-sm">
            <div class="card-header bg-light border-bottom p-3">
                <h3 class="h6 fw-semibold text-secondary mb-0">Order Status Breakdown</h3>
            </div>
            <div class="card-body p-3">
                @php
                    $allOrdersCount = array_sum($orderStatusBreakdown);
                    $statusColors = [
                        'pending' => ['bg' => 'warning', 'icon' => 'clock'],
                        'processing' => ['bg' => 'info', 'icon' => 'cog'],
                        'shipped' => ['bg' => 'primary', 'icon' => 'truck'],
                        'delivered' => ['bg' => 'success', 'icon' => 'check-circle'],
                        'cancelled' => ['bg' => 'danger', 'icon' => 'times-circle'],
                    ];
                @endphp
                @if($allOrdersCount > 0)
                    @foreach($orderStatusBreakdown as $status => $count)
                    @php $pct = round(($count / $allOrdersCount) * 100); @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded bg-{{ $statusColors[$status]['bg'] ?? 'secondary' }} bg-opacity-10" style="width: 36px; height: 36px;">
                            <i class="fas fa-{{ $statusColors[$status]['icon'] ?? 'circle' }} text-{{ $statusColors[$status]['bg'] ?? 'secondary' }} small"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-medium text-dark text-capitalize">{{ $status }}</span>
                                <span class="small text-muted">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-{{ $statusColors[$status]['bg'] ?? 'secondary' }}" role="progressbar" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-inbox mb-2 fs-4 opacity-50"></i><br>
                        No orders yet
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Inventory Health -->
    <div class="col-12 col-md-6">
        <div class="card border shadow-sm">
            <div class="card-header bg-light border-bottom p-3">
                <h3 class="h6 fw-semibold text-secondary mb-0">Inventory Health</h3>
            </div>
            <div class="card-body p-3">
                @php
                    $inStockCount = $totalInventory > 0 ? \App\Models\ProductVariant::whereHas('product', fn($q) => $q->where('tenant_id', auth()->user()->tenant_id ?? 1))->where('stock', '>', 10)->count() : 0;
                    $lowStockCount = \App\Models\ProductVariant::whereHas('product', fn($q) => $q->where('tenant_id', auth()->user()->tenant_id ?? 1))->where('stock', '>', 0)->where('stock', '<=', 10)->count();
                    $totalVariants = $inStockCount + $lowStockCount + $outOfStockCount;
                @endphp

                <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-success bg-opacity-10 rounded">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-25" style="width: 40px; height: 40px;">
                        <i class="fas fa-check text-success small"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="small fw-medium text-dark">In Stock</span>
                        <span class="small text-muted d-block">{{ $inStockCount }} variants</span>
                    </div>
                    @if($totalVariants > 0)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">{{ round(($inStockCount / $totalVariants) * 100) }}%</span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-warning bg-opacity-10 rounded">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-25" style="width: 40px; height: 40px;">
                        <i class="fas fa-exclamation-triangle text-warning small"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="small fw-medium text-dark">Low Stock</span>
                        <span class="small text-muted d-block">{{ $lowStockCount }} variants (≤10 units)</span>
                    </div>
                    @if($totalVariants > 0)
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ round(($lowStockCount / $totalVariants) * 100) }}%</span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3 p-2 bg-danger bg-opacity-10 rounded">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-25" style="width: 40px; height: 40px;">
                        <i class="fas fa-times-circle text-danger small"></i>
                    </div>
                    <div class="flex-grow-1">
                        <span class="small fw-medium text-dark">Out of Stock</span>
                        <span class="small text-muted d-block">{{ $outOfStockCount }} variants</span>
                    </div>
                    @if($totalVariants > 0)
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ round(($outOfStockCount / $totalVariants) * 100) }}%</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
<style>
    .bg-purple { background-color: #a855f7 !important; }
    .bg-indigo { background-color: #6366f1 !important; }
    .bg-orange { background-color: #f97316 !important; }
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    .transition-base { transition: all .3s ease; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
</style>
@endsection
