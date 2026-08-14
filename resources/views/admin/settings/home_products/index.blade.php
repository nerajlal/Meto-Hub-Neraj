@extends('layouts.admin')

@section('title', 'Home Products')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold text-dark mb-0">Home Products</h1>
    <span class="badge bg-success rounded-pill shadow-sm py-2 px-3">{{ $selectedProducts->count() }} / 8 Selected</span>
</div>

@if(session('success'))
    <div class="alert alert-success shadow-sm border-0 mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger shadow-sm border-0 mb-4">{{ session('error') }}</div>
@endif

<div class="card border shadow-sm">
    <div class="card-header bg-light border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs m-0 border-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link active fw-medium py-3 px-4 rounded-0 border-0 text-secondary bg-transparent" id="selected-tab" data-bs-toggle="tab" href="#selected" role="tab">
                    Selected Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-medium py-3 px-4 rounded-0 border-0 text-secondary bg-transparent" id="all-tab" data-bs-toggle="tab" href="#all" role="tab">
                    All Products
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <!-- Selected Products Tab -->
            <div class="tab-pane fade show active" id="selected" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="px-2" style="width: 40px;"></th>
                                <th class="px-4 py-3 fw-medium">Product</th>
                                <th class="px-4 py-3 fw-medium">Price</th>
                                <th class="px-4 py-3 text-end fw-medium">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-home-products">
                            @forelse($selectedProducts as $item)
                            @if($item->product)
                            <tr class="draggable-item" draggable="true" data-id="{{ $item->id }}">
                                <td class="px-2">
                                    <div class="cursor-move text-secondary px-2 handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded border p-1 bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 48px; height: 48px;">
                                            @if($item->product->images->count() > 0)
                                                <img src="{{ Storage::url($item->product->images->first()->path) }}" alt="{{ $item->product->title }}" class="w-100 h-100 object-fit-cover rounded">
                                            @else
                                                <i class="fas fa-image text-secondary opacity-50"></i>
                                            @endif
                                        </div>
                                        <span class="fw-medium text-dark">{{ $item->product->title }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->product->variants->isNotEmpty())
                                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($item->product->variants->min('price'), 2) }}
                                    @else
                                        <span class="text-muted">No variants</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <form action="{{ route('admin.settings.home-products.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-white btn-sm border text-danger hover-bg-danger hover-text-white shadow-sm"><i class="fas fa-trash me-1"></i> Remove</button>
                                    </form>
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No products selected yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- All Products Tab -->
            <div class="tab-pane fade" id="all" role="tabpanel">
                 <div class="p-3 bg-light border-bottom">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="search-all-products" class="form-control border-start-0 ps-0 shadow-none" placeholder="Search products to add...">
                    </div>
                </div>
                <div id="all-products-container">
                    @include('admin.settings.home_products.partials.all_products_table')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: none !important;
        border-bottom: 2px solid transparent !important;
        margin-bottom: -1px;
    }
    .nav-tabs .nav-link.active {
        color: #202223 !important;
        background-color: transparent !important;
        border-bottom-color: #008060 !important;
        font-weight: 600;
    }
    .nav-tabs .nav-link:not(.active):hover {
        border-bottom-color: transparent !important;
        color: #008060 !important;
    }
    .hover-text-success:hover {
        color: #008060 !important;
    }
    .hover-bg-danger:hover {
        background-color: var(--bs-danger) !important;
        border-color: var(--bs-danger) !important;
        color: white !important;
    }
    .handle:hover { color: var(--bs-dark) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Drag and Drop Logic for Home Products
        const sortContainer = document.getElementById('sortable-home-products');
        if(sortContainer) {
            let draggables = sortContainer.querySelectorAll('.draggable-item');

            function initDraggables() {
                draggables.forEach(draggable => {
                    draggable.addEventListener('dragstart', () => {
                        draggable.classList.add('dragging');
                        draggable.classList.add('bg-light'); 
                        draggable.style.opacity = '0.5';
                    });

                    draggable.addEventListener('dragend', () => {
                        draggable.classList.remove('dragging');
                        draggable.classList.remove('bg-light');
                        draggable.style.opacity = '1';
                        saveOrder();
                    });
                });
            }

            initDraggables();

            sortContainer.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(sortContainer, e.clientY);
                const draggable = document.querySelector('.dragging');
                if (afterElement == null) {
                    sortContainer.appendChild(draggable);
                } else {
                    sortContainer.insertBefore(draggable, afterElement);
                }
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }

            function saveOrder() {
                const items = sortContainer.querySelectorAll('.draggable-item');
                const order = Array.from(items).map(item => item.getAttribute('data-id'));

                fetch('{{ route("admin.settings.home-products.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Optional: Show toast
                    }
                });
            }
        }

        // Live search for All Products
        const searchInput = document.getElementById('search-all-products');
        const container = document.getElementById('all-products-container');
        let debounceTimer;

        // Function to bind pagination links
        function bindPagination() {
            container.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchProducts(this.href);
                });
            });
        }

        function fetchProducts(url) {
            const currentUrl = new URL(url);
            currentUrl.searchParams.set('tab', 'all');
            currentUrl.searchParams.set('search', searchInput.value);

            fetch(currentUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                bindPagination();
            });
        }

        searchInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const url = new URL("{{ route('admin.settings.home-products') }}");
                fetchProducts(url);
            }, 300);
        });

        // Initial bind
        bindPagination();
    });
</script>
@endsection
