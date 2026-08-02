@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Products</h1>
    <div class="d-flex flex-wrap gap-2">
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
        
        <div class="dropdown d-inline-block">
            <button class="btn btn-white border shadow-sm fw-medium dropdown-toggle" type="button" id="importTallyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-import me-1"></i> Import Tally
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="importTallyDropdown">
                <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-2 text-muted"></i> Upload File</a></li>
                <li><a class="dropdown-item py-2 disabled" href="#" style="opacity: 0.5; cursor: not-allowed;" onclick="event.preventDefault();"><i class="fas fa-table me-2 text-success"></i> Live Excel Editor <span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">Soon</span></a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.products.sample') }}"><i class="fas fa-download me-2 text-muted"></i> Download Sample Format</a></li>
            </ul>
        </div>
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
        <div class="d-flex flex-column flex-md-row gap-3">
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
            <div class="d-flex gap-2">
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
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                 <tr>
                    <th class="px-3 py-3 w-auto"><input type="checkbox" class="form-check-input"></th>
                    <th class="px-3 py-3 border-0 fw-medium" style="width: 60px;">ID</th>
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
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="importModalLabel">Import Tally</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-4">
                        Upload an Excel (.xlsx, .xls) or CSV (.csv) file containing your product data. 
                        We automatically map standard exports (like Tally) as well as full product catalogs. <br>
                        <strong>Supported Columns:</strong> Title, Description, Status, Product Type, Vendor, Tags, Min Order Qty, Max Order Qty, Continue Selling, Variant Size, SKU, Price, Compare Price, Stock.
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

<!-- Live Excel Modal -->
<div class="modal fade" id="liveExcelModal" tabindex="-1" aria-labelledby="liveExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="liveExcelModalLabel"><i class="fas fa-table text-success me-2"></i>Live Excel Editor</h5>
                <div>
                    <button type="button" class="btn btn-secondary shadow-sm me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success shadow-sm" onclick="saveLiveExcel()"><i class="fas fa-save me-1"></i> Save & Sync</button>
                </div>
            </div>
            <div class="modal-body p-0" style="overflow: auto; height: calc(100vh - 130px);">
                <div id="spreadsheet" style="width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />
<script src="https://jsuites.net/v4/jsuites.js"></script>
<link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />

<script>
    let mySpreadsheet = null;

    function openLiveExcel() {
        // Show modal
        var myModal = new bootstrap.Modal(document.getElementById('liveExcelModal'));
        myModal.show();
        
        // Destroy previous instance if exists
        document.getElementById('spreadsheet').innerHTML = '<div class="p-5 text-center text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p>Loading catalog...</p></div>';
        
        // Show loading state or fetch data
        fetch("{{ route('admin.products.live-export') }}", {
            credentials: 'same-origin'
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                document.getElementById('spreadsheet').innerHTML = '';
                // Prevent crash if data is completely empty
                let spreadsheetData = data;
                if (!Array.isArray(data) || data.length === 0) {
                    spreadsheetData = [['', '', '', '', '', '', 'active', '', '']];
                }

                mySpreadsheet = jexcel(document.getElementById('spreadsheet'), {
                    data: spreadsheetData,
                    minDimensions: [9, 10],
                    columns: [
                        { type: 'numeric', title: 'ID', width: 60, readOnly: true },
                        { type: 'text', title: 'Title', width: 300 },
                        { type: 'text', title: 'SKU', width: 120 },
                        { type: 'numeric', title: 'Price', width: 100, mask: '₹ #.##,00' },
                        { type: 'numeric', title: 'Compare Price', width: 120, mask: '₹ #.##,00' },
                        { type: 'numeric', title: 'Stock', width: 100 },
                        { type: 'dropdown', title: 'Status', width: 120, source: ['active', 'draft', 'archived'] },
                        { type: 'text', title: 'Type', width: 150 },
                        { type: 'text', title: 'Vendor', width: 150 }
                    ],
                    tableOverflow: true,
                    tableHeight: 'calc(100vh - 135px)',
                    tableWidth: '100%',
                    search: true,
                    pagination: 100,
                    contextMenu: function(obj, x, y, e) {
                        var items = [];
                        if (y !== null) {
                            items.push({
                                title: 'Insert new row above',
                                onclick: function() {
                                    obj.insertRow(1, parseInt(y), 1);
                                }
                            });
                            items.push({
                                title: 'Insert new row below',
                                onclick: function() {
                                    obj.insertRow(1, parseInt(y));
                                }
                            });
                            items.push({
                                title: 'Delete row',
                                onclick: function() {
                                    obj.deleteRow(parseInt(y), 1);
                                }
                            });
                        }
                        return items;
                    }
                });
            })
            .catch(err => {
                document.getElementById('spreadsheet').innerHTML = '<div class="p-5 text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p>Failed to load data. Please try again.</p></div>';
            });
    }

    function saveLiveExcel() {
        if (!mySpreadsheet) return;
        
        // Get all data from the spreadsheet
        const data = mySpreadsheet.getData();
        const btn = document.querySelector('#liveExcelModal .btn-success');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        btn.disabled = true;
        
        fetch("{{ route('admin.products.live-import') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ data: data })
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                alert(response.message);
                window.location.reload();
            } else {
                alert('Error: ' + (response.message || 'Unknown error'));
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while saving.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>

@endsection
