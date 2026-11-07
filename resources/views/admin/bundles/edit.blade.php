@extends('layouts.admin')

@section('title', 'Edit Bundle')

@section('content')
    <div class="container pb-5" style="max-width: 900px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.bundles') }}" class="text-secondary hover-text-dark">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 text-primary">Edit bundle</h1>
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
                            @include('admin.bundles.partials.product_picker')
                        @endif
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-12 col-lg-4">
                    @include('admin.bundles.partials.card_background')
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
                                    <option value="fixed" {{ $bundle->discount_type == 'fixed' ? 'selected' : '' }}>Fixed discount amount</option>
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
                <button type="submit" class="btn btn-primary shadow-sm">Update bundle</button>
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
