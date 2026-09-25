@extends('layouts.admin')

@section('title', 'Add New Reel')

@section('content')
<div class="container-fluid" style="max-width: 720px;">

    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.settings.reels') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-primary">Add Instagram Reel</h1>
            <p class="text-muted small mb-0">Paste an Instagram reel link and connect it to a product or collection.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.reels.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Instagram URL -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3"><i class="fab fa-instagram text-danger me-2"></i>Reel Link</h5>
                
                <div class="mb-3">
                    <label for="instagram_url" class="form-label fw-semibold">Instagram Reel URL <span class="text-danger">*</span></label>
                    <input type="url" name="instagram_url" id="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror" 
                        placeholder="https://www.instagram.com/reel/ABC123..." value="{{ old('instagram_url') }}" required>
                    @error('instagram_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Paste the full Instagram reel URL. Example: https://www.instagram.com/reel/C1234abcdef/</div>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Title <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Summer Collection Showcase" value="{{ old('title') }}">
                </div>


            </div>
        </div>

        <!-- Link To -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3"><i class="fas fa-link text-primary me-2"></i>Link To</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Link Type <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="link_type" id="link_product" value="product" {{ old('link_type', 'product') === 'product' ? 'checked' : '' }} onchange="toggleLinkFields()">
                            <label class="form-check-label fw-semibold" for="link_product">
                                <i class="fas fa-box me-1 text-primary"></i> Product
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="link_type" id="link_collection" value="collection" {{ old('link_type') === 'collection' ? 'checked' : '' }} onchange="toggleLinkFields()">
                            <label class="form-check-label fw-semibold" for="link_collection">
                                <i class="fas fa-layer-group me-1 text-info"></i> Collection
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Product Select -->
                <div id="product-field" class="mb-0">
                    <label for="product_id" class="form-label fw-semibold">Select Product</label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                        <option value="">Choose a product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Collection Select -->
                <div id="collection-field" class="mb-0" style="display: none;">
                    <label for="collection_id" class="form-label fw-semibold">Select Collection</label>
                    <select name="collection_id" id="collection_id" class="form-select @error('collection_id') is-invalid @enderror">
                        <option value="">Choose a collection...</option>
                        @foreach($collections as $collection)
                            <option value="{{ $collection->id }}" {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                {{ $collection->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('collection_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="status">Active</label>
                </div>
                <div class="form-text">When active, this reel will be visible on the storefront.</div>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                <i class="fas fa-save me-2"></i>Save Reel
            </button>
            <a href="{{ route('admin.settings.reels') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleLinkFields() {
    const isProduct = document.getElementById('link_product').checked;
    document.getElementById('product-field').style.display = isProduct ? 'block' : 'none';
    document.getElementById('collection-field').style.display = isProduct ? 'none' : 'block';
}
document.addEventListener('DOMContentLoaded', toggleLinkFields);
</script>
@endsection
