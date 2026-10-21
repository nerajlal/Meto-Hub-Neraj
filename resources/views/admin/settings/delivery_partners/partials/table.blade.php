@forelse($partners as $partner)
<tr>
    <td class="px-3 py-3 text-muted">#{{ $partner->id }}</td>
    <td class="px-3 py-3 fw-medium text-dark">{{ $partner->name }}</td>
    <td class="px-3 py-3">
        @if($partner->tracking_url_template)
            <a href="{{ $partner->tracking_url_template }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-success small">
                <i class="fas fa-external-link-alt me-1"></i> {{ Str::limit($partner->tracking_url_template, 40) }}
            </a>
        @else
            <span class="text-muted small">-</span>
        @endif
    </td>
    <td class="px-3 py-3">
        <form action="{{ route('admin.settings.delivery-partners.default', $partner->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Set as Default">
                <i class="fas fa-star {{ $partner->is_default ? 'text-warning' : 'text-secondary opacity-25' }}"></i>
            </button>
        </form>
    </td>
    <td class="px-3 py-3">
        <span class="badge {{ $partner->status ? 'bg-success text-success' : 'bg-secondary text-secondary' }} bg-opacity-10 rounded-pill px-2 py-1 small">
            {{ $partner->status ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-3 py-3 text-end">
        <div class="btn-group">
            <button class="btn btn-sm btn-white border shadow-sm" onclick="editPartner({{ $partner }}, '{{ route('admin.settings.delivery-partners.update', $partner->id) }}')">
                <i class="fas fa-edit text-secondary"></i>
            </button>
            <form action="{{ route('admin.settings.delivery-partners.destroy', $partner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-white border shadow-sm ms-1"><i class="fas fa-trash text-danger"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-5 text-muted">
        <i class="fas fa-box-open fs-3 mb-3 text-secondary opacity-50"></i>
        <p class="mb-0">No delivery partners found.</p>
    </td>
</tr>
@endforelse
