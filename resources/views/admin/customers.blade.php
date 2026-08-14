@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-dark">Customers</h1>
    <!-- <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success shadow-sm">Add customer</a>
    </div> -->
</div>

<div class="card border shadow-sm container-fluid p-0" style="min-height: 400px;">
    <div class="card-header bg-light border-bottom p-3 d-flex gap-3">
        <div class="flex-grow-1">
             <form action="{{ route('admin.customers') }}" method="GET">
                 @foreach(request()->except(['search', 'page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                 @endforeach
                 <div class="input-group shadow-sm">
                     <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                     <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers" class="form-control border-start-0 ps-0 shadow-none">
                 </div>
             </form>
        </div>
        <!-- Status Filter -->
        <!-- <div class="dropdown">
            <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-2"></i> {{ request('status') ? ucfirst(request('status')) : 'All Status' }}
            </button>
            <ul class="dropdown-menu shadow-sm border-0">
                <li><a class="dropdown-item small {{ !request('status') ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['status' => null, 'page' => 1])) }}">All Status</a></li>
                <li><a class="dropdown-item small {{ request('status') == 'verified' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['status' => 'verified', 'page' => 1])) }}">Verified</a></li>
                <li><a class="dropdown-item small {{ request('status') == 'unverified' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['status' => 'unverified', 'page' => 1])) }}">Unverified</a></li>
            </ul>
        </div> -->

        <!-- Sort Dropdown -->
        <div class="dropdown">
            <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-sort me-2"></i> Sort
            </button>
            <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                <li><a class="dropdown-item small {{ !request('sort') || request('sort') == 'newest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['sort' => 'newest', 'page' => 1])) }}">Newest First</a></li>
                <li><a class="dropdown-item small {{ request('sort') == 'oldest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['sort' => 'oldest', 'page' => 1])) }}">Oldest First</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small {{ request('sort') == 'name_asc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['sort' => 'name_asc', 'page' => 1])) }}">Name (A-Z)</a></li>
                <li><a class="dropdown-item small {{ request('sort') == 'name_desc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.customers', array_merge(request()->query(), ['sort' => 'name_desc', 'page' => 1])) }}">Name (Z-A)</a></li>
            </ul>
        </div>
    </div>

    <!-- Placeholder Content -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-secondary">
             <thead class="bg-light text-uppercase small fw-medium text-muted">
                 <tr>
                    <th class="px-3 py-3" style="width: 50px;"><div class="form-check"><input type="checkbox" class="form-check-input"></div></th>
                    <th class="px-3 py-3">Customer name</th>
                    <th class="px-3 py-3">Email</th>
                    <th class="px-3 py-3">Phone</th>
                    <th class="px-3 py-3">Location</th>
                    <th class="px-3 py-3">Orders</th>
                    <th class="px-3 py-3 text-end">Amount spent</th>
                 </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($customers as $customer)
                @php
                    $initials = collect(explode(' ', $customer->name))->map(fn($s) => strtoupper(substr($s, 0, 1)))->take(2)->implode('');
                    $bgColors = ['bg-purple-subtle', 'bg-primary-subtle', 'bg-success-subtle', 'bg-info-subtle', 'bg-warning-subtle'];
                    $textColors = ['text-purple', 'text-primary', 'text-success', 'text-info', 'text-dark'];
                    $colorIndex = $customer->id % count($bgColors);
                @endphp
                <tr class="cursor-pointer" onclick="window.location='{{ route('admin.customers.show', $customer->id) }}'">
                    <td class="px-3 py-3" onclick="event.stopPropagation()"><div class="form-check"><input type="checkbox" class="form-check-input"></div></td>
                    <td class="px-3 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle {{ $bgColors[$colorIndex] }} {{ $textColors[$colorIndex] }} fw-bold small" style="width: 32px; height: 32px;">{{ $initials }}</div>
                            <div>
                                <p class="mb-0 fw-semibold text-dark text-decoration-hover-underline">{{ $customer->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3 text-secondary">{{ $customer->email }}</td>
                    <td class="px-3 py-3 text-secondary">{{ $customer->phone ?? ($customer->defaultAddress->phone ?? 'N/A') }}</td>
                    <td class="px-3 py-3 text-secondary">
                        @if($customer->defaultAddress)
                            {{ $customer->defaultAddress->city }}, {{ $customer->defaultAddress->state }}
                        @else
                            <span class="text-muted small">--</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">{{ $customer->orders_count ?? 0 }} orders</td>
                    <td class="px-3 py-3 text-end fw-medium text-dark">{{ $currentTenant->currency ?? '₹' }}{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted mb-2"><i class="fas fa-users fa-2x opacity-25"></i></div>
                        <p class="mb-0">No customers found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-3 border-top">
        {{ $customers->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
<style>
    .bg-purple-subtle { background-color: #e9d5ff !important; }
    .text-purple { color: #9333ea !important; }
    .text-decoration-hover-underline:hover { text-decoration: underline !important; }
</style>
@endsection
