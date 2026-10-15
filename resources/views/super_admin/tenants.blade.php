@extends('super_admin.layouts.app')
@section('title', 'All Stores')
@section('content')
<div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4"><div><h1 class="h3 fw-bold text-dark">All Stores</h1><p class="text-muted mb-0">Find owners, review trials and inspect store subscriptions.</p></div><a class="btn btn-primary" href="{{ route('super_admin.create_tenant') }}">Create new store</a></div>
@include('super_admin.partials.billing_metrics')
@include('super_admin.partials.store_filters')
<div class="card border"><div class="card-header bg-white p-3">{{ number_format($tenants->total()) }} matching stores</div>@include('super_admin.partials.store_table')<div class="p-3">{{ $tenants->links('pagination::bootstrap-5') }}</div></div>
@endsection
