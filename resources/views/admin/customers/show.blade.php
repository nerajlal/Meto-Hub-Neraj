@extends('layouts.admin')

@section('title', 'Sarah Jenkins')

@section('content')
@section('content')
<div class="container pb-5">
    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.customers') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h1 class="h3 mb-1 text-dark d-flex align-items-center gap-2">
                {{ $customer->name }}
                @if($customer->email_verified_at)
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase small tracking-wide" style="letter-spacing: 0.05em;">Verified</span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold text-uppercase small tracking-wide" style="letter-spacing: 0.05em;">Unverified</span>
                @endif
            </h1>
            <p class="small text-muted mb-0">Customer since {{ $customer->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="row g-4">
        
        <!-- Left Column: Orders & Timeline -->
        <div class="col-12 col-lg-8">
            <div class="vstack gap-4">
            
                <!-- Orders -->
                 <div class="card border shadow-sm overflow-hidden">
                     <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center p-3">
                        <h2 class="h6 fw-bold text-secondary mb-0">Orders</h2>
                        <span class="small text-muted">Total spent: <span class="fw-bold text-dark">{{ $currentTenant->currency ?? '₹' }}{{ number_format($customer->total_spent ?? 0, 2) }}</span></span>
                     </div>
                     
                     <div class="table-responsive">
                         <table class="table table-hover align-middle mb-0 text-start">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="px-3 py-2 border-bottom fw-medium">Order</th>
                                    <th class="px-3 py-2 border-bottom fw-medium">Date</th>
                                    <th class="px-3 py-2 border-bottom fw-medium">Status</th>
                                    <th class="px-3 py-2 border-bottom fw-medium text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($customer->orders as $order)
                                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order->id) }}'">
                                    <td class="px-3 py-3 font-medium text-dark fw-bold">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-3 py-3 text-secondary">
                                        {{ $order->created_at->format('M d, Y') }}
                                        <div class="small text-muted">{{ $order->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-warning text-warning-emphasis',
                                                'processing' => 'bg-info text-info-emphasis',
                                                'shipped' => 'bg-primary text-primary-emphasis',
                                                'delivered' => 'bg-success text-success-emphasis',
                                                'cancelled' => 'bg-danger text-danger-emphasis',
                                            ];
                                            $badgeClass = $statusColors[$order->status] ?? 'bg-secondary text-secondary-emphasis';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} bg-opacity-10 rounded-pill px-2 py-1 fw-medium">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-end fw-medium text-dark">
                                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="fas fa-shopping-bag fa-2x opacity-25"></i></div>
                                        <p class="mb-0">No orders found for this customer.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                         </table>
                     </div>
                </div>

                <!-- Notes -->
                <div class="card border shadow-sm p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <h2 class="h6 fw-bold text-secondary mb-0">Notes</h2>
                         <button class="btn btn-link btn-sm p-0 text-decoration-none">Edit</button>
                     </div>
                     <p class="small text-secondary fst-italic mb-0">No notes available.</p>
                </div>
                
            </div>
        </div>

        <!-- Right Column: Customer Info -->
        <div class="col-12 col-lg-4">
            <div class="vstack gap-4">
            
                <!-- Customer Contact -->
                <div class="card border shadow-sm p-4 position-relative card-hover-actions">
                    <!-- <button class="btn btn-link btn-sm p-0 position-absolute top-0 end-0 mt-3 me-3 text-decoration-none opacity-0 hover-opacity-100 transition-opacity show-on-hover">Edit</button> -->
                    <h2 class="h6 fw-bold text-secondary mb-3">Customer Contact</h2>
                    <div class="vstack gap-3">
                        <div class="d-flex align-items-center gap-3">
                             @php
                                $initials = collect(explode(' ', $customer->name))->map(fn($s) => strtoupper(substr($s, 0, 1)))->take(2)->implode('');
                             @endphp
                             <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle fw-bold small" style="width: 32px; height: 32px;">{{ $initials }}</div>
                             <a href="mailto:{{ $customer->email }}" class="text-primary text-decoration-none small" id="copy-email-text">{{ $customer->email }}</a>
                             <button class="btn btn-link btn-sm p-0 text-secondary hover-text-dark" onclick="copyToClipboard('{{ $customer->email }}', this)"><i class="far fa-copy"></i></button>
                        </div>
                         <div class="d-flex align-items-center gap-3">
                             <div class="text-center text-secondary small" style="width: 32px;"><i class="fas fa-phone"></i></div>
                             <span class="small text-dark" id="copy-phone-text">{{ $customer->phone ?? ($customer->defaultAddress->phone ?? 'No phone number') }}</span>
                             @if($customer->phone || ($customer->defaultAddress && $customer->defaultAddress->phone))
                                <button class="btn btn-link btn-sm p-0 text-secondary hover-text-dark" onclick="copyToClipboard('{{ $customer->phone ?? $customer->defaultAddress->phone }}', this)"><i class="far fa-copy"></i></button>
                             @endif
                        </div>
                    </div>
                </div>

                <!-- Default Address -->
                <div class="card border shadow-sm p-4 position-relative card-hover-actions">
                    <!-- <button class="btn btn-link btn-sm p-0 position-absolute top-0 end-0 mt-3 me-3 text-decoration-none opacity-0 hover-opacity-100 transition-opacity show-on-hover">Manage</button> -->
                    <h2 class="h6 fw-bold text-secondary mb-3">Default Address</h2>
                    <div class="small text-secondary lh-sm">
                        @if($customer->defaultAddress)
                            <p class="mb-1 text-dark fw-medium">{{ $customer->defaultAddress->address_line1 }}</p>
                            @if($customer->defaultAddress->address_line2)
                                <p class="mb-1 text-secondary">{{ $customer->defaultAddress->address_line2 }}</p>
                            @endif
                            <p class="mb-1 text-secondary">{{ $customer->defaultAddress->city }}, {{ $customer->defaultAddress->state }} {{ $customer->defaultAddress->zip }}</p>
                            <p class="mb-0 text-secondary">{{ $customer->defaultAddress->country }}</p>
                        @else
                            <p class="mb-0 text-muted">No address information available.</p>
                        @endif
                    </div>
                </div>

                
                <div class="pt-3 border-top">
                    <!-- Delete button can be implemented later -->
                    <button class="btn btn-link text-danger text-decoration-none fw-medium w-100 text-start border-0 p-0 small hover-text-danger-dark" onclick="alert('Delete functionality coming soon')">Delete customer</button>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .text-dark-warning { color: #856404; }
    .card-hover-actions:hover .show-on-hover { opacity: 1 !important; }
    .hover-text-danger-dark:hover { color: #bd2130 !important; }
</style>
<script>
    function copyToClipboard(text, btn) {
        if (!text) return;
        
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            const originalClass = icon.className;
            
            icon.className = 'fas fa-check text-success';
            
            setTimeout(() => {
                icon.className = originalClass;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy to clipboard');
        });
    }
</script>
@endsection
