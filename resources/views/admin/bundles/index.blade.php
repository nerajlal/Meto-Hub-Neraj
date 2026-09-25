@extends('layouts.admin')

@section('title', 'Bundles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-primary">Bundles</h1>
    <div class="d-flex gap-2">
        <a href="javascript:void(0)" class="btn btn-outline-info shadow-sm disabled" style="pointer-events: none; opacity: 0.5;" title="Pool is disabled">
            <i class="fas fa-swimming-pool me-1"></i> Pool
        </a>
        <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#packOfModal">
            <i class="fas fa-layer-group me-1"></i> Pack Of
        </button>
        <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary shadow-sm">Create bundle</a>
    </div>
</div>

<!-- Pack Of Modal -->
<div class="modal fade" id="packOfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.bundles.pack-of') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Create "Pack Of" Bundle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <p class="text-muted small mb-4">Quickly create bundles for multiple units of the same product variant.</p>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">1. Select Product</label>
                    <select name="product_id" id="pack_product_id" class="form-select" required>
                        <option value="">Choose a product...</option>
                        @php $allProductsForPack = \App\Models\Product::where('status', 'active')->get(); @endphp
                        @foreach($allProductsForPack as $product)
                            <option value="{{ $product->id }}">{{ $product->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 d-none" id="variant_container">
                    <label class="form-label small fw-bold text-secondary">2. Select Variant (Size)</label>
                    <select name="variant_id" id="pack_variant_id" class="form-select" required>
                        <!-- Populated by JS -->
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">3. Quantity</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 small">x</span>
                            <input type="number" name="quantity" class="form-control border-start-0" value="2" min="2" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-secondary">4. Pack Price</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 small">{{ $currentTenant->currency ?? '₹' }}</span>
                            <input type="number" name="pack_price" class="form-control border-start-0" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-white border px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4">Create Pack</button>
            </div>
        </form>
    </div>
</div>

<div class="card border shadow-sm">
    <div class="card-header bg-white border-bottom-0 p-0">
        <ul class="nav nav-tabs px-3 pt-2">
            <li class="nav-item">
                <a class="nav-link {{ !request('type') || request('type') == 'bundle' ? 'active fw-bold text-dark border-bottom-0' : 'text-muted' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['type' => 'bundle', 'page' => 1])) }}">
                    Bundles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'pack' ? 'active fw-bold text-dark border-bottom-0' : 'text-muted' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['type' => 'pack', 'page' => 1])) }}">
                    Pack Of
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="javascript:void(0)" style="pointer-events: none; opacity: 0.5;" title="Pool is disabled">
                    Pool
                </a>
            </li>
        </ul>
    </div>
    <div class="card-header bg-light border-bottom p-3">
        <div class="d-flex gap-3">
            <div class="flex-grow-1">
                 <form action="{{ route('admin.bundles') }}" method="GET">
                     @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                     @endforeach
                     <div class="input-group shadow-sm">
                         <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                         <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 shadow-none" placeholder="Filter {{ request('type') == 'pack' ? 'packs' : 'bundles' }}">
                     </div>
                 </form>
            </div>
            
            <!-- Status Filter -->
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i> {{ request('status') ? ucfirst(request('status')) : 'All Status' }}
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li><a class="dropdown-item small {{ !request('status') ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['status' => null, 'page' => 1])) }}">All Status</a></li>
                    <li><a class="dropdown-item small {{ request('status') == 'active' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['status' => 'active', 'page' => 1])) }}">Active</a></li>
                    <li><a class="dropdown-item small {{ request('status') == 'draft' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['status' => 'draft', 'page' => 1])) }}">Draft</a></li>
                </ul>
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-2"></i> Sort
                </button>
                <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                    <li><a class="dropdown-item small {{ !request('sort') || request('sort') == 'newest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['sort' => 'newest', 'page' => 1])) }}">Newest First</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'oldest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['sort' => 'oldest', 'page' => 1])) }}">Oldest First</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'title_asc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['sort' => 'title_asc', 'page' => 1])) }}">Title (A-Z)</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'title_desc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.bundles', array_merge(request()->query(), ['sort' => 'title_desc', 'page' => 1])) }}">Title (Z-A)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-secondary">
            <thead class="bg-light text-uppercase small fw-medium text-muted">
                 <tr>
                    <th class="px-3 py-3" style="width: 50px;"><div class="form-check"><input type="checkbox" class="form-check-input"></div></th>
                    <th class="px-3 py-3">Title</th>
                    <th class="px-3 py-3">Status</th>
                    @if(request('type') != 'pool')
                        <th class="px-3 py-3 text-nowrap">Price</th>
                    @endif
                    <th class="px-3 py-3">Products</th>
                    <th class="px-3 py-3">Total Sales</th>
                    <th class="px-3 py-3" style="width: 80px;"></th>
                 </tr>
            </thead>
            <tbody class="border-top-0" id="bundles-table-body">
                @include('admin.bundles.partials.table')
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const tableBody = document.getElementById('bundles-table-body');
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

        // Pack Of Modal Logic
        const packProductSelect = document.getElementById('pack_product_id');
        const variantContainer = document.getElementById('variant_container');
        const packVariantSelect = document.getElementById('pack_variant_id');

        if (packProductSelect) {
            packProductSelect.addEventListener('change', function() {
                const productId = this.value;
                if (!productId) {
                    variantContainer.classList.add('d-none');
                    return;
                }

                const url = "{{ route('admin.products.variants', ['tenant' => request()->route('tenant'), 'id' => ':id']) }}".replace(':id', productId);
                fetch(url)
                    .then(response => response.json())
                    .then(variants => {
                        packVariantSelect.innerHTML = '<option value="">Choose a size...</option>';
                        variants.forEach(v => {
                            packVariantSelect.innerHTML += `<option value="${v.id}">${v.size} - {{ $currentTenant->currency ?? '₹' }}${v.price}</option>`;
                        });
                        variantContainer.classList.remove('d-none');
                    })
                    .catch(error => console.error('Error fetching variants:', error));
            });
        }
    });
</script>
<style>
    .text-decoration-hover-underline:hover { text-decoration: underline !important; }
    .hover-text-primary:hover { color: #8b5cf6 !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }

    /* Pagination Overrides */
    .page-link {
        color: #8b5cf6;
        border-color: #dee2e6;
    }
    .page-link:hover {
        color: #6d28d9;
        background-color: #e6f2f0;
        border-color: #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
        color: white;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endsection
