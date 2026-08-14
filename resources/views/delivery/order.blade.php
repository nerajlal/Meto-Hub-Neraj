@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('delivery.dashboard', request()->route('tenant') ?? session('active_tenant_id') ?? 1) }}" class="text-dark text-decoration-none">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Deliveries
        </a>
        <h1 class="h4 fw-bold mt-2 mb-0">Order #{{ $order->order_number }}</h1>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold text-muted small text-uppercase mb-3">Customer Details</h6>
            
            <h5 class="fw-bold text-dark mb-1 fs-5">{{ $order->customer_name }}</h5>
            
            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="tel:{{ $order->customer_phone }}" class="btn btn-sm btn-light text-primary border rounded-pill px-3 py-1 fw-medium">
                    <i class="fa-solid fa-phone me-1"></i> Call
                </a>
                <a href="mailto:{{ $order->customer_email }}" class="btn btn-sm btn-light text-secondary border rounded-pill px-3 py-1 fw-medium">
                    <i class="fa-solid fa-envelope me-1"></i> Email
                </a>
            </div>
            
            @php $addr = $order->shipping_address; @endphp
            <div class="d-flex align-items-start gap-2 bg-light p-3 rounded">
                <i class="fa-solid fa-location-dot mt-1 text-danger"></i>
                <div class="small fw-medium text-dark">
                    {{ $addr['address'] ?? '' }}<br>
                    @if(!empty($addr['apartment'])) {{ $addr['apartment'] }}<br> @endif
                    {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }} {{ $addr['zip'] ?? '' }}
                </div>
            </div>
            
            <div class="mt-3">
                <a href="https://maps.google.com/?q={{ urlencode(($addr['address'] ?? '') . ' ' . ($addr['city'] ?? '') . ' ' . ($addr['state'] ?? '') . ' ' . ($addr['zip'] ?? '')) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold">
                    <i class="fa-solid fa-map-location-dot me-2"></i> Open in Maps
                </a>
            </div>
        </div>
    </div>


    <!-- Order Items -->
    <div class="card border shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
            <h2 class="h6 fw-semibold text-secondary mb-0">Order Items ({{ $order->items->count() }})</h2>
        </div>
        <div class="list-group list-group-flush">
            @foreach($order->items as $item)
            <div class="list-group-item p-3 d-flex gap-3">
                <div class="bg-light rounded border d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; overflow:hidden;">
                    @if($item->product && $item->product->main_image_url)
                        <img src="{{ $item->product->main_image_url }}" alt="{{ $item->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @elseif($item->bundle && $item->bundle->image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->bundle->image) }}" alt="{{ $item->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <i class="fas fa-image text-secondary opacity-50 fs-4"></i>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <h4 class="h6 fw-medium text-primary mb-1"><a href="#" class="text-decoration-none">{{ $item->name }}</a></h4>
                    <p class="small text-muted mb-0">
                        @if($item->size) Size: {{ $item->size }}<br> @endif
                        @if($item->type == 'bundle') 
                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7em;">Bundle</span>
                            @if($item->bundle && $item->bundle->products->count() > 0)
                                <div class="mt-1 ps-2 border-start border-2">
                                    <small class="text-muted d-block fw-bold">Includes:</small>
                                    @foreach($item->bundle->products as $bProduct)
                                        <small class="text-muted d-block">• {{ $bProduct->title }} 
                                            @if($bProduct->variants->isNotEmpty())
                                                ({{ $bProduct->variants->first()->size }})
                                            @endif
                                        </small>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </p>
                </div>
                <div class="text-end">
                    <p class="small text-dark mb-1">{{ $currentTenant->currency ?? '₹' }}{{ number_format($item->price, 2) }} x {{ $item->quantity }}</p>
                    <p class="small fw-medium text-dark mb-0">{{ $currentTenant->currency ?? '₹' }}{{ number_format($item->total, 2) }}</p>
                    @if(isset($item->options['coupon_code']) && $item->options['coupon_code'])
                        <div class="mt-1">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" style="font-size: 0.7em;">
                                {{ $item->options['coupon_code'] }} Applied
                            </span>
                            <p class="small text-success mb-0" style="font-size: 0.75rem;">
                                Saved {{ $currentTenant->currency ?? '₹' }}{{ number_format($item->options['saved_amount'] * $item->quantity, 2) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

        </div>
        
        <div class="col-lg-4">
            <!-- Payment Info -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-muted small text-uppercase mb-3">Payment Info</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block small text-muted">Method</span>
                            <span class="fw-bold text-dark fs-5">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                        </div>
                        <div class="text-end">
                            <span class="d-block small text-muted">Amount to Collect</span>
                            @if($order->payment_method == 'cod' && $order->payment_status != 'paid')
                                <span class="fw-bold text-success fs-3">{{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 fs-6 rounded-pill">PAID</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 bg-transparent shadow-none mb-4 position-sticky" style="top: 20px;">
                <form action="{{ route('delivery.orders.update-status', ['tenant' => request()->route('tenant') ?? session('active_tenant_id') ?? 1, 'id' => $order->id]) }}" method="POST">
            @csrf
            
            @if($order->status != 'out_for_delivery' && $order->status != 'shipped' && $order->status != 'delivered')
                <input type="hidden" name="status" value="shipped">
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow-sm mb-3">
                    <i class="fa-solid fa-truck-fast me-2"></i> Start Delivery
                </button>
            @endif

            @if($order->status != 'delivered')
                <input type="hidden" name="status" value="delivered">
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm" onclick="return confirm('Confirm that you have delivered this order and collected any necessary payments?');">
                    <i class="fa-solid fa-check-circle me-2"></i> Mark as Delivered
                </button>
            @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
