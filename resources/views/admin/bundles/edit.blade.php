@extends('layouts.admin')

@section('title', 'Edit Bundle')

@section('content')
    <div class="container pb-5" style="max-width: 900px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.bundles') }}" class="text-secondary hover-text-dark">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 text-dark">Edit bundle</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm mb-4 border-0">
                <h5 class="alert-heading fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.bundles.update', $bundle->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-12 col-lg-8">
                    <!-- Title -->
                    <div class="card border shadow-sm p-3 mb-4">
                        <div class="vstack gap-3">
                            <div>
                                <label class="form-label fw-medium text-secondary small">Title</label>
                                <input type="text" name="title" value="{{ $bundle->title }}" class="form-control"
                                    placeholder="e.g. Summer Essentials Bundle" required>
                            </div>
                            @if($bundle->type != 'pack')
                                <div>
                                    <label class="form-label fw-medium text-secondary small">Description</label>
                                    <textarea name="description" rows="4"
                                        class="form-control">{{ $bundle->description }}</textarea>
                                </div>
                            @endif
                            @if($bundle->type != 'pack')
                                <div>
                                    <label class="form-label fw-medium text-secondary small">Image (WebP, PNG, JPG, JPEG)</label>
                                    @if($bundle->image)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($bundle->image) }}" class="rounded border"
                                                style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control" accept="image/webp,image/png,image/jpeg,image/jpg">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Products / Pack Configuration -->
                    <div class="card border shadow-sm p-3 mb-4">
                        @if($bundle->type == 'pack')
                            <h2 class="h6 fw-bold text-secondary mb-3">Pack Configuration</h2>
                            <div class="alert alert-info border-0 small py-2 px-3 mb-3">
                                <i class="fas fa-info-circle me-1"></i> This is a "Pack Of" bundle. You can edit the title,
                                price, and status here.
                            </div>

                            @php $product = $bundle->products->first(); @endphp
                            @if($product)
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                    <div class="d-flex align-items-center justify-content-center bg-white border rounded overflow-hidden"
                                        style="width: 50px; height: 50px;">
                                        @if($product->images->first())
                                            <img src="{{ Storage::url($product->images->first()->path) }}"
                                                class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="fas fa-image text-secondary opacity-50"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="d-block fw-bold text-dark">{{ $product->title }}</span>
                                        <span class="badge bg-white border text-secondary fw-normal">
                                            {{ $product->pivot->quantity }}x
                                            @if($product->pivot->product_variant_id)
                                                {{ $product->variants->firstWhere('id', $product->pivot->product_variant_id)->size ?? '' }}
                                            @endif
                                        </span>
                                    </div>
                                    <input type="hidden" name="products[]" value="{{ $product->id }}">
                                    <input type="hidden" name="type" value="pack">
                                </div>
                            @endif
                        @else
                            <h2 class="h6 fw-bold text-secondary mb-3">Products</h2>
                            <div class="vstack gap-3">
                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" id="product_search" class="form-control border-start-0 shadow-none"
                                            placeholder="Search products..." autocomplete="off">
                                    </div>
                                    <div id="search_results"
                                        class="position-absolute w-100 bg-white border rounded shadow-sm d-none"
                                        style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                        <!-- Results populated by JS -->
                                    </div>
                                </div>

                                <div id="selected_products_container"
                                    class="border rounded {{ $bundle->products->isEmpty() ? 'd-none' : '' }}">
                                    @foreach($bundle->products as $index => $product)
                                        @php
                                            $uniqueId = 'product_' . $product->id . '_existing_' . $index;
                                        @endphp
                                        <div class="p-3 d-flex align-items-center gap-3 border-bottom last-border-none product-item"
                                            id="ui_{{ $uniqueId }}" data-price="{{ $product->variants->min('price') ?? 0 }}">
                                            <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden"
                                                style="width: 40px; height: 40px;">
                                                @if($product->images->first())
                                                    <img src="{{ Storage::url($product->images->first()->path) }}"
                                                        class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <i class="fas fa-image text-secondary opacity-50"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="d-block small fw-medium text-dark">{{ $product->title }}</span>
                                                <p class="mb-0 small text-muted">Starts at ₹
                                                    {{ $product->variants->min('price') ?? 0 }}</p>
                                            </div>
                                            <button type="button" onclick="removeProduct('{{ $uniqueId }}')"
                                                class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i
                                                    class="fas fa-times"></i></button>
                                        </div>
                                    @endforeach
                                </div>

                                <div id="hidden_inputs">
                                    @foreach($bundle->products as $index => $product)
                                        @php
                                            $uniqueId = 'product_' . $product->id . '_existing_' . $index;
                                        @endphp
                                        <input type="hidden" name="products[]" value="{{ $product->id }}"
                                            id="input_{{ $uniqueId }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-12 col-lg-4">
                    <!-- Status -->
                    <div class="card border shadow-sm p-3 mb-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Status</h2>
                        <select name="status" class="form-select">
                            <option value="active" {{ $bundle->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ $bundle->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <div class="card border shadow-sm p-3 mb-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Bundle Discount</h2>
                        <div class="vstack gap-3">
                            <div>
                                <label class="form-label fw-medium text-secondary small">Discount type</label>
                                <select name="discount_type" class="form-select">
                                    <option value="percentage" {{ $bundle->discount_type == 'percentage' ? 'selected' : '' }}>
                                        Percentage</option>
                                    <option value="fixed" {{ $bundle->discount_type == 'fixed' ? 'selected' : '' }}>Fixed
                                        price</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-medium text-secondary small">Discount value</label>
                                <input type="number" name="discount_value" value="{{ $bundle->discount_value }}"
                                    class="form-control" placeholder="10" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Purchase Quantity Limits -->
                    <div class="card border shadow-sm p-3 mb-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Purchase Limits (Wholesale/Promo)</h2>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-medium text-secondary small mb-1">Min Order Qty</label>
                                <input type="number" name="min_order_qty" value="{{ old('min_order_qty', $bundle->min_order_qty) }}" class="form-control" placeholder="e.g. 1" min="1">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium text-secondary small mb-1">Max Order Qty</label>
                                <input type="number" name="max_order_qty" value="{{ old('max_order_qty', $bundle->max_order_qty) }}" class="form-control" placeholder="e.g. 10" min="1">
                            </div>
                        </div>
                        <div class="form-text text-muted small mt-2">Leave blank to disable order constraints on this bundle.</div>
                    </div>

                    <!-- Summary -->
                    @if($bundle->type != 'pack')
                        <div class="card border shadow-sm p-3 mb-4">
                            <h2 class="h6 fw-bold text-secondary mb-3">Summary</h2>
                            <ul class="list-unstyled small text-muted mb-0">
                                <li><i class="fas fa-circle text-secondary me-2"
                                        style="font-size: 4px; vertical-align: middle;"></i><span
                                        id="summary_count">{{ $bundle->products->count() }}</span> products</li>
                                <li class="fw-bold text-dark pt-2">Final Price: <span id="summary_total">Calculated at
                                        checkout</span></li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div> 

            <div class="d-flex justify-content-end gap-3 pt-4 border-top mt-4">
                <button type="button" onclick="confirmDelete()"
                    class="btn btn-white border-danger text-danger hover-bg-danger-soft">Delete bundle</button>
                <button type="submit" class="btn btn-success shadow-sm">Update bundle</button>
            </div>
        </form>

        <form id="delete-form" action="{{ route('admin.bundles.destroy', $bundle->id) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function confirmDelete() {
                if (confirm('Are you sure you want to delete this bundle?')) {
                    document.getElementById('delete-form').submit();
                }
            }

            @php
                $productData = $products->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'title' => $p->title,
                        'price' => $p->variants->min('price') ?? 0,
                        'image' => $p->images->first()?->path
                    ];
                });
            @endphp

            const allProducts = @json($productData);

            const discountTypeSelect = document.querySelector('select[name="discount_type"]');
            const discountValueInput = document.querySelector('input[name="discount_value"]');

            // Add event listeners for discount changes
            discountTypeSelect.addEventListener('change', updateSummary);
    discountValueInput.addEventListener('input', updateSummary);

            // Initial Calculation
            document.addEventListener('DOMContentLoaded', updateSummary);

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
                        // Check if already selected - REMOVED to allow multiple of same product
                        // if (document.querySelector(`input[name="products[]"][value="${product.id}"]`)) return;

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
                // Check duplications - REMOVED
                // if (document.querySelector(`input[name="products[]"][value="${id}"]`)) return;

                const container = document.getElementById('selected_products_container');
    const hiddenInputs = document.getElementById('hidden_inputs');

    container.classList.remove('d-none');

                // Generate unique ID for this instance
                const uniqueId = 'product_' + id + '_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                // Add UI
                const div = document.createElement('div');
                div.className = 'p-3 d-flex align-items-center gap-3 border-bottom last-border-none product-item';
                div.dataset.price = price;
                div.id = `ui_${uniqueId}`;
                div.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 40px; height: 40px;">
                        ${imagePath ? `<img src="/storage/${imagePath}" class="w-100 h-100 object-fit-cover">` : `<i class="fas fa-image text-secondary opacity-50"></i>`}
                    </div>
                    <div class="flex-grow-1">
                        <span class="d-block small fw-medium text-dark">${title}</span>
                        <p class="mb-0 small text-muted">Starts at ₹ ${price}</p>
                    </div>
                    <button type="button" onclick="removeProduct('${uniqueId}')" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-times"></i></button>
                `;
                container.appendChild(div);

                // Add Input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'products[]';
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

                // Calculate total price
                let total = 0;
                products.forEach(p => {
                    total += parseFloat(p.dataset.price) || 0;
                });

                // Calculate discount
                const discountType = discountTypeSelect.value;
                const discountValue = parseFloat(discountValueInput.value) || 0;
                let finalPrice = total;

                if (discountValue > 0) {
                    if (discountType === 'percentage') {
                        finalPrice = total - (total * (discountValue / 100));
                    } else {
                        finalPrice = total - discountValue;
                    }
                }

                // Ensure non-negative
                finalPrice = Math.max(0, finalPrice);

                document.getElementById('summary_total').innerText = '₹ ' + finalPrice.toFixed(2);
            }
        </script>
    </div>
    <style>



               .hover-text-dark:hover { c
           olor: #343a40 !important; }


           .hover-text-danger:hover { co
           lor: var(--bs-danger) !important; }

           .hover-bg-danger-soft:hover { background-color: #fef2f2 !important; }
    </style>
@endsection
