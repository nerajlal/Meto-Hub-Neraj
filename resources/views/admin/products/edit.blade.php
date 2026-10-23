@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid" style="max-width: 1200px; padding-bottom: 2.5rem;">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.products') }}" class="text-secondary text-decoration-none">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 fw-bold text-primary mb-0">{{ $product->title }}</h1>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-medium text-uppercase small">Active</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products') }}" class="btn btn-white border shadow-sm text-secondary">Discard</a>
                <button type="submit" class="btn btn-primary shadow-sm">Save</button>
            </div>
        </div>

        <div class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            
            <!-- Basic Info -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title', $product->title) }}" class="form-control shadow-sm" required>
                    </div>
                    <div>
                        <label class="form-label fw-medium text-secondary small mb-1">Description</label>
                        <textarea name="description" rows="6" class="form-control shadow-sm">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-semibold text-secondary mb-0">Media</h2>
                        <!-- <button type="button" id="toggleUrlBtn" class="btn btn-link btn-sm text-decoration-none p-0" onclick="toggleUrlInput()">Add from URL</button> -->
                    </div>
                    
                    <!-- URL Input Section - HIDDEN -->
                    <!--
                    <div id="urlInputContainer" class="d-none mb-3 p-3 bg-light rounded border">
                        <label class="form-label fw-medium text-secondary extra-small mb-1">Image or Video URL</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="mediaUrl" class="form-control" placeholder="https://example.com/image.jpg">
                            <button type="button" onclick="addMediaFromUrl()" class="btn btn-white border text-secondary">Add</button>
                        </div>
                        <p class="text-muted extra-small mt-1 mb-0">Supports types: .jpg, .png, .gif, .mp4, .mov</p>
                    </div>
                    -->

                    <input type="file" id="media_upload" name="media[]" multiple accept=".webp,.png,.jpg,.jpeg" class="d-none" onchange="handleFileSelect(event)">
                    
                    <!-- Preview Grid -->
                    <div id="media_preview_grid" class="row g-3 mb-3 {{ $product->images->isEmpty() ? 'd-none' : '' }}">
                         <!-- Existing Images -->
                         @foreach($product->images as $image)
                             <div class="col-6 col-md-3 existing-image" data-id="{{ $image->id }}" style="cursor: move;">
                                 <input type="hidden" name="media_order[]" value="{{ $image->id }}">
                                 <div class="ratio ratio-1x1 bg-light rounded border overflow-hidden position-relative group-hover-container d-flex align-items-center justify-content-center">
                                     <img src="{{ asset('storage/' . $image->path) }}" class="w-100 h-100 object-fit-cover">
                                     <button type="button" onclick="markImageForDeletion(this, {{ $image->id }})" class="btn btn-white btn-sm rounded-circle shadow-sm position-absolute top-0 end-0 m-1 opacity-0 group-hover-visible transition-opacity text-danger p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                        <i class="fas fa-trash extra-small"></i>
                                    </button>
                                 </div>
                             </div>
                         @endforeach
                    </div>

                    <div id="media_dropzone" class="border opacity-50 border-2 border-dashed rounded py-5 text-center bg-light bg-opacity-25 cursor-pointer hover-bg-light transition-base" onclick="document.getElementById('media_upload').click()">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-image text-secondary opacity-50 fs-2 mb-2"></i>
                            <span class="text-secondary small fw-medium mb-1">Add images</span>
                            <p class="text-muted extra-small mb-0">Accepts .webp, .png, .jpg, .jpeg images</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Container for deleted image IDs -->
            <div id="deleted_images_container"></div>

            <!-- SortableJS -->
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

            <script>
                // Global DataTransfer to accumulate new files across multiple clicks/selections
                let newMediaDt = new DataTransfer();

                // Initialize Sortable
                document.addEventListener('DOMContentLoaded', function() {
                    var el = document.getElementById('media_preview_grid');
                    var sortable = Sortable.create(el, {
                        animation: 150,
                        handle: '.existing-image', // Allow dragging by the whole item
                        draggable: '.existing-image', // Only existing images are draggable for now
                        onEnd: function (evt) {
                            // Optional: logic to handle drop event if needed
                        }
                    });
                });

                // Convert any image file to WebP using HTML5 Canvas
                async function convertToWebp(file) {
                    if (file.type === 'image/webp') {
                        return file;
                    }
                    return new Promise((resolve) => {
                        const img = new Image();
                        const url = URL.createObjectURL(file);
                        img.onload = function() {
                            URL.revokeObjectURL(url);
                            const canvas = document.createElement('canvas');
                            let width = img.naturalWidth || img.width;
                            let height = img.naturalHeight || img.height;
                            const maxDimension = 1600; // Optimal for web product photos

                            if (width > maxDimension || height > maxDimension) {
                                if (width > height) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                } else {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    const newName = file.name.replace(/\.[^/.]+$/, "") + ".webp";
                                    const webpFile = new File([blob], newName, { type: 'image/webp' });
                                    resolve(webpFile);
                                } else {
                                    resolve(file); // Fallback to original
                                }
                            }, 'image/webp', 0.85); // 85% high quality, massive size savings
                        };
                        img.onerror = function() {
                            URL.revokeObjectURL(url);
                            resolve(file);
                        };
                        img.src = url;
                    });
                }

                async function handleFileSelect(event) {
                    const files = Array.from(event.target.files || []);
                    const previewGrid = document.getElementById('media_preview_grid');
                    const fileInput = document.getElementById('media_upload');
                    
                    if (files.length === 0) return;

                    // Existing non-deleted images in DOM
                    const existingCount = document.querySelectorAll('.existing-image').length;
                    const currentNewCount = newMediaDt.items.length;
                    const incomingCount = files.length;
                    
                    if ((existingCount + currentNewCount + incomingCount) > 5) {
                        alert(`You can only have a maximum of 5 images. You currently have ${existingCount} saved image(s), ${currentNewCount} new image(s) queued, and are trying to add ${incomingCount} more.`);
                        fileInput.files = newMediaDt.files;
                        return;
                    }

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        
                        const allowedTypes = ['image/webp', 'image/png', 'image/jpeg', 'image/jpg'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Only WebP, PNG, JPG, and JPEG images are allowed: ' + file.name);
                            continue;
                        }

                        // Convert to lightweight WebP
                        const webpFile = await convertToWebp(file);

                        // Accumulate in DataTransfer
                        newMediaDt.items.add(webpFile);

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            createPreviewItem(e.target.result, 'image', webpFile);
                        };
                        reader.readAsDataURL(webpFile);
                    }

                    // Keep input.files in sync with all accumulated files
                    fileInput.files = newMediaDt.files;
                    previewGrid.classList.remove('d-none');
                }

                function createPreviewItem(src, type, fileObj) {
                    const previewGrid = document.getElementById('media_preview_grid');
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-3 new-image-preview';
                    col.fileRef = fileObj;
                    
                    const div = document.createElement('div');
                    div.className = 'ratio ratio-1x1 bg-dark rounded border overflow-hidden position-relative group-hover-container';
                    
                    let content = '';
                    if (type === 'video') {
                        content = `<video src="${src}" class="w-100 h-100 object-fit-cover opacity-75"></video><i class="fas fa-play position-absolute top-50 start-50 translate-middle text-white fs-3 opacity-75"></i>`;
                    } else {
                        content = `<img src="${src}" class="w-100 h-100 object-fit-cover">`;
                    }

                    div.innerHTML = `
                        ${content}
                        <button type="button" onclick="removeNewMediaItem(this)" class="btn btn-white btn-sm rounded-circle shadow-sm position-absolute top-0 end-0 m-1 opacity-0 group-hover-visible transition-opacity text-danger p-0 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                            <i class="fas fa-trash extra-small"></i>
                        </button>
                    `;
                    col.appendChild(div);
                    previewGrid.appendChild(col);
                }

                function removeNewMediaItem(btn) {
                    const col = btn.closest('.new-image-preview');
                    if (col) {
                        col.remove();
                    }
                    syncNewMediaFromDOM();
                }

                function syncNewMediaFromDOM() {
                    const updatedDt = new DataTransfer();
                    const newPreviews = document.querySelectorAll('.new-image-preview');
                    newPreviews.forEach(item => {
                        if (item.fileRef) {
                            updatedDt.items.add(item.fileRef);
                        }
                    });
                    newMediaDt = updatedDt;
                    document.getElementById('media_upload').files = newMediaDt.files;

                    const previewGrid = document.getElementById('media_preview_grid');
                    const totalRemaining = previewGrid.querySelectorAll('.existing-image, .new-image-preview').length;
                    if (totalRemaining === 0) {
                        previewGrid.classList.add('d-none');
                    }
                }

                function markImageForDeletion(btn, id) {
                    btn.closest('.col-6').remove();
                    const container = document.getElementById('deleted_images_container');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_images[]';
                    input.value = id;
                    container.appendChild(input);

                    const previewGrid = document.getElementById('media_preview_grid');
                    const totalRemaining = previewGrid.querySelectorAll('.existing-image, .new-image-preview').length;
                    if (totalRemaining === 0) {
                        previewGrid.classList.add('d-none');
                    }
                }
            </script>

            <!-- Variants Section -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-semibold text-secondary mb-0">Product Variants (Wholesale & Retail)</h2>
                        <button type="button" class="btn btn-outline-success btn-sm fw-medium" onclick="addVariantRow()">+ Add Variant</button>
                    </div>
                    <div id="variants-container" class="vstack gap-3">
                        @foreach($product->variants as $variant)
                        <div class="border rounded p-3 bg-light bg-opacity-50 variant-row">
                            <input type="hidden" name="variants[{{ $loop->index }}][id]" value="{{ $variant->id }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-3">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Variant Name (e.g. 1kg, Pack of 10)</label>
                                    <input type="text" class="form-control form-control-sm shadow-sm variant-size-input" name="variants[{{ $loop->index }}][size]" value="{{ $variant->size }}" placeholder="e.g. 1kg" required>
                                </div>
                                <div class="col-4 col-md-3">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Stock</label>
                                    <input type="number" class="form-control form-control-sm shadow-sm variant-stock-input" name="variants[{{ $loop->index }}][stock]" value="{{ $variant->stock }}" placeholder="0">
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                    <input type="text" class="form-control form-control-sm shadow-sm variant-price-input" name="variants[{{ $loop->index }}][price]" value="{{ $variant->price }}" placeholder="0.00" required>
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Compare Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                    <input type="text" class="form-control form-control-sm shadow-sm variant-compare-input" name="variants[{{ $loop->index }}][compare_at_price]" value="{{ $variant->compare_at_price }}" placeholder="0.00">
                                </div>
                                <div class="col-12 col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeVariantRow(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Volume Deals (Pack Of) Section -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-semibold text-secondary mb-0">Volume Deals ("Pack Of" Discounts)</h2>
                        <button type="button" class="btn btn-outline-success btn-sm fw-medium" onclick="addPackRow()">+ Add Pack Deal</button>
                    </div>
                    <div id="packs-container" class="vstack gap-3">
                        @foreach($packDeals as $pIndex => $pack)
                        @php
                            $variantId = $pack->products->first()->pivot->product_variant_id;
                            $qty = $pack->products->first()->pivot->quantity;
                            $packPrice = $pack->total_price;
                            $currentVariant = $product->variants->firstWhere('id', $variantId);
                            $currentSize = $currentVariant ? $currentVariant->size : '';
                        @endphp
                        <div class="border rounded p-3 bg-light bg-opacity-50 pack-row">
                            <input type="hidden" name="packs[{{ $pIndex }}][id]" value="{{ $pack->id }}">
                            <input type="hidden" class="pack-variant-size-hidden" name="packs[{{ $pIndex }}][variant_size]" value="{{ $currentSize }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Select Variant</label>
                                    <select class="form-select form-select-sm shadow-sm pack-variant-select" name="packs[{{ $pIndex }}][variant_id]" data-temp-size="{{ $currentSize }}" required onchange="const activeOption = this.options[this.selectedIndex]; this.closest('.pack-row').querySelector('.pack-variant-size-hidden').value = activeOption ? (activeOption.getAttribute('data-size') || '') : '';">
                                        @foreach($product->variants as $v)
                                            <option value="{{ $v->id }}" @selected($v->id == $variantId) data-size="{{ $v->size }}">{{ $v->size }} ({{ $currentTenant->currency ?? '₹' }}{{ $v->price }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Quantity in Pack</label>
                                    <input type="number" class="form-control form-control-sm shadow-sm pack-qty-input" name="packs[{{ $pIndex }}][quantity]" value="{{ $qty }}" min="2" placeholder="e.g. 6" required>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small fw-medium text-muted mb-1">Special Pack Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                    <input type="text" class="form-control form-control-sm shadow-sm pack-price-input" name="packs[{{ $pIndex }}][pack_price]" value="{{ $packPrice }}" placeholder="0.00" required>
                                </div>
                                <div class="col-12 col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removePackRow(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <script>
                function reindexVariants() {
                    const rows = document.querySelectorAll('.variant-row');
                    rows.forEach((row, index) => {
                        const idInput = row.querySelector('input[type="hidden"]');
                        if (idInput) {
                            idInput.name = `variants[${index}][id]`;
                        }
                        
                        const sizeInput = row.querySelector('.variant-size-input');
                        if (sizeInput) {
                            sizeInput.name = `variants[${index}][size]`;
                        }
                        
                        const stockInput = row.querySelector('.variant-stock-input');
                        if (stockInput) {
                            stockInput.name = `variants[${index}][stock]`;
                        }
                        
                        const priceInput = row.querySelector('.variant-price-input');
                        if (priceInput) {
                            priceInput.name = `variants[${index}][price]`;
                        }
                        
                        const compareInput = row.querySelector('.variant-compare-input');
                        if (compareInput) {
                            compareInput.name = `variants[${index}][compare_at_price]`;
                        }
                    });
                    updatePackVariantOptions();
                }

                function reindexPacks() {
                    const rows = document.querySelectorAll('.pack-row');
                    rows.forEach((row, index) => {
                        const idInput = row.querySelector('input[type="hidden"]');
                        if (idInput) {
                            idInput.name = `packs[${index}][id]`;
                        }
                        
                        const select = row.querySelector('.pack-variant-select');
                        if (select) {
                            select.name = `packs[${index}][variant_id]`;
                        }
                        
                        const sizeHidden = row.querySelector('.pack-variant-size-hidden');
                        if (sizeHidden) {
                            sizeHidden.name = `packs[${index}][variant_size]`;
                        }
                        
                        const qtyInput = row.querySelector('.pack-qty-input');
                        if (qtyInput) {
                            qtyInput.name = `packs[${index}][quantity]`;
                        }
                        
                        const priceInput = row.querySelector('.pack-price-input');
                        if (priceInput) {
                            priceInput.name = `packs[${index}][pack_price]`;
                        }
                    });
                }

                function updatePackVariantOptions() {
                    const variants = [];
                    document.querySelectorAll('.variant-row').forEach(row => {
                        const idInput = row.querySelector('input[type="hidden"]');
                        const idVal = idInput ? idInput.value : '';
                        const sizeInput = row.querySelector('.variant-size-input');
                        const sizeVal = sizeInput ? sizeInput.value.trim() : '';
                        const priceInput = row.querySelector('.variant-price-input');
                        const priceVal = priceInput ? priceInput.value.trim() : '';
                        
                        if (sizeVal) {
                            variants.push({
                                id: idVal,
                                size: sizeVal,
                                price: priceVal ? '{{ $currentTenant->currency ?? '₹' }}' + priceVal : ''
                            });
                        }
                    });

                    document.querySelectorAll('.pack-row').forEach(row => {
                        const select = row.querySelector('.pack-variant-select');
                        const sizeHidden = row.querySelector('.pack-variant-size-hidden');
                        
                        const selectedVal = select.value;
                        const selectedSize = select.options[select.selectedIndex]?.getAttribute('data-size') || select.getAttribute('data-temp-size');
                        
                        select.innerHTML = '<option value="">Choose variant...</option>';
                        variants.forEach(v => {
                            const option = document.createElement('option');
                            option.value = v.id;
                            option.setAttribute('data-size', v.size);
                            option.text = `${v.size} (${v.price})`;
                            
                            if (v.id && selectedVal === v.id) {
                                option.selected = true;
                            } else if (v.size === selectedSize) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });

                        // Set the hidden size input
                        if (sizeHidden) {
                            const activeOption = select.options[select.selectedIndex];
                            sizeHidden.value = activeOption ? (activeOption.getAttribute('data-size') || '') : '';
                        }
                    });
                }

                function addVariantRow() {
                    const container = document.getElementById('variants-container');
                    const index = container.querySelectorAll('.variant-row').length;
                    const row = document.createElement('div');
                    row.className = 'border rounded p-3 bg-light bg-opacity-50 variant-row';
                    row.innerHTML = `
                        <input type="hidden" name="variants[${index}][id]" value="">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Variant Name (e.g. 1kg, Pack of 10)</label>
                                <input type="text" class="form-control form-control-sm shadow-sm variant-size-input" name="variants[${index}][size]" placeholder="e.g. 1kg" required>
                            </div>
                            <div class="col-4 col-md-3">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Stock</label>
                                <input type="number" class="form-control form-control-sm shadow-sm variant-stock-input" name="variants[${index}][stock]" placeholder="0">
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                <input type="text" class="form-control form-control-sm shadow-sm variant-price-input" name="variants[${index}][price]" placeholder="0.00" required>
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Compare Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                <input type="text" class="form-control form-control-sm shadow-sm variant-compare-input" name="variants[${index}][compare_at_price]" placeholder="0.00">
                            </div>
                            <div class="col-12 col-md-2 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeVariantRow(this)">Remove</button>
                            </div>
                        </div>
                    `;
                    container.appendChild(row);
                    reindexVariants();
                }

                function removeVariantRow(button) {
                    const row = button.closest('.variant-row');
                    const container = document.getElementById('variants-container');
                    if (container.children.length > 1) {
                        row.remove();
                        reindexVariants();
                    } else {
                        alert('You must have at least one variant.');
                    }
                }

                function addPackRow() {
                    const container = document.getElementById('packs-container');
                    const index = container.querySelectorAll('.pack-row').length;
                    const row = document.createElement('div');
                    row.className = 'border rounded p-3 bg-light bg-opacity-50 pack-row';
                    row.innerHTML = `
                        <input type="hidden" name="packs[${index}][id]" value="">
                        <input type="hidden" class="pack-variant-size-hidden" name="packs[${index}][variant_size]" value="">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-4">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Select Variant</label>
                                <select class="form-select form-select-sm shadow-sm pack-variant-select" name="packs[${index}][variant_id]" required onchange="const activeOption = this.options[this.selectedIndex]; this.closest('.pack-row').querySelector('.pack-variant-size-hidden').value = activeOption ? (activeOption.getAttribute('data-size') || '') : '';">
                                    <option value="">Choose variant...</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Quantity in Pack</label>
                                <input type="number" class="form-control form-control-sm shadow-sm pack-qty-input" name="packs[${index}][quantity]" min="2" placeholder="e.g. 6" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label extra-small fw-medium text-muted mb-1">Special Pack Price ({{ $currentTenant->currency ?? '₹' }})</label>
                                <input type="text" class="form-control form-control-sm shadow-sm pack-price-input" name="packs[${index}][pack_price]" placeholder="0.00" required>
                            </div>
                            <div class="col-12 col-md-2 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removePackRow(this)">Remove</button>
                            </div>
                        </div>
                    `;
                    container.appendChild(row);
                    updatePackVariantOptions();
                    reindexPacks();
                }

                function removePackRow(button) {
                    const row = button.closest('.pack-row');
                    row.remove();
                    reindexPacks();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('variants-container');
                    if (container && container.children.length === 0) {
                        addVariantRow();
                    } else {
                        updatePackVariantOptions();
                    }

                    // Add live updates when typing variant sizes/prices
                    container.addEventListener('input', function(e) {
                        if (e.target.classList.contains('variant-size-input') || e.target.classList.contains('variant-price-input')) {
                            updatePackVariantOptions();
                        }
                    });
                });
            </script>

        </div>

        <!-- Right Column -->
        <div class="col-12 col-lg-4">
            
            <!-- Status -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold text-secondary mb-3">Product Status</h2>
                    <select name="status" class="form-select shadow-sm mb-3">
                        <option value="active" @selected(old('status', $product->status) == 'active')>Active</option>
                        <option value="draft" @selected(old('status', $product->status) == 'draft')>Draft</option>
                    </select>

                    <div class="form-check form-switch mt-4">
                        <input type="hidden" name="continue_selling_when_out_of_stock" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="continue_selling" name="continue_selling_when_out_of_stock" value="1" @checked(old('continue_selling_when_out_of_stock', $product->continue_selling_when_out_of_stock))>
                        <label class="form-check-label fw-medium text-secondary small" for="continue_selling">Open when out of stock</label>
                        <div class="form-text text-muted extra-small">Allow customers to purchase this item even when stock reaches 0.</div>
                    </div>
                </div>
            </div>

            <!-- Organization -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold text-secondary mb-3">Product Organization</h2>
                    <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Product type</label>
                            <input type="text" name="type" value="{{ old('type', $product->type) }}" class="form-control shadow-sm">
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Vendor</label>
                            <input type="text" name="vendor" value="{{ old('vendor', $product->vendor) }}" class="form-control shadow-sm" placeholder="e.g. Local Farms">
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Collections</label>
                            <select name="collection_id" class="form-select shadow-sm">
                                <option value="">Select a collection</option>
                                @foreach($collections as $collection)
                                    <option value="{{ $collection->id }}" @selected(old('collection_id', $product->collection_id) == $collection->id)>{{ $collection->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Tags</label>
                            @php
                                $currentTags = is_array($product->tags) ? implode(',', $product->tags) : '';
                            @endphp
                            <input name="tags_json" class="form-control shadow-sm" value="{{ old('tags_json', $currentTags) }}" placeholder="e.g. New, Bestseller">
                            <div class="form-text text-muted extra-small">Add tags to categorize this product. The last tag will appear on product cards.</div>
                        </div>
                </div>
                </div>
            </div>

            <!-- Purchase Quantity Limits -->
            <div class="card border shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold text-secondary mb-3">Purchase Limits (Wholesale/Promo)</h2>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small mb-1">Min Order Qty</label>
                            <input type="number" name="min_order_qty" value="{{ old('min_order_qty', $product->min_order_qty) }}" class="form-control shadow-sm" placeholder="e.g. 1" min="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small mb-1">Max Order Qty</label>
                            <input type="number" name="max_order_qty" value="{{ old('max_order_qty', $product->max_order_qty) }}" class="form-control shadow-sm" placeholder="e.g. 10" min="1">
                        </div>
                    </div>
                    <div class="form-text text-muted small mt-2">Leave blank to disable order constraints on this product.</div>
                </div>
            </div>



        </div>
        
        <div class="col-12 d-flex justify-content-end gap-2 border-top pt-3">
            <button type="button" class="btn btn-danger-soft text-danger me-auto" onclick="if(confirm('Delete this product?')) document.getElementById('delete-product-form').submit()">Delete product</button>
            <button type="button" class="btn btn-white border shadow-sm text-secondary">Discard</button>
            <button type="submit" class="btn btn-primary shadow-sm">Save</button>
        </div>
        </div> <!-- Close row g-4 -->
    <div class="card my-4"><div class="card-body">@include('admin.settings.partials.seo-fields', ['entity'=>$product])</div></div>
</form>
    
    <form id="delete-product-form" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
<style>
    .group-hover-visible { visibility: hidden; }
    .group-hover-container:hover .group-hover-visible { visibility: visible; opacity: 1 !important; }
    .extra-small { font-size: 0.75rem; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .cursor-pointer { cursor: pointer; }
    .btn-danger-soft { background-color: rgba(220, 53, 69, 0.1); border-color: transparent; }
    .btn-danger-soft:hover { background-color: rgba(220, 53, 69, 0.2); }
</style>

<!-- Tagify CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.querySelector('input[name=tags_json]');
        var whitelist = {!! json_encode($allTags ?? []) !!};
        
        new Tagify(input, {
            whitelist: whitelist,
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: false
            }
        });
    });
</script>
@endsection
