@extends('super_admin.layouts.app')
@section('title', 'Subscriptions & Trials')
@section('content')
<div class="mb-4"><h1 class="h3 fw-bold text-dark">Subscriptions & Trials</h1><p class="text-muted">Review trial expiry, pending checkout and the subscription status recorded for each store.</p></div>
@include('super_admin.partials.billing_metrics')
<div class="alert alert-light border small"><strong>Payment visibility:</strong> “Payment pending” means subscription checkout was started or is awaiting activation; it is not a confirmed overdue invoice. Dates, invoices and payment history appear on each store’s details after webhook processing or billing reconciliation. Statuses here reflect the latest locally synchronized provider records.</div>
@include('super_admin.partials.store_filters')
<div class="card border"><div class="card-header bg-white p-3">{{ number_format($tenants->total()) }} matching stores</div>@include('super_admin.partials.store_table')<div class="p-3">{{ $tenants->links('pagination::bootstrap-5') }}</div></div>
@endsection
