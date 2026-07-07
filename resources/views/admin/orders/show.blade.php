@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.orders') }}" class="text-secondary text-decoration-none">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h4 fw-bold text-dark mb-0">Order {{ $order->order_number }}</h1>

                <span id="fulfillmentBadge" class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ $order->status }}</span>
            </div>
            <p class="text-muted small mt-1 mb-0 ms-4 ps-1">{{ $order->created_at->format('F d, Y at h:i a') }} from Online Store</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-white border shadow-sm btn-sm">Print</a>
            <button onclick="advanceAction()" id="fulfillBtn" class="btn btn-warning text-white shadow-sm btn-sm fw-medium">Mark as Processing</button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-12 col-lg-8">
            
            <!-- Products Card -->
            <div class="card border shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold text-secondary mb-0">Order Items ({{ $order->items->count() }})</h2>
                </div>
                <div class="list-group list-group-flush">
                    <div id="trackingInfo" class="list-group-item bg-info bg-opacity-10 border-bottom border-info border-opacity-25 {{ $order->tracking_number ? '' : 'd-none' }}">
                         <p class="small text-info text-darken-2 fw-medium mb-0">
                            Tracking Number: <span id="trackingNumberDisplay" class="fw-bold">{{ $order->tracking_number }}</span>
                            @if($order->deliveryPartner)
                                <span class="mx-2">|</span>
                                <span class="text-muted">via {{ $order->deliveryPartner->name }}</span>
                                @if($order->deliveryPartner->tracking_url_template)
                                    <a href="{{ str_replace('{tracking_number}', $order->tracking_number, $order->deliveryPartner->tracking_url_template) }}" target="_blank" class="ms-1 text-decoration-none">
                                        <i class="fas fa-external-link-alt small"></i> Track
                                    </a>
                                @endif
                            @endif
                         </p>
                    </div>
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
                            <p class="small text-dark mb-1">₹{{ number_format($item->price, 2) }} x {{ $item->quantity }}</p>
                            <p class="small fw-medium text-dark mb-0">₹{{ number_format($item->total, 2) }}</p>
                            @if(isset($item->options['coupon_code']) && $item->options['coupon_code'])
                                <div class="mt-1">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" style="font-size: 0.7em;">
                                        {{ $item->options['coupon_code'] }} Applied
                                    </span>
                                    <p class="small text-success mb-0" style="font-size: 0.75rem;">
                                        Saved ₹{{ number_format($item->options['saved_amount'] * $item->quantity, 2) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Card -->
            <div class="card border shadow-sm overflow-hidden">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold text-secondary mb-0">Payment</h2>
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} text-uppercase">{{ $order->payment_status }}</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="text-dark">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Shipping</span>
                        <span class="text-dark">₹{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-dark border-top pt-3 mb-3">
                        <span>Total</span>
                        <span>₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="border-top pt-3 small text-muted">
                        <div class="d-flex justify-content-between">
                            <span>Payment method</span>
                            <span class="text-uppercase">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            
            <!-- Customer Card -->
            <div class="card border shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-semibold text-secondary mb-0">Customer</h2>
                </div>
                <div class="card-body p-3">
                    <a href="#" class="text-decoration-none fw-medium text-primary mb-1 d-block">{{ $order->customer_name }}</a>
                    
                    <h3 class="small fw-semibold text-muted text-uppercase mb-2">Contact Information</h3>
                    <p class="small text-primary mb-1"><a href="mailto:{{ $order->customer_email }}" class="text-decoration-none">{{ $order->customer_email }}</a></p>
                    <p class="small text-muted">{{ $order->customer_phone }}</p>
                    
                    <hr class="text-muted opacity-25 my-3">
                    
                    <h3 class="small fw-semibold text-muted text-uppercase mb-2">Shipping Address</h3>
                    <p class="small text-muted lh-base">
                        @php $addr = $order->shipping_address; @endphp
                        {{ $addr['address'] ?? '' }}<br>
                        {{ $addr['apartment'] ?? '' }}<br>
                        {{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }} {{ $addr['zip'] ?? '' }}<br>
                        India
                    </p>
                    
                    <!-- <hr class="text-muted opacity-25 my-3"> -->
                    
                    <!-- <h3 class="small fw-semibold text-muted text-uppercase mb-2">Billing Address</h3>
                    <p class="small text-muted opacity-75">Same as shipping address</p> -->
                </div>
            </div>

             <!-- Notes Card -->
             <div class="card border shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold text-secondary mb-0">Notes</h2>
                </div>
                <div class="card-body p-3">
                    <p class="small text-muted fst-italic mb-0">{{ $order->notes ?? 'No notes from customer' }}</p>
                </div>
            </div>

            <!-- Custom Data Card -->
            @if($order->custom_checkout_data && count($order->custom_checkout_data) > 0)
            <div class="card border shadow-sm overflow-hidden mb-4">
               <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                   <h2 class="h6 fw-semibold text-secondary mb-0">Additional Information</h2>
               </div>
               <div class="card-body p-3">
                   @foreach($order->custom_checkout_data as $key => $value)
                       <div class="mb-2">
                           <span class="small fw-semibold text-muted text-uppercase d-block">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                           <span class="small text-dark">{{ $value }}</span>
                       </div>
                   @endforeach
               </div>
           </div>
           @endif

            <!-- Delivery Schedule Card -->
            @if($order->delivery_date || $order->delivery_time_slot)
            <div class="card border shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 fw-semibold text-secondary mb-0"><i class="fa-regular fa-calendar-check text-primary me-2"></i> Delivery Schedule</h2>
                </div>
                <div class="card-body p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small fw-semibold text-muted text-uppercase d-block mb-1">Scheduled Date</span>
                            <span class="fw-bold text-dark fs-5">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('D, M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="text-end">
                            <span class="small fw-semibold text-muted text-uppercase d-block mb-1">Time Slot</span>
                            @if($order->delivery_time_slot)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 fs-6 rounded-pill">{{ $order->delivery_time_slot }}</span>
                            @else
                            <span class="fw-bold text-dark fs-6">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
    <!-- Shipment Modal -->
    <div class="modal fade" id="shipmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Shipped</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tracking Number</label>
                        <input type="text" id="modalTrackingId" class="form-control" placeholder="Enter tracking number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Partner</label>
                        <select id="modalDeliveryPartner" class="form-select">
                            <option value="">Select Partner</option>
                            @foreach($deliveryPartners as $partner)
                                <option value="{{ $partner->id }}" {{ $partner->is_default ? 'selected' : '' }}>{{ $partner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmShipment()">Confirm Shipment</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Beautiful custom toast notification with a golden border and green icon -->
<div id="toast" style="position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; align-items: center; gap: 12px; background: #ffffff; border: 2px solid #C9A84C; border-radius: 8px; padding: 14px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transform: translateY(-20px); opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none;">
    <div id="toastIcon" style="width: 24px; height: 24px; background: #34A853; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; font-size: 11px;">
        <i class="fas fa-check"></i>
    </div>
    <span id="toastMessage" style="font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 550; color: #1a1a1a;"></span>
</div>

<script>
    let orderStatus = '{{ $order->status }}';

    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        const msgSpan = document.getElementById('toastMessage');
        
        if (toast && msgSpan) {
            msgSpan.textContent = message;
            
            if (isSuccess) {
                icon.style.background = '#34A853';
                icon.innerHTML = '<i class="fas fa-check"></i>';
                toast.style.borderColor = '#C9A84C';
            } else {
                icon.style.background = '#EA4335';
                icon.innerHTML = '<i class="fas fa-times"></i>';
                toast.style.borderColor = '#EA4335';
            }
            
            // Show toast
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
            toast.style.pointerEvents = 'auto';
            
            // Hide toast after 3.5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                toast.style.pointerEvents = 'none';
            }, 3500);
        }
    }

    function advanceAction() {
        if (orderStatus === 'pending') {
            updateStatus('processing');
        } else if (orderStatus === 'processing') {
            // Show Modal for Shipping
            new bootstrap.Modal(document.getElementById('shipmentModal')).show();
        } else if (orderStatus === 'shipped') {
            updateStatus('delivered');
        }
    }

    function confirmShipment() {
        const trackingId = document.getElementById('modalTrackingId').value;
        const partnerId = document.getElementById('modalDeliveryPartner').value;

        if (!trackingId) {
            alert('Please enter a tracking number');
            return;
        }

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('shipmentModal'));
        modal.hide();

        updateStatus('shipped', trackingId, partnerId);
    }

    function updateStatus(nextStatus, trackingId = null, partnerId = null) {
        const btn = document.getElementById('fulfillBtn');
        const badge = document.getElementById('fulfillmentBadge');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        btn.disabled = true;

        fetch('{{ route("admin.orders.update-status", $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: nextStatus,
                tracking_id: trackingId,
                delivery_partner_id: partnerId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                orderStatus = nextStatus;
                
                // Update Badge and Button
                 if (orderStatus === 'processing') {
                    badge.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 text-uppercase';
                    badge.innerText = 'Processing';
                    btn.innerHTML = 'Mark as Shipped';
                    btn.className = 'btn btn-info text-white shadow-sm btn-sm fw-medium'; 
                    showToast('Order marked as Processing!');

                } else if (orderStatus === 'shipped') {
                    badge.className = 'badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 text-uppercase';
                    badge.innerText = 'Shipped';
                    btn.innerHTML = 'Mark as Delivered';
                    btn.className = 'btn btn-success text-white shadow-sm btn-sm fw-medium';
                    
                    // Show tracking info
                    const trackingDisplay = document.getElementById('trackingInfo');
                    document.getElementById('trackingNumberDisplay').innerText = trackingId;
                    trackingDisplay.classList.remove('d-none');
                    
                    showToast('Order marked as Shipped!');

                } else if (orderStatus === 'delivered') {
                    badge.className = 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 text-uppercase';
                    badge.innerText = 'Delivered';
                    btn.innerHTML = 'Completed';
                    btn.className = 'btn btn-light text-muted border shadow-sm btn-sm fw-medium disabled';
                    btn.disabled = true;
                    showToast('Order marked as Delivered!');
                }
            } else {
                showToast('Something went wrong. Please try again.', false);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred.', false);
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Initial Button State Logic on Page Load
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('fulfillBtn');
        if(orderStatus === 'processing') {
             btn.innerHTML = 'Mark as Shipped';
             btn.className = 'btn btn-info text-white shadow-sm btn-sm fw-medium';
        } else if(orderStatus === 'shipped') {
             btn.innerHTML = 'Mark as Delivered';
             btn.className = 'btn btn-success text-white shadow-sm btn-sm fw-medium';
        } else if(orderStatus === 'delivered') {
             btn.innerHTML = 'Completed';
             btn.className = 'btn btn-light text-muted border shadow-sm btn-sm fw-medium disabled';
             btn.disabled = true;
        }
    });

</script>
@endsection
