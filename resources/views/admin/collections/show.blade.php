@extends('layouts.admin')

@section('title', $collection->name)

@section('content')
<div class="container pb-5">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
             <a href="{{ route('admin.collections') }}" class="text-secondary hover-text-dark">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 text-primary">{{ $collection->name }}</h1>
        </div>
        <div class="d-flex gap-2">
             <a href="{{ route('admin.collections.edit', ['id' => $collection->id]) }}" class="btn btn-white border shadow-sm text-secondary hover-bg-light fw-medium">Edit Collection</a>
             <a href="{{ route('admin.products.create') }}" class="btn btn-dark shadow-sm fw-medium">Add Product</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="vstack gap-4">
            
                <!-- Products List -->
                <div class="card border shadow-sm overflow-hidden">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center p-3">
                        <h2 class="h6 fw-bold text-secondary mb-0">Products ({{ $collection->products->count() }})</h2>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        @forelse($collection->products as $product)
                        <div class="list-group-item p-3 d-flex gap-3 align-items-center hover-bg-light transition-colors">
                            <div class="d-flex align-items-center justify-content-center bg-light rounded border flex-shrink-0 overflow-hidden" style="width: 40px; height: 40px;">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ Storage::url($product->images->sortBy('order')->first()->path) }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <i class="fas fa-image text-secondary opacity-50"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <a href="{{ route('admin.products.edit', ['id' => $product->id]) }}" class="d-block text-dark fw-medium text-decoration-none text-decoration-underline-hover small">{{ $product->title }}</a>
                                <p class="small text-muted mb-0">
                                    {{ ucfirst($product->status) }} • 
                                    {{ $product->variants->sum('stock') }} in stock
                                    @if($product->variants->isNotEmpty())
                                        • {{ $product->variants->count() }} variants
                                    @endif
                                </p>
                            </div>
                             <a href="{{ route('admin.products.edit', ['id' => $product->id]) }}" class="btn btn-link btn-sm text-secondary hover-text-primary p-0">
                                 <i class="fas fa-edit"></i>
                             </a>
                        </div>
                        @empty
                        <div class="list-group-item p-5 text-center text-muted">
                            <p class="mb-0">No products in this collection yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="vstack gap-4">
                <!-- Image Card -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Collection Image</h2>
                    <div class="ratio ratio-1x1 bg-light rounded border d-flex align-items-center justify-content-center overflow-hidden mb-3">
                        @if($collection->image)
                            <img src="{{ Storage::url($collection->image) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="fas fa-image text-secondary opacity-25 display-4"></i>
                        @endif
                    </div>
                    <a href="{{ route('admin.collections.edit', ['id' => $collection->id]) }}" class="btn btn-link w-100 text-decoration-none small">Change image</a>
                </div>
                
                <!-- Status Card -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Status</h2>
                    <div class="d-flex align-items-center justify-content-between">
                         <span class="badge {{ $collection->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                            {{ $collection->status ? 'Active' : 'Draft' }}
                        </span>
                        <span class="text-muted small">
                             {{ $collection->status ? 'Visible on store' : 'Hidden from store' }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .text-decoration-underline-hover:hover { text-decoration: underline !important; }
    .hover-text-primary:hover { color: var(--bs-primary) !important; }
</style>
@endsection
