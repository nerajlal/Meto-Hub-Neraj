@extends('super_admin.layouts.app')

@section('title', 'Global Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Global Orders Log</h1>
        <p class="text-muted small">Monitor all customer checkouts and transactions across the multi-tenant SaaS platform.</p>
    </div>
</div>

<div class="card border shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-dark fw-bold">Transaction Ledger</h5>
        <span class="badge bg-light text-secondary border">{{ $orders->total() }} total orders</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="px-3 py-2 border-0 fw-semibold">Order ID</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Customer</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Store / Tenant ID</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Payment</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Status</th>
                    <th class="px-3 py-2 border-0 fw-semibold">Placed At</th>
                    <th class="px-3 py-2 border-0 fw-semibold text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
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
                    <td class="px-3 py-3 text-muted small">{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-3 py-3 text-dark fw-bold text-end">{{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted small">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-box-open fs-1 mb-3 opacity-25"></i>
                            <p class="mb-0">No orders recorded on the platform yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
