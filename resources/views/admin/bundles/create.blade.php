@extends('layouts.admin')

@section('title', 'Create Bundle')

@section('content')
<div class="container pb-5" style="max-width: 900px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.bundles') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Create bundle</h1>
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

    <form action="{{ route('admin.bundles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
         <div class="row g-4">
            <!-- Left Column -->
            <div class="col-12 col-lg-8">
                <!-- Title -->
                 <div class="card border shadow-sm p-3 mb-4">
                     <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="e.g. Summer Essentials Bundle" required>
                        </div>
                         <div>
                            <label class="form-label fw-medium text-secondary small">Description</label>
                            <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                        </div>
                         <div>
                            <label class="form-label fw-medium text-secondary small">Image (WebP, PNG, JPG, JPEG)</label>
                            <input type="file" name="image" class="form-control" accept="image/webp,image/png,image/jpeg,image/jpg">
                        </div>
                     </div>
                </div>

                <!-- Products -->
                <div class="card border shadow-sm p-3 mb-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Products</h2>
                    @include('admin.bundles.partials.product_picker')
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-12 col-lg-4">
                 <!-- Status -->
                <div class="card border shadow-sm p-3 mb-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Status</h2>
                    <select name="status" class="form-select">
                        <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                        <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                    </select>
                </div>

                 <div class="card border shadow-sm p-3 mb-4">
                     <h2 class="h6 fw-bold text-secondary mb-3">Bundle Discount</h2>
                     <div class="vstack gap-3">
                         <div>
                            <label class="form-label fw-medium text-secondary small">Discount type</label>
                             <select name="discount_type" class="form-select">
                                <option value="percentage">Percentage</option>
                                <option value="fixed" @selected(old('discount_type') === 'fixed')>Fixed discount amount</option>
                            </select>
                         </div>
                         <div>
                            <label class="form-label fw-medium text-secondary small">Discount value</label>
                            <input type="number" name="discount_value" value="{{ old('discount_value') }}" class="form-control" placeholder="10" step="0.01" min="0">
                         </div>
                     </div>
                </div>

                 <!-- Purchase Quantity Limits -->
                 <div class="card border shadow-sm p-3 mb-4">
                     <h2 class="h6 fw-bold text-secondary mb-3">Purchase Limits (Wholesale/Promo)</h2>
                     <div class="row g-2">
                         <div class="col-6">
                             <label class="form-label fw-medium text-secondary small mb-1">Min Order Qty</label>
                             <input type="number" name="min_order_qty" class="form-control" placeholder="e.g. 1" min="1">
                         </div>
                         <div class="col-6">
                             <label class="form-label fw-medium text-secondary small mb-1">Max Order Qty</label>
                             <input type="number" name="max_order_qty" class="form-control" placeholder="e.g. 10" min="1">
                         </div>
                     </div>
                     <div class="form-text text-muted small mt-2">Leave blank to disable order constraints on this bundle.</div>
                 </div>

                <!-- Summary -->
                 <div class="card border shadow-sm p-3 mb-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Summary</h2>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="fas fa-circle text-secondary me-2" style="font-size: 4px; vertical-align: middle;"></i><span id="summary_count">0</span> products</li>
                        <li class="fw-bold text-dark pt-2">Final Price: <span id="summary_total">Calculated at checkout</span></li>
                    </ul>
                </div>
            </div>
         </div>
        
        <div class="d-flex justify-content-end gap-3 pt-4 border-top mt-4">
             <a href="{{ route('admin.bundles') }}" class="btn btn-white border shadow-sm text-secondary">Discard</a>
            <button type="submit" class="btn btn-primary shadow-sm">Save bundle</button>
        </div>

    </form>

</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
</style>
@endsection
