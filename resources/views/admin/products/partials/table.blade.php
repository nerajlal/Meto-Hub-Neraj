@forelse($products as $product)
<tr class="cursor-pointer" onclick="window.location='{{ route('admin.products.edit', $product->id) }}'">
    <td class="px-3 py-3" onclick="event.stopPropagation()"><input type="checkbox" class="form-check-input" value="{{ $product->id }}"></td>
    <td class="px-3 py-3 fw-medium text-secondary">#{{ $product->id }}</td>
    <td class="px-3 py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-light rounded border d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" style="width: 48px; height: 48px;">
                @if($product->images->first())
                    <img src="{{ Storage::url($product->images->first()->path) }}" class="w-100 h-100 object-fit-cover">
                @else
                    <i class="fas fa-image text-secondary opacity-50"></i>
                @endif
            </div>
            <div class="d-flex flex-column gap-1">
                <span class="fw-medium text-dark hover-success mb-0">{{ $product->title }}</span>
                @if($product->bundles->where('type', 'pack')->isNotEmpty())
                    <div class="mt-1">
                        <span class="badge rounded-pill fw-medium" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; background-color: rgba(99, 102, 241, 0.1); color: rgb(79, 70, 229); border: 1px solid rgba(99, 102, 241, 0.2);">
                            <i class="fas fa-boxes me-1"></i> Pack: 
                            {{ $product->bundles->where('type', 'pack')->map(function($b) {
                                return $b->pivot->quantity ?? (preg_match('/Pack of (\d+)/i', $b->title, $m) ? $m[1] : null);
                            })->filter()->unique()->sort()->map(fn($q) => $q . 'x')->implode(', ') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td class="px-3 py-3">
        <span class="badge {{ $product->status == 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill fw-medium">
            {{ ucfirst($product->status) }}
        </span>
    </td>
    <td class="px-3 py-3 text-secondary">
        {{ $product->variants->sum('stock') }} in stock
        @if($product->variants->count() > 1)
            <small class="text-muted d-block">({{ $product->variants->count() }} variants)</small>
        @endif
    </td>
    <td class="px-3 py-3">{{ $product->type }}</td>
    <td class="px-3 py-3">{{ $product->vendor }}</td>
    <td class="px-3 py-3 text-end">
        <div class="d-flex justify-content-end gap-2" onclick="event.stopPropagation()">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-link text-secondary hover-success p-0"><i class="fas fa-edit"></i></a>
            
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-secondary hover-danger p-0 ms-2"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5">
        <div class="text-muted mb-2"><i class="fas fa-box-open fa-2x opacity-25"></i></div>
        <p class="mb-0">No products found.</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-link">Add your first product</a>
    </td>
</tr>
@endforelse
<tr>
    <td colspan="7" class="px-3 py-3 border-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-secondary small">
                Showing 
                <span class="fw-medium">{{ $products->firstItem() ?? 0 }}</span> 
                to 
                <span class="fw-medium">{{ $products->lastItem() ?? 0 }}</span> 
                of 
                <span class="fw-medium">{{ $products->total() }}</span> 
                results
            </div>
            <div>
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </td>
</tr>
