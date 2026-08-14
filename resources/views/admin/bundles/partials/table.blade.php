@forelse($bundles as $bundle)
<tr class="cursor-pointer" onclick="window.location='{{ route('admin.bundles.edit', $bundle->id) }}'">
    <td class="px-3 py-3" onclick="event.stopPropagation()"><div class="form-check"><input type="checkbox" class="form-check-input" value="{{ $bundle->id }}"></div></td>
    <td class="px-3 py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center bg-light border rounded overflow-hidden" style="width: 40px; height: 40px;">
                @if($bundle->image)
                    <img src="{{ Storage::url($bundle->image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $bundle->title }}">
                @else
                    <i class="fas fa-cubes text-secondary opacity-50"></i>
                @endif
            </div>
            <span class="fw-medium text-dark text-decoration-hover-underline">{{ $bundle->title }}</span>
        </div>
    </td>
    <td class="px-3 py-3">
        <span class="badge {{ $bundle->status === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-2 py-1 fw-medium">
            {{ ucfirst($bundle->status) }}
        </span>
    </td>
    @if($bundle->type != 'pool')
        <td class="px-3 py-3 fw-medium text-dark">{{ $currentTenant->currency ?? '₹' }} {{ number_format($bundle->total_price, 2) }}</td>
    @endif
    <td class="px-3 py-3">{{ $bundle->products->count() }} products</td>
    <td class="px-3 py-3">{{ $bundle->total_sales }}</td>
    <td class="px-3 py-3 text-end">
        <div class="d-flex justify-content-end gap-2" onclick="event.stopPropagation()">
             <a href="{{ route('admin.bundles.edit', $bundle->id) }}" class="btn btn-white btn-sm border-0 text-secondary hover-text-primary p-1"><i class="fas fa-edit"></i></a>
             
             <form action="{{ route('admin.bundles.destroy', $bundle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bundle?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-white btn-sm border-0 text-secondary hover-text-danger p-1"><i class="fas fa-trash"></i></button>
             </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5 text-muted">No bundles found.</td>
</tr>
@endforelse
<tr>
    <td colspan="7" class="px-3 py-3 border-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-secondary small">
                Showing
                <span class="fw-medium">{{ $bundles->firstItem() ?? 0 }}</span>
                to
                <span class="fw-medium">{{ $bundles->lastItem() ?? 0 }}</span>
                of
                <span class="fw-medium">{{ $bundles->total() }}</span>
                results
            </div>
            <div>
                {{ $bundles->appends(request()->query())->links() }}
            </div>
        </div>
    </td>
</tr>
