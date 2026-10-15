@extends('super_admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div><span class="small text-uppercase text-muted">Platform overview</span><h1 class="h3 fw-bold text-dark mt-1 mb-2">Super Admin Dashboard</h1><p class="text-muted mb-0">Track store growth, trials and subscription follow-ups.</p></div>
    <a class="btn btn-primary" href="{{ route('super_admin.create_tenant') }}">Create new store</a>
</div>
@include('super_admin.partials.billing_metrics')
<div class="row g-3 mb-4"><div class="col-md-6"><div class="card border p-3 h-100"><h2 class="h6 fw-bold text-dark">Subscription collections</h2>@forelse($subscriptionCollections as $collection)<p class="mb-1"><strong>{{ $collection->currency }} {{ number_format($collection->captured / 100, 2) }}</strong> captured · {{ number_format($collection->refunded / 100, 2) }} refunded</p>@empty<p class="mb-1">No captured subscription payments recorded.</p>@endforelse<small class="text-muted">Gross provider payment records before fees. Historical totals depend on reconciliation.</small></div></div><div class="col-md-6"><div class="card border p-3 h-100"><h2 class="h6 fw-bold text-dark">Outstanding subscription invoices</h2>@forelse($outstandingInvoices as $invoice)<p class="mb-1"><strong>{{ $invoice->currency }} {{ number_format($invoice->due / 100, 2) }}</strong></p>@empty<p class="mb-1">No outstanding invoices recorded.</p>@endforelse<small class="text-muted">Issued and partially paid invoices imported from the provider.</small></div></div></div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card border p-3 h-100"><span class="small text-muted">Registered stores</span><strong class="fs-3 text-dark">{{ number_format($totalStores) }}</strong><small>Includes trials and expired subscriptions</small></div></div>
    <div class="col-6 col-lg-3"><div class="card border p-3 h-100"><span class="small text-muted">Global orders</span><strong class="fs-3 text-dark">{{ number_format($totalOrders) }}</strong><a class="small" href="{{ route('super_admin.orders') }}">View orders →</a></div></div>
    <div class="col-6 col-lg-3"><div class="card border p-3 h-100"><span class="small text-muted">Store customers</span><strong class="fs-3 text-dark">{{ number_format($totalCustomers) }}</strong><a class="small" href="{{ route('super_admin.customers') }}">View customers →</a></div></div>
    <div class="col-6 col-lg-3"><div class="card border p-3 h-100"><span class="small text-muted">Store order value</span>@forelse($salesByCurrency as $sales)<strong class="fs-5 text-dark">{{ $sales->currency ?: 'INR' }} {{ number_format($sales->amount, 2) }}</strong>@empty<strong class="fs-5 text-dark">No orders yet</strong>@endforelse<small>Excludes cancelled orders. Includes unpaid orders; this is not SaaS revenue.</small></div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-7"><div class="card border p-4 h-100"><h2 class="h5 fw-bold text-dark">Needs attention</h2><div class="list-group list-group-flush">
        <a class="list-group-item d-flex justify-content-between px-0" href="{{ route('super_admin.subscriptions', ['trial_ending' => 1]) }}"><span>Unpaid trials ending within 48 hours</span><strong>{{ $counts['trial_ending'] }}</strong></a>
        <a class="list-group-item d-flex justify-content-between px-0" href="{{ route('super_admin.subscriptions', ['status' => 'halted']) }}"><span>Payments halted</span><strong>{{ $counts['halted'] }}</strong></a>
        <a class="list-group-item d-flex justify-content-between px-0" href="{{ route('super_admin.subscriptions', ['status' => 'paused']) }}"><span>Subscriptions paused</span><strong>{{ $counts['paused'] }}</strong></a>
        <div class="list-group-item d-flex justify-content-between px-0"><span>Store owner emails unverified</span><strong>{{ $counts['unverified'] }}</strong></div>
        <a class="list-group-item d-flex justify-content-between px-0" href="{{ route('super_admin.subscriptions', ['status' => 'unknown']) }}"><span>Subscription status needs review</span><strong>{{ $counts['unknown'] }}</strong></a>
    </div></div></div>
    <div class="col-lg-5"><div class="card border p-4 h-100"><h2 class="h5 fw-bold text-dark">Plan distribution</h2>@forelse($planBreakdown as $plan => $count)<div class="d-flex justify-content-between py-2 border-bottom"><span>{{ config('saas.plans.'.$plan.'.name', ucfirst($plan ?: 'Unassigned')) }}</span><strong>{{ $count }}</strong></div>@empty<p>No stores yet.</p>@endforelse<a class="mt-3 small" href="{{ route('super_admin.plans') }}">View current subscription prices →</a></div></div>
</div>
<div class="card border mb-4"><div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center p-3"><h2 class="h5 fw-bold text-dark mb-0">Recently registered stores</h2><a href="{{ route('super_admin.tenants') }}">View all stores →</a></div>@include('super_admin.partials.store_table')</div>
<div class="card border"><div class="card-header bg-white p-3"><h2 class="h5 fw-bold text-dark mb-0">Recent store orders</h2></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Order</th><th>Store</th><th>Status</th><th>Amount</th></tr></thead><tbody>@forelse($recentOrders as $order)<tr><td>#{{ $order->id }}</td><td>{{ $order->tenant?->name ?: 'Deleted store' }}</td><td>{{ ucfirst($order->status) }}</td><td>{{ $order->tenant?->currency ?: '₹' }}{{ number_format($order->total_amount, 2) }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No orders yet.</td></tr>@endforelse</tbody></table></div></div>
@endsection
