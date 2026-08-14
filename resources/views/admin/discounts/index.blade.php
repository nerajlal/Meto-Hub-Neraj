@extends('layouts.admin')

@section('title', 'Discounts')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-0 text-dark">Discounts & Custom Pricing</h1>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-4 border-bottom-0 gap-2" id="discountsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2.5 fw-semibold rounded-3 active border shadow-sm" id="coupons-tab" data-bs-toggle="tab" data-bs-target="#coupons-pane" type="button" role="tab" style="color: #495057;">
            <i class="fa-solid fa-ticket me-2"></i> Coupon Codes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2.5 fw-semibold rounded-3 border shadow-sm" id="custom-prices-tab" data-bs-toggle="tab" data-bs-target="#custom-prices-pane" type="button" role="tab" style="color: #495057;">
            <i class="fa-solid fa-user-tag me-2"></i> Customer Custom Pricing
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2.5 fw-semibold rounded-3 border shadow-sm" id="group-prices-tab" data-bs-toggle="tab" data-bs-target="#group-prices-pane" type="button" role="tab" style="color: #495057;">
            <i class="fa-solid fa-users-viewfinder me-2"></i> Group Custom Pricing
        </button>
    </li>
</ul>

<div class="tab-content" id="discountsTabsContent">
    <!-- Coupons Pane -->
    <div class="tab-pane fade show active" id="coupons-pane" role="tabpanel" aria-labelledby="coupons-tab">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-secondary mb-0">Coupons & Promo Codes</h2>
            <a href="{{ route('admin.discounts.create') }}" class="btn btn-success shadow-sm">Create discount</a>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.discounts') }}" class="text-decoration-none">
                    <div class="card border shadow-sm p-3 text-center h-100 {{ !request('status') ? 'border-primary bg-primary bg-opacity-10' : '' }}">
                        <div class="h3 fw-bold text-dark mb-0">{{ $total }}</div>
                        <div class="small text-muted text-uppercase tracking-wide mt-1">Total</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.discounts', ['status' => 'active']) }}" class="text-decoration-none">
                    <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'active' ? 'border-success bg-success bg-opacity-10' : '' }}">
                        <div class="h3 fw-bold text-success mb-0">{{ $active }}</div>
                        <div class="small text-muted text-uppercase tracking-wide mt-1">Active</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.discounts', ['status' => 'expired']) }}" class="text-decoration-none">
                    <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'expired' ? 'border-secondary bg-secondary bg-opacity-10' : '' }}">
                        <div class="h3 fw-bold text-secondary opacity-50 mb-0">{{ $expired }}</div>
                        <div class="small text-muted text-uppercase tracking-wide mt-1">Expired</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.discounts', ['status' => 'inactive']) }}" class="text-decoration-none">
                    <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'inactive' ? 'border-secondary bg-secondary bg-opacity-10' : '' }}">
                        <div class="h3 fw-bold text-secondary opacity-50 mb-0">{{ $inactive }}</div>
                        <div class="small text-muted text-uppercase tracking-wide mt-1">Inactive</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card border shadow-sm mb-4">
            <!-- Toolbar -->
            <div class="card-header bg-light border-bottom p-3 d-flex gap-3">
                <div class="flex-grow-1">
                     <form action="{{ route('admin.discounts') }}" method="GET">
                         <!-- Preserve other filters -->
                         @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                         @endforeach
                         <div class="input-group input-group-sm">
                             <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                             <input type="text" name="search" value="{{ request('search') }}" placeholder="Search discounts" class="form-control border-start-0 shadow-none">
                         </div>
                     </form>
                </div>
                
                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-white border btn-sm shadow-sm text-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-2"></i> {{ request('type') ? ucfirst(request('type')) : 'All Types' }}
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0">
                        <li><a class="dropdown-item small {{ !request('type') ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['type' => null, 'page' => 1])) }}">All Types</a></li>
                        <li><a class="dropdown-item small {{ request('type') == 'percentage' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['type' => 'percentage', 'page' => 1])) }}">Percentage</a></li>
                        <li><a class="dropdown-item small {{ request('type') == 'fixed' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['type' => 'fixed', 'page' => 1])) }}">Fixed Amount</a></li>
                    </ul>
                </div>

                <!-- Sort Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-white border btn-sm shadow-sm text-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sort me-2"></i> Sort
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                        <li><a class="dropdown-item small {{ !request('sort') || request('sort') == 'newest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['sort' => 'newest', 'page' => 1])) }}">Newest First</a></li>
                        <li><a class="dropdown-item small {{ request('sort') == 'oldest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['sort' => 'oldest', 'page' => 1])) }}">Oldest First</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small {{ request('sort') == 'code_asc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['sort' => 'code_asc', 'page' => 1])) }}">Code (A-Z)</a></li>
                        <li><a class="dropdown-item small {{ request('sort') == 'code_desc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.discounts', array_merge(request()->query(), ['sort' => 'code_desc', 'page' => 1])) }}">Code (Z-A)</a></li>
                    </ul>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-secondary">
                    <thead class="bg-light text-uppercase small fw-medium text-muted">
                        <tr>
                            <th class="px-3 py-3" style="width: 50px;"><div class="form-check"><input type="checkbox" class="form-check-input"></div></th>
                            <th class="px-3 py-3">Discount code</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Discount</th>
                            <th class="px-3 py-3">Valid Period</th>
                            <th class="px-3 py-3 text-end">Used</th>
                            <th class="px-3 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0" id="discounts-table-body">
                        @include('admin.discounts.partials.table')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Custom Prices Pane -->
    <div class="tab-pane fade" id="custom-prices-pane" role="tabpanel" aria-labelledby="custom-prices-tab">
        <div class="row g-4">
            <!-- Add Custom Price Form -->
            <div class="col-12 col-md-4">
                <div class="card border shadow-sm p-4 bg-white">
                    <h2 class="h5 fw-bold text-dark mb-3">Set Customer Pricing</h2>
                    <p class="small text-muted mb-4">Set specific wholesale or custom pricing rules for individual customers on specific items.</p>
                    
                    <form action="{{ route('admin.custom-prices.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Customer</label>
                            <select name="user_id" class="form-select shadow-sm" required>
                                <option value="">Choose Customer...</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email ?? $cust->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Product</label>
                            <select name="product_id" class="form-select shadow-sm" required>
                                <option value="">Choose Product...</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Custom Price ({{ $currentTenant->currency ?? '₹' }})</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted">{{ $currentTenant->currency ?? '₹' }}</span>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 shadow-sm">Save Custom Price</button>
                    </form>
                </div>
            </div>

            <!-- Custom Prices List -->
            <div class="col-12 col-md-8">
                <div class="card border shadow-sm p-0 bg-white">
                    <div class="card-header bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 fw-bold text-secondary mb-0">Active Custom Customer Pricing Rules</h2>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-file-excel text-success me-1"></i> Excel
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="{{ route('admin.custom-prices.sample-excel') }}"><i class="fa-solid fa-download me-2"></i> Download Sample Excel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadCustomPriceExcelModal"><i class="fa-solid fa-upload me-2"></i> Upload Excel</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-secondary">
                            <thead class="bg-light text-uppercase small fw-medium text-muted">
                                <tr>
                                    <th class="px-3 py-3">Customer</th>
                                    <th class="px-3 py-3">Product</th>
                                    <th class="px-3 py-3">Custom Price</th>
                                    <th class="px-3 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customPrices as $cp)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="fw-semibold text-dark">{{ $cp->user?->name ?? 'Deleted User' }}</div>
                                        <div class="small text-muted">{{ $cp->user?->email ?? $cp->user?->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-dark fw-medium">
                                        {{ $cp->product?->title ?? 'Deleted Product' }}
                                    </td>
                                    <td class="px-3 py-3 text-success fw-bold">
                                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($cp->price, 2) }}
                                    </td>
                                    <td class="px-3 py-3 text-end">
                                        <form action="{{ route('admin.custom-prices.destroy', $cp->id) }}" method="POST" onsubmit="return confirm('Delete this custom pricing rule?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 hover-text-danger shadow-none"><i class="fas fa-trash-can"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No custom pricing rules found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Prices Pane -->
    <div class="tab-pane fade" id="group-prices-pane" role="tabpanel" aria-labelledby="group-prices-tab">
        <div class="row g-4">
            <!-- Left Side Forms -->
            <div class="col-12 col-md-4">
                <!-- Create Customer Group -->
                <div class="card border shadow-sm p-4 bg-white mb-4">
                    <h2 class="h5 fw-bold text-dark mb-3">Create Customer Group</h2>
                    <form action="{{ route('admin.customer-groups.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Group Name</label>
                            <input type="text" name="name" class="form-control shadow-sm" placeholder="e.g. VIP Customers, Wholesale Buyers" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow-sm">Create Group</button>
                    </form>
                </div>

                <!-- Add Customers to Group -->
                <div class="card border shadow-sm p-4 bg-white mb-4">
                    <h2 class="h5 fw-bold text-dark mb-3">Assign Customers to Group</h2>
                    <form action="{{ route('admin.customer-groups.add-users') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Group</label>
                            <select name="customer_group_id" class="form-select shadow-sm" required>
                                <option value="">Choose Group...</option>
                                @foreach($customerGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Customers</label>
                            <select name="users[]" class="form-select shadow-sm" multiple required style="height: 120px;">
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email ?? $cust->phone }})</option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">Hold Ctrl/Cmd to select multiple customers.</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow-sm">Assign Customers</button>
                    </form>
                </div>

                <!-- Set Group Custom Price -->
                <div class="card border shadow-sm p-4 bg-white">
                    <h2 class="h5 fw-bold text-dark mb-3">Set Group Pricing</h2>
                    <form action="{{ route('admin.group-custom-prices.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Group</label>
                            <select name="customer_group_id" class="form-select shadow-sm" required>
                                <option value="">Choose Group...</option>
                                @foreach($customerGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Select Product</label>
                            <select name="product_id" class="form-select shadow-sm" required>
                                <option value="">Choose Product...</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Custom Price ({{ $currentTenant->currency ?? '₹' }})</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted">{{ $currentTenant->currency ?? '₹' }}</span>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow-sm">Save Group Price</button>
                    </form>
                </div>
            </div>

            <!-- Right Side lists -->
            <div class="col-12 col-md-8">
                <!-- Groups and Members List -->
                <div class="card border shadow-sm p-0 bg-white mb-4">
                    <div class="card-header bg-light border-bottom p-3">
                        <h2 class="h6 fw-bold text-secondary mb-0">Customer Groups & Members</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-secondary">
                            <thead class="bg-light text-uppercase small fw-medium text-muted">
                                <tr>
                                    <th class="px-3 py-3">Group Name</th>
                                    <th class="px-3 py-3">Members</th>
                                    <th class="px-3 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customerGroups as $group)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="fw-bold text-dark">{{ $group->name }}</div>
                                        <div class="small text-muted">{{ $group->users->count() }} members</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        @forelse($group->users as $u)
                                            <span class="badge bg-light text-dark border p-2 mb-1 d-inline-flex align-items-center gap-2">
                                                {{ $u->name }}
                                                <form action="{{ route('admin.customer-groups.remove-user', [$group->id, $u->id]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Remove user from group?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-close" style="font-size: 8px; width: 6px; height: 6px;" aria-label="Close"></button>
                                                </form>
                                            </span>
                                        @empty
                                            <span class="text-muted small">No members assigned yet.</span>
                                        @endforelse
                                    </td>
                                    <td class="px-3 py-3 text-end">
                                        <form action="{{ route('admin.customer-groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Delete this group and all its pricing rules?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 hover-text-danger shadow-none"><i class="fas fa-trash-can"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No customer groups created yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Group Pricing Rules List -->
                <div class="card border shadow-sm p-0 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h2 class="h6 fw-bold text-secondary mb-0">Active Group Pricing Rules</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-secondary">
                            <thead class="bg-light text-uppercase small fw-medium text-muted">
                                <tr>
                                    <th class="px-3 py-3">Group</th>
                                    <th class="px-3 py-3">Product</th>
                                    <th class="px-3 py-3">Group Custom Price</th>
                                    <th class="px-3 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupCustomPrices as $gcp)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="fw-bold text-dark">{{ $gcp->group?->name ?? 'Deleted Group' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-dark fw-medium">
                                        {{ $gcp->product?->title ?? 'Deleted Product' }}
                                    </td>
                                    <td class="px-3 py-3 text-success fw-bold">
                                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($gcp->price, 2) }}
                                    </td>
                                    <td class="px-3 py-3 text-end">
                                        <form action="{{ route('admin.group-custom-prices.destroy', $gcp->id) }}" method="POST" onsubmit="return confirm('Delete this group custom pricing rule?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 hover-text-danger shadow-none"><i class="fas fa-trash-can"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No group custom pricing rules found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .hover-text-primary:hover { color: #008060 !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }

    /* Pagination Overrides */
    .page-link {
        color: #008060;
        border-color: #dee2e6;
    }
    .page-link:hover {
        color: #004d3a;
        background-color: #e6f2f0;
        border-color: #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #008060;
        border-color: #008060;
        color: white;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-switch to custom prices tab if specified in URL query param
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'custom-prices') {
            const customTab = document.getElementById('custom-prices-tab');
            if (customTab) {
                const tab = new bootstrap.Tab(customTab);
                tab.show();
            }
        } else if (urlParams.get('tab') === 'group-prices') {
            const groupTab = document.getElementById('group-prices-tab');
            if (groupTab) {
                const tab = new bootstrap.Tab(groupTab);
                tab.show();
            }
        }

        const searchInput = document.querySelector('input[name="search"]');
        const tableBody = document.getElementById('discounts-table-body');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(debounceTimer);
                const query = e.target.value;
                
                // Update URL without reloading
                const url = new URL(window.location.href);
                if (query) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', 1); // Reset to page 1 on search
                window.history.pushState({}, '', url);

                debounceTimer = setTimeout(() => {
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableBody.innerHTML = html;
                    })
                    .catch(error => console.error('Error:', error));
                }, 300);
            });
        }
    });
</script>


<!-- Upload Excel Modal -->
<div class="modal fade" id="uploadCustomPriceExcelModal" tabindex="-1" aria-labelledby="uploadCustomPriceExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.custom-prices.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="uploadCustomPriceExcelModalLabel">Upload Custom Pricing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label small fw-semibold text-secondary">Excel/CSV File</label>
                        <input class="form-control shadow-sm" type="file" id="excelFile" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        <div class="form-text">Please use the downloaded sample template. Required columns: customer_email, product_id, price.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success shadow-sm">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
