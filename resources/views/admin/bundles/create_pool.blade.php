@extends('layouts.admin')

@section('title', 'Create Bundle Pool')

@section('content')
<div class="container pb-5" style="max-width: 900px;">
    <form action="{{ route('admin.bundles.pool') }}" method="POST" id="poolForm">
        @csrf
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.bundles') }}" class="text-secondary hover-text-dark">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0 text-primary">Create bundle pool</h1>
            </div>
            <div class="d-flex gap-3">
                 <a href="{{ route('admin.bundles') }}" class="btn btn-white border shadow-sm text-secondary">Discard</a>
                <button type="submit" class="btn btn-info text-white shadow-sm">Save pool</button>
            </div>
        </div>
        
         <div class="row g-4">
            <!-- Left Column -->
            <div class="col-12 col-lg-8">
                <!-- Title -->
                 <div class="card border shadow-sm p-3 mb-4">
                     <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small">Pool Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Summer Essentials Pool" required>
                        </div>
                     </div>
                </div>

                <!-- Products -->
                <div class="card border shadow-sm p-3 mb-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Include Products in Pool</h2>
                    <div class="vstack gap-3">
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" id="product_search" class="form-control border-start-0 shadow-none" placeholder="Search and add products..." autocomplete="off">
                            </div>
                            <div id="search_results" class="position-absolute w-100 bg-white border rounded shadow-sm d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                <!-- Results populated by JS -->
                            </div>
                        </div>
                        
                        <div id="selected_products_container" class="border rounded d-none">
                            <div class="p-2 bg-light border-bottom d-flex align-items-center gap-3">
                                <span class="small fw-bold text-secondary flex-grow-1 ms-2">Product</span>
                                <div style="width: 20px;"></div>
                            </div>
                            <!-- Selected products will appear here -->
                        </div>
                        <div id="hidden_inputs"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-12 col-lg-4">
                <div class="card border shadow-sm p-3 mb-4">
                     <h2 class="h6 fw-bold text-secondary mb-3">Pool Rules</h2>
                     <div class="vstack gap-3">
                         <div>
                            <label class="form-label fw-medium text-secondary small">Required Quantity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 small">Buy</span>
                                <input type="number" name="min_quantity" class="form-control border-start-0" value="3" min="1" required>
                            </div>
                            <div class="form-text small mt-2">Number of products from this pool to get the discount.</div>
                         </div>
                         <div>
                            <label class="form-label fw-medium text-secondary small">Fixed Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 small">{{ $currentTenant->currency ?? '₹' }}</span>
                                <input type="number" name="discount_amount" class="form-control border-start-0" placeholder="100.00" step="0.01" min="0" required>
                            </div>
                            <div class="form-text small mt-2">This amount will be deducted from the total.</div>
                         </div>
                     </div>
                </div>

                <!-- Summary -->
                 <div class="card border shadow-sm p-3 mb-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Pool Summary</h2>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="fas fa-circle text-secondary me-2" style="font-size: 4px; vertical-align: middle;"></i><span id="summary_count">0</span> products included</li>
                    </ul>
                </div>
            </div>
         </div>
        


    </form>

    @php
        $productData = $products->map(function($p) {
            return [
                'id' => $p->id,
                'title' => $p->title,
                'price' => $p->variants->min('price') ?? 0,
                'image' => $p->images->first()?->path
            ];
        });
    @endphp

    <script>
        const allProducts = @json($productData);

        const searchInput = document.getElementById('product_search');
        const resultsContainer = document.getElementById('search_results');

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            if (query.length < 1) {
                resultsContainer.classList.add('d-none');
                return;
            }

            const matches = allProducts.filter(p => p.title.toLowerCase().includes(query));
            
            if (matches.length > 0) {
                resultsContainer.innerHTML = '';
                matches.forEach(product => {
                    // Check if already selected
                    if (document.querySelector(`input[name="product_ids[]"][value="${product.id}"]`)) return;

                    const div = document.createElement('div');
                    div.className = 'p-2 d-flex align-items-center gap-2 hover-bg-light cursor-pointer border-bottom';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 32px; height: 32px;">
                            ${product.image ? `<img src="/storage/${product.image}" class="w-100 h-100 object-fit-cover">` : `<i class="fas fa-image text-secondary opacity-50 small"></i>`}
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 small fw-medium text-dark">${product.title}</p>
                        </div>
                    `;
                    div.onclick = () => {
                        addProduct(product.id, product.title, product.price, product.image);
                        searchInput.value = '';
                        resultsContainer.classList.add('d-none');
                    };
                    resultsContainer.appendChild(div);
                });
                
                if (resultsContainer.children.length > 0) {
                    resultsContainer.classList.remove('d-none');
                } else {
                    resultsContainer.classList.add('d-none');
                }
            } else {
                resultsContainer.classList.add('d-none');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('d-none');
            }
        });

        function addProduct(id, title, price, imagePath) {
            const container = document.getElementById('selected_products_container');
            const hiddenInputs = document.getElementById('hidden_inputs');
            
            container.classList.remove('d-none');
            
            const uniqueId = 'product_' + id;

            // Add UI
            const div = document.createElement('div');
            div.className = 'p-3 d-flex align-items-center gap-3 border-bottom last-border-none product-item';
            div.id = `ui_${uniqueId}`;
            div.innerHTML = `
                <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 40px; height: 40px;">
                    ${imagePath ? `<img src="/storage/${imagePath}" class="w-100 h-100 object-fit-cover">` : `<i class="fas fa-image text-secondary opacity-50"></i>`}
                </div>
                <div class="flex-grow-1">
                    <span class="d-block small fw-medium text-dark">${title}</span>
                </div>
                <button type="button" onclick="removeProduct('${uniqueId}')" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(div);

            // Add Input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_ids[]';
            input.value = id;
            input.id = `input_${uniqueId}`;
            hiddenInputs.appendChild(input);
            
            updateSummary();
        }

        function removeProduct(uniqueId) {
            const uiEl = document.getElementById(`ui_${uniqueId}`);
            const inputEl = document.getElementById(`input_${uniqueId}`);
            
            if (uiEl) uiEl.remove();
            if (inputEl) inputEl.remove();

             const container = document.getElementById('selected_products_container');
             if (container.children.length === 0) container.classList.add('d-none');
             updateSummary();
        }

        function updateSummary() {
            const container = document.getElementById('selected_products_container');
            const products = container.querySelectorAll('.product-item');
            const count = products.length;
            document.getElementById('summary_count').innerText = count;
        }
    </script>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .cursor-pointer { cursor: pointer; }
    .last-border-none:last-child { border-bottom: none !important; }
</style>
@endsection
