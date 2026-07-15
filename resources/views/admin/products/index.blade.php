@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Products</h1>
    <div class="d-flex gap-2">
        @if(isset($zohoConnected) && $zohoConnected)
            <form action="{{ route('admin.zoho.sync', request()->route('tenant') ?? 1) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-white border shadow-sm fw-medium text-primary">
                    <i class="fas fa-sync-alt me-1"></i> Sync from Zoho
                </button>
            </form>
        @else
            <a href="{{ route('admin.zoho.connect', request()->route('tenant') ?? 1) }}" class="btn btn-white border shadow-sm fw-medium text-primary">
                <i class="fas fa-plug me-1"></i> Connect Zoho
            </a>
        @endif

        <form action="{{ route('admin.dartpos.sync', request()->route('tenant') ?? 1) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-white border shadow-sm fw-medium text-primary">
                <i class="fas fa-sync-alt me-1"></i> Sync DartPOS
            </button>
        </form>
        
        <button type="button" class="btn btn-white border shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import me-1"></i> Import Products
        </button>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success shadow-sm">Add product</a>
    </div>
</div>

    <!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.products') }}" class="text-decoration-none">
            <div class="card border shadow-sm p-3 text-center h-100 {{ !request('status') ? 'border-success bg-success bg-opacity-10' : '' }}">
                <div class="h3 fw-bold text-dark mb-0">{{ $total }}</div>
                <div class="small text-muted text-uppercase tracking-wide mt-1">Total</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.products', ['status' => 'active']) }}" class="text-decoration-none">
            <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'active' ? 'border-success bg-success bg-opacity-10' : '' }}">
                <div class="h3 fw-bold text-success mb-0">{{ $active }}</div>
                <div class="small text-muted text-uppercase tracking-wide mt-1">Active</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.products', ['status' => 'draft']) }}" class="text-decoration-none">
            <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'draft' ? 'border-warning bg-warning bg-opacity-10' : '' }}">
                <div class="h3 fw-bold text-secondary opacity-50 mb-0">{{ $draft }}</div>
                <div class="small text-muted text-uppercase tracking-wide mt-1">Draft</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.products', ['status' => 'archived']) }}" class="text-decoration-none">
            <div class="card border shadow-sm p-3 text-center h-100 {{ request('status') == 'archived' ? 'border-secondary bg-secondary bg-opacity-10' : '' }}">
                <div class="h3 fw-bold text-secondary opacity-50 mb-0">{{ $archived }}</div>
                <div class="small text-muted text-uppercase tracking-wide mt-1">Archived</div>
            </div>
        </a>
    </div>
</div>

<div class="card border shadow-sm">
    <div class="card-header bg-light border-bottom p-3">
        <div class="d-flex gap-3">
            <div class="flex-grow-1">
                 <form action="{{ route('admin.products') }}" method="GET">
                     @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                     @endforeach
                     <div class="input-group shadow-sm">
                         <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                         <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 shadow-none" placeholder="Filter products">
                     </div>
                 </form>
            </div>
            
             <!-- Type Filter -->
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i> {{ request('type') ?? 'Type' }}
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li><a class="dropdown-item small" href="{{ route('admin.products', array_merge(request()->query(), ['type' => null, 'page' => 1])) }}">All Types</a></li>
                    @foreach($types as $type)
                        <li><a class="dropdown-item small {{ request('type') == $type ? 'active bg-light text-success fw-bold' : '' }}" href="{{ route('admin.products', array_merge(request()->query(), ['type' => $type, 'page' => 1])) }}">{{ $type }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-2"></i> Sort
                </button>
                <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                    <li><a class="dropdown-item small {{ !request('sort') || request('sort') == 'newest' ? 'active bg-light text-success fw-bold' : '' }}" href="{{ route('admin.products', array_merge(request()->query(), ['sort' => 'newest', 'page' => 1])) }}">Newest First</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'oldest' ? 'active bg-light text-success fw-bold' : '' }}" href="{{ route('admin.products', array_merge(request()->query(), ['sort' => 'oldest', 'page' => 1])) }}">Oldest First</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'title_asc' ? 'active bg-light text-success fw-bold' : '' }}" href="{{ route('admin.products', array_merge(request()->query(), ['sort' => 'title_asc', 'page' => 1])) }}">Title (A-Z)</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'title_desc' ? 'active bg-light text-success fw-bold' : '' }}" href="{{ route('admin.products', array_merge(request()->query(), ['sort' => 'title_desc', 'page' => 1])) }}">Title (Z-A)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                 <tr>
                    <th class="px-3 py-3 w-auto"><input type="checkbox" class="form-check-input"></th>
                    <th class="px-3 py-3 border-0 fw-medium">Product</th>
                    <th class="px-3 py-3 border-0 fw-medium">Status</th>
                    <th class="px-3 py-3 border-0 fw-medium">Inventory</th>
                    <th class="px-3 py-3 border-0 fw-medium">Type</th>
                    <th class="px-3 py-3 border-0 fw-medium">Vendor</th>
                    <th class="px-3 py-3 text-end" style="width: 100px;"></th>
                 </tr>
            </thead>
            <tbody id="products-table-body">
                @include('admin.products.partials.table')
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const tableBody = document.getElementById('products-table-body');
        let debounceTimer;

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
            url.searchParams.set('page', 1);
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
    });
</script>
<style>
    .hover-success:hover {
        color: #008060 !important;
    }
    .hover-danger:hover {
        color: var(--bs-danger) !important;
    }
    
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Import Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-4">
                        Upload an Excel (.xlsx, .xls) or CSV (.csv) file containing your product data. 
                        We automatically map standard exports (like Tally) if they use column headers such as <strong>Item Name</strong>, <strong>Rate</strong>, <strong>Closing Balance</strong>, or <strong>Part No</strong>.
                    </p>
                    <div class="mb-3">
                        <label for="import_file" class="form-label fw-medium">Select File</label>
                        <input type="file" class="form-control form-control-lg bg-light" id="import_file" name="import_file" accept=".csv,.txt,.xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-white border fw-medium px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-medium px-4">Upload and Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
