@extends('layouts.admin')

@section('title', 'Edit Discount')

@section('content')
<div class="container pb-5" style="max-width: 900px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.discounts') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Edit discount</h1>
    </div>

    <form action="{{ route('admin.discounts.update', $discount->id) }}" method="POST">
        @csrf
        @method('PUT')
         <div class="row g-4">
            <!-- Left Column -->
            <div class="col-12 col-lg-8">
                <div class="vstack gap-4">
                    <!-- Product Search -->
                    <div class="card border shadow-sm p-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Product</h2>
                        <div class="position-relative">
                            <label class="form-label fw-medium text-secondary small mb-1">Search product</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-secondary border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" id="product_search" class="form-control border-start-0 shadow-none ps-0" placeholder="Search product name..." autocomplete="off">
                            </div>
                            <div id="search_results" class="position-absolute w-100 bg-white border rounded shadow-sm d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                <!-- Results populated by JS -->
                            </div>
                            <p class="small text-muted mt-1 mb-0">Search for a product to apply this coupon to.</p>
                        </div>
                        
                        <div id="selected_products_container" class="border rounded mt-3 {{ $discount->products->isEmpty() ? 'd-none' : '' }}">
                            @foreach($discount->products as $product)
                            <div class="p-3 d-flex align-items-center gap-3 border-bottom last-border-none">
                                <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 40px; height: 40px;">
                                    @if($product->images->first())
                                        <img src="{{ Storage::url($product->images->first()->path) }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <i class="fas fa-image text-secondary opacity-50"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <span class="d-block small fw-medium text-dark">{{ $product->title }}</span>
                                </div>
                                <button type="button" onclick="removeProduct(this, '{{ $product->id }}')" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        
                        <div id="hidden_inputs">
                             @foreach($discount->products as $product)
                                <input type="hidden" name="products[]" value="{{ $product->id }}" id="input_product_{{ $product->id }}">
                             @endforeach
                        </div>
                    </div>

                    <!-- Discount Code -->
                    <div class="card border shadow-sm p-4">
                         <h2 class="h6 fw-bold text-secondary mb-3">Discount code</h2>
                         <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Code</label>
                            <div class="input-group">
                                <input type="text" name="code" value="{{ $discount->code }}" id="discountCodeInput" class="form-control text-uppercase fw-medium" placeholder="e.g. SUMMER20" required>
                                <button type="button" id="generateCodeBtn" class="btn btn-outline-success">Generate</button>
                            </div>
                         </div>
                    </div>

                    <!-- Value -->
                    <div class="card border shadow-sm p-4">
                         <h2 class="h6 fw-bold text-secondary mb-3">Value</h2>
                         <div class="row g-3">
                             <div class="col-12 col-md-6">
                                <label class="form-label fw-medium text-secondary small mb-1">Discount type</label>
                                 <select name="type" class="form-select">
                                    <option value="percentage" {{ $discount->type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed" {{ $discount->type == 'fixed' ? 'selected' : '' }}>Fixed amount</option>
                                </select>
                             </div>
                             <div class="col-12 col-md-6">
                                <label class="form-label fw-medium text-secondary small mb-1">Discount value</label>
                                <input type="number" name="value" value="{{ $discount->value }}" class="form-control" placeholder="10" step="0.01" min="0" required>
                             </div>
                         </div>
                    </div>


                </div>
            </div>

            <!-- Right Column -->
            <div class="col-12 col-lg-4">
                <div class="vstack gap-4">
                     <!-- Status -->
                    <div class="card border shadow-sm p-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Status</h2>
                        <select name="status" class="form-select">
                            <option value="active" {{ $discount->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ $discount->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <!-- Active Dates -->
                     <div class="card border shadow-sm p-4">
                         <h2 class="h6 fw-bold text-secondary mb-3">Active dates</h2>
                         <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium text-secondary small mb-1">Start date</label>
                                <input type="date" name="starts_at" value="{{ $discount->starts_at->format('Y-m-d') }}" class="form-control" required>
                            </div>
                             <div class="col-12">
                                <label class="form-label fw-medium text-secondary small mb-1">End date</label>
                                <input type="date" name="ends_at" value="{{ $discount->ends_at ? $discount->ends_at->format('Y-m-d') : '' }}" class="form-control">
                            </div>
                         </div>
                    </div>

                    <!-- Summary -->
                     <div class="card border shadow-sm p-4">
                        <h2 class="h6 fw-bold text-secondary mb-3">Summary</h2>
                        <h3 class="h6 fw-bold text-primary mb-2" id="summary_code">{{ $discount->code }}</h3>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            <li class="mb-1"><i class="fas fa-circle text-secondary me-2" style="font-size: 4px; vertical-align: middle;"></i>Applies to <span id="summary_products_count">{{ $discount->products->count() }}</span> products</li>
                            <li class="mb-1"><i class="fas fa-circle text-secondary me-2" style="font-size: 4px; vertical-align: middle;"></i><span id="summary_value">--</span></li>
                            <li><i class="fas fa-circle text-secondary me-2" style="font-size: 4px; vertical-align: middle;"></i>Active from <span id="summary_date">{{ $discount->starts_at->format('Y-m-d') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
         </div>
        
        <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
             <button type="button" onclick="confirmDelete()" class="btn btn-white border border-danger text-danger shadow-sm fw-medium hover-bg-danger-soft">Delete discount</button>
            <button type="submit" class="btn btn-primary shadow-sm fw-medium">Update discount</button>
        </div>

    </form>
    
    <form id="delete-form" action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .hover-bg-danger-soft:hover { background-color: #fef2f2 !important; }
    .last-border-none:last-child { border-bottom: none !important; }
</style>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this discount?')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Summary Update Logic
        const codeInput = document.getElementById('discountCodeInput');
        const summaryCode = document.getElementById('summary_code');
        const summaryDate = document.getElementById('summary_date');
        const startDateInput = document.querySelector('input[name="starts_at"]');
        const summaryValue = document.getElementById('summary_value');
        const valueInput = document.querySelector('input[name="value"]');
        const typeSelect = document.querySelector('select[name="type"]');

        function updateSummary() {
            summaryCode.innerText = codeInput.value || '--';
            summaryDate.innerText = startDateInput.value || 'today';
            const value = valueInput.value || 0;
            const type = typeSelect.value === 'percentage' ? '%' : ' AED';
            summaryValue.innerText = value + ' ' + type + ' off';
        }

        codeInput.addEventListener('input', updateSummary);
        startDateInput.addEventListener('change', updateSummary);
        valueInput.addEventListener('input', updateSummary);
        typeSelect.addEventListener('change', updateSummary);
        
        // Initial summary update
        updateSummary();

        // Check Code Generator
        const generateBtn = document.getElementById('generateCodeBtn');
        generateBtn.addEventListener('click', function() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            const length = 10;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            codeInput.value = result;
            updateSummary();
        });

        // Product Search Logic
        @php
            $productData = $products->map(function($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'image' => $p->images->first()?->path
                ];
            });
        @endphp
        
        const allProducts = @json($productData);
        const searchInput = document.getElementById('product_search');
        const resultsContainer = document.getElementById('search_results');
        const selectedContainer = document.getElementById('selected_products_container');
        const hiddenInputs = document.getElementById('hidden_inputs');
        const summaryProductsCount = document.getElementById('summary_products_count');

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
                    if (document.querySelector(`input[name="products[]"][value="${product.id}"]`)) return;

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
                        addProduct(product.id, product.title, product.image);
                        searchInput.value = '';
                        resultsContainer.classList.add('d-none');
                    };
                    resultsContainer.appendChild(div);
                });
                resultsContainer.classList.remove('d-none');
            } else {
                resultsContainer.classList.add('d-none');
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.add('d-none');
            }
        });

        function addProduct(id, title, imagePath) {
            // Clear existing selections for single product mode
            selectedContainer.innerHTML = '';
            hiddenInputs.innerHTML = '';
            selectedContainer.classList.remove('d-none');
            
            const div = document.createElement('div');
            div.className = 'p-3 d-flex align-items-center gap-3 border-bottom last-border-none';
            div.innerHTML = `
                <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 40px; height: 40px;">
                    ${imagePath ? `<img src="/storage/${imagePath}" class="w-100 h-100 object-fit-cover">` : `<i class="fas fa-image text-secondary opacity-50"></i>`}
                </div>
                <div class="flex-grow-1">
                    <span class="d-block small fw-medium text-dark">${title}</span>
                </div>
                <button type="button" onclick="removeProduct(this, '${id}')" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-times"></i></button>
            `;
            selectedContainer.appendChild(div);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'products[]';
            input.value = id;
            input.id = `input_product_${id}`;
            hiddenInputs.appendChild(input);
            
            summaryProductsCount.innerText = hiddenInputs.children.length;
        }

        window.removeProduct = function(btn, id) {
            btn.closest('div').remove();
            document.getElementById(`input_product_${id}`).remove();
             if (selectedContainer.children.length === 0) selectedContainer.classList.add('d-none');
             summaryProductsCount.innerText = hiddenInputs.children.length;
        }
    });
</script>
@endpush
