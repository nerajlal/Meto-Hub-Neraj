<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted small text-uppercase">
            <tr>
                <th class="px-4 py-3 fw-medium">Product</th>
                <th class="px-4 py-3 fw-medium">Price</th>
                <th class="px-4 py-3 text-end fw-medium">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allProducts as $product)
            <tr>
                <td class="px-4 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded border p-1 bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 48px; height: 48px;">
                            @if($product->images->isNotEmpty())
                                <img src="{{ Storage::url($product->images->first()->path) }}" alt="{{ $product->title }}" class="w-100 h-100 object-fit-cover rounded">
                            @else
                                <i class="fas fa-image text-secondary opacity-50"></i>
                            @endif
                        </div>
                        <span class="fw-medium text-dark">{{ $product->title }}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    @if($product->variants->isNotEmpty())
                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($product->variants->min('price'), 2) }}
                    @else
                        <span class="text-muted">No variants</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-end">
                    <form action="{{ route('admin.settings.home-products.store') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-white btn-sm border shadow-sm text-success hover-bg-success hover-text-white"><i class="fas fa-plus me-1"></i> Add</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-5 text-muted">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="p-3 d-flex justify-content-end border-top">
    {{ $allProducts->appends(['tab' => 'all', 'search' => request('search')])->links() }}
</div>
<style>
    .hover-bg-success:hover {
        background-color: #008060 !important;
        border-color: #008060 !important;
        color: white !important;
    }
</style>
