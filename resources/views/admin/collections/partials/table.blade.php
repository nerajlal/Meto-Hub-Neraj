@forelse($collections as $collection)
<tr class="cursor-pointer group" onclick="window.location='{{ route('admin.collections.edit', ['id' => $collection->id]) }}'">
    <td class="px-4 py-3" onclick="event.stopPropagation()">
        <input class="form-check-input" type="checkbox" value="{{ $collection->id }}">
    </td>
    <td class="px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center bg-light rounded border flex-shrink-0 overflow-hidden" style="width: 40px; height: 40px;">
                @if($collection->image)
                    <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->name }}" class="w-100 h-100 object-fit-cover">
                @else
                    <i class="fas fa-image text-secondary opacity-50"></i>
                @endif
            </div>
            <a href="{{ route('admin.collections.show', ['id' => $collection->id]) }}" class="fw-medium text-dark text-decoration-underline-hover">{{ $collection->name }}</a>
        </div>
    </td>
    <td class="px-4 py-3 text-secondary">{{ $collection->isOfferCollection() ? $collection->bundles_count.' offers' : $collection->products_count.' products' }}</td>
    <td class="px-4 py-3">
        <span class="badge {{ $collection->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
            {{ $collection->status ? 'Active' : 'Draft' }}
        </span>
    </td>
    <td class="px-4 py-3 text-end">
        <div class="d-flex justify-content-end gap-2" onclick="event.stopPropagation()">
            <a href="{{ route('admin.collections.show', ['id' => $collection->id]) }}" class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-primary"><i class="fas fa-eye"></i></a>
            <a href="{{ route('admin.collections.edit', ['id' => $collection->id]) }}" class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-primary"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.collections.destroy', ['id' => $collection->id]) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-danger"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-5 text-muted">No collections found.</td>
</tr>
@endforelse
<tr>
    <td colspan="5" class="px-4 py-3 border-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-secondary small">
                Showing
                <span class="fw-medium">{{ $collections->firstItem() ?? 0 }}</span>
                to
                <span class="fw-medium">{{ $collections->lastItem() ?? 0 }}</span>
                of
                <span class="fw-medium">{{ $collections->total() }}</span>
                results
            </div>
            <div>
                {{ $collections->appends(request()->query())->links() }}
            </div>
        </div>
    </td>
</tr>
