@extends('layouts.admin')

@section('title', 'My Deliveries')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Assigned to You</h1>
            <p class="text-muted small mb-0">{{ $activeOrders->count() }} active orders</p>
        </div>
        <div class="text-end">
            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">
                {{ $completedOrdersCount }} done today
            </span>
        </div>
    </div>

    <div class="d-flex flex-column gap-3">
        @forelse($activeOrders as $order)
            <a href="{{ route('delivery.orders.show', ['tenant' => request()->route('tenant') ?? session('active_tenant_id') ?? 1, 'id' => $order->id]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary small fw-semibold">#{{ $order->order_number }}</span>
                            @if($order->status == 'out_for_delivery' || $order->status == 'shipped')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle">Out for Delivery</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-1 fs-6">{{ $order->customer_name }}</h5>
                        
                        <div class="d-flex align-items-start gap-2 text-muted small mb-2">
                            <i class="fa-solid fa-location-dot mt-1 text-danger"></i>
                            <span>
                                @php $addr = $order->shipping_address; @endphp
                                {{ $addr['address'] ?? '' }}, {{ $addr['city'] ?? '' }}
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="small fw-medium text-dark">
                                {{ $order->payment_method == 'cod' ? 'Collect COD' : 'Prepaid' }}
                            </div>
                            <div class="fw-bold text-dark">
                                {{ $order->payment_method == 'cod' ? ($currentTenant->currency ?? '₹') . number_format($order->total_amount, 2) : 'Paid' }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fa-solid fa-box-open fs-1 text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark">All caught up!</h5>
                <p class="text-muted small">You have no active deliveries assigned right now.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
