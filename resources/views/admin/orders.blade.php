@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Orders</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.export', ['tenant' => request()->route('tenant'), 'status' => request('status'), 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-white border shadow-sm fw-medium d-flex align-items-center gap-2" style="font-size: 13px; border-radius: 6px;">
            <i class="fa-solid fa-file-export fs-6"></i> Export Excel
        </a>
        <a href="{{ route('admin.orders.create', ['tenant' => request()->route('tenant')]) }}" class="btn btn-dark shadow-sm fw-medium d-flex align-items-center gap-2" style="font-size: 13px; border-radius: 6px;">
            <i class="fa-solid fa-plus fs-6"></i> Create order
        </a>
    </div>
</div>

<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div class="nav nav-pills gap-2 flex-wrap">
        <a href="{{ route('admin.orders', ['date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ !request('status') || request('status') == 'all' ? 'active bg-dark' : 'bg-light text-secondary' }} border px-3 py-1">All</a>
        <a href="{{ route('admin.orders', ['status' => 'pending', 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ request('status') == 'pending' ? 'active bg-warning text-dark' : 'bg-light text-secondary' }} border px-3 py-1">Pending</a>
        <a href="{{ route('admin.orders', ['status' => 'processing', 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ request('status') == 'processing' ? 'active bg-primary' : 'bg-light text-secondary' }} border px-3 py-1">Processing</a>
        <a href="{{ route('admin.orders', ['status' => 'shipped', 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ request('status') == 'shipped' ? 'active bg-info text-white' : 'bg-light text-secondary' }} border px-3 py-1">Shipped</a>
        <a href="{{ route('admin.orders', ['status' => 'delivered', 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ request('status') == 'delivered' ? 'active bg-success' : 'bg-light text-secondary' }} border px-3 py-1">Delivered</a>
        <a href="{{ route('admin.orders', ['status' => 'cancelled', 'date_filter' => request('date_filter', 'this_month'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="nav-link {{ request('status') == 'cancelled' ? 'active bg-danger' : 'bg-light text-secondary' }} border px-3 py-1">Cancelled</a>
    </div>

    <form method="GET" action="{{ route('admin.orders') }}" class="d-flex align-items-center gap-2" id="dateFilterForm">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        
        <select name="date_filter" class="form-select form-select-sm" style="width: auto; min-width: 140px; border-radius: 6px;" onchange="toggleCustomDates(this.value)">
            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
            <option value="this_month" {{ request('date_filter', 'this_month') == 'this_month' ? 'selected' : '' }}>This Month</option>
            <option value="this_year" {{ request('date_filter') == 'this_year' ? 'selected' : '' }}>This Year</option>
            <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>All Time</option>
            <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
        </select>

        <div id="customDateInputs" class="d-flex align-items-center gap-2 {{ request('date_filter') == 'custom' ? '' : 'd-none' }}">
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" style="border-radius: 6px;">
            <span class="text-muted small">to</span>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" style="border-radius: 6px;">
        </div>

        <button type="submit" class="btn btn-dark btn-sm shadow-sm" style="border-radius: 6px;">Filter</button>
    </form>
</div>

<script>
    function toggleCustomDates(value) {
        const inputs = document.getElementById('customDateInputs');
        if (value === 'custom') {
            inputs.classList.remove('d-none');
        } else {
            inputs.classList.add('d-none');
            // If they didn't select custom, auto-submit the form for a better UX
            document.getElementById('dateFilterForm').submit();
        }
    }
</script>

<div class="card border shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="px-3 py-3 border-0 fw-medium">Order</th>
                    <th class="px-3 py-3 border-0 fw-medium">Date</th>
                    <th class="px-3 py-3 border-0 fw-medium">Customer</th>
                    <th class="px-3 py-3 border-0 fw-medium">Total</th>
                    <th class="px-3 py-3 border-0 fw-medium">Payment</th>
                    <th class="px-3 py-3 border-0 fw-medium">Fulfillment</th>
                    <th class="px-3 py-3 border-0 fw-medium text-end">Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order->id) }}'">
                    <td class="px-3 py-3 fw-semibold text-dark"><a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none text-dark hover-primary">{{ $order->order_number }}</a></td>
                    <td class="px-3 py-3 text-secondary">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    <td class="px-3 py-3">{{ $order->customer_name }}</td>
                    <td class="px-3 py-3 text-dark">₹{{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-3 py-3">
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-medium">Paid</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill fw-medium">Pending</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill fw-medium">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <!-- Fulfillment Status Logic (Assuming 'status' column or similar) -->
                        @if($order->status == 'delivered')
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill fw-medium">Delivered</span>
                        @elseif($order->status == 'shipped')
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill fw-medium">Shipped</span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill fw-medium">Cancelled</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill fw-medium">{{ ucfirst($order->status) }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-end">{{ $order->items_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2 text-muted small">
        {{ $orders->links('pagination::bootstrap-5') }} <!-- Standard Laravel Pagination Link -->
    </div>
</div>
@endsection
