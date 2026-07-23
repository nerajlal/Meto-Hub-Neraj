@extends('super_admin.layouts.app')

@section('title', 'Global Product Images')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800">Global Product Images</h4>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Image
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white border-bottom-0">
            <h6 class="m-0 font-weight-bold text-primary">Dictionary</h6>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Product Title (Lowercase)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($images as $image)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->title }}" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td class="fw-medium text-dark">{{ $image->title }}</td>
                            <td>
                                @if($image->status)
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('super_admin.global_images.destroy', $image->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this global image?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-images fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No global images added yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Image Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super_admin.global_images.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title font-weight-bold text-dark" id="addImageModalLabel">Add Global Product Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="text-muted small mb-4">When a tenant imports an Excel file, products matching this exact title (case-insensitive) will automatically receive this image.</p>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium text-dark">Product Title</label>
                        <input type="text" class="form-control bg-light" id="title" name="title" required placeholder="e.g. Tata Salt 1kg">
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label fw-medium text-dark">Image File</label>
                        <input type="file" class="form-control bg-light" id="image" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light shadow-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">Upload Image</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
