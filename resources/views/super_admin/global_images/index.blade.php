@extends('super_admin.layouts.app')

@section('title', 'Global Product Images')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-dark fw-bold">Global Product Images</h4>
            <p class="text-muted small mb-0 mt-1">
                {{ \App\Models\GlobalProductImage::count() }} entries in dictionary
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('super_admin.global_images.sample_csv') }}" class="btn btn-outline-secondary shadow-sm btn-sm">
                <i class="fas fa-download me-1"></i> Download Sample CSV
            </a>
            <button class="btn btn-outline-primary shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#csvImportModal">
                <i class="fas fa-file-csv me-1"></i> Bulk CSV Import
            </button>
            <button class="btn btn-primary shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#addImageModal">
                <i class="fas fa-plus me-1"></i> Add Image
            </button>
            @if($totalCount > 0)
            <form action="{{ route('super_admin.global_images.clear_all') }}" method="POST"
                  onsubmit="return confirm('⚠️ This will delete ALL {{ $totalCount }} entries and their images from storage. Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger shadow-sm btn-sm">
                    <i class="fas fa-trash me-1"></i> Clear All ({{ $totalCount }})
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Banner --}}
    <div class="alert alert-info border-0 shadow-sm small mb-4" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>How it works:</strong> When a store owner imports products via Excel, any product whose name contains or matches a title here will automatically get this image assigned. Matching is case-insensitive.
        You can bulk-import hundreds of items at once using <strong>CSV Import</strong> or fetch live data from <strong>Open Food Facts</strong> — a free global grocery database!
    </div>

    {{-- Open Food Facts Live Importer Card --}}
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-left: 4px solid #22c55e !important;">
        <div class="card-body py-3">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white flex-shrink-0" style="width:42px;height:42px;">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-dark mb-1">
                        Import from Open Food Facts API
                        <span class="badge bg-success ms-2 small">FREE</span>
                    </h6>
                    <p class="text-muted small mb-2">Fetch real product images directly from the Open Food Facts database — a free, open-source global grocery database with millions of products. Select a category and how many products to fetch.</p>
                    <form action="{{ route('super_admin.global_images.fetch_api') }}" method="POST" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-sm-5">
                            <label class="form-label small fw-medium text-dark mb-1">Category</label>
                            <select name="category" class="form-select form-select-sm" required>
                                <option value="">-- Select Category --</option>
                                <option value="fruits">🍎 Fruits</option>
                                <option value="vegetables">🥦 Vegetables</option>
                                <option value="dairy">🥛 Dairy Products</option>
                                <option value="beverages">🥤 Beverages</option>
                                <option value="snacks">🍿 Snacks</option>
                                <option value="cereals">🌾 Cereals & Grains</option>
                                <option value="spices">🌶 Spices & Herbs</option>
                                <option value="sweets">🍬 Confectioneries / Sweets</option>
                                <option value="biscuits">🍪 Biscuits & Cakes</option>
                                <option value="pasta-noodles">🍝 Pasta & Noodles</option>
                                <option value="dry-fruits">🥜 Dry Fruits</option>
                                <option value="bread">🍞 Breads</option>
                                <option value="sauces">🥫 Sauces</option>
                                <option value="oils">🫙 Oils</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small fw-medium text-dark mb-1">How many to fetch?</label>
                            <select name="limit" class="form-select form-select-sm">
                                <option value="20">20 products</option>
                                <option value="50">50 products</option>
                                <option value="100">100 products</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-cloud-download-alt me-1"></i> Fetch & Import Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Table --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">#</th>
                            <th style="width: 70px;">Image</th>
                            <th>Product Title</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($images as $image)
                        <tr>
                            <td class="ps-3 text-muted small">{{ $image->id }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->title }}"
                                     class="rounded"
                                     style="width: 46px; height: 46px; object-fit: cover; border: 1px solid #eee;">
                            </td>
                            <td class="fw-medium text-dark text-capitalize">{{ $image->title }}</td>
                            <td>
                                @if($image->status)
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <form action="{{ route('super_admin.global_images.destroy', $image->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this global image?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-images fa-3x mb-3 d-block text-light"></i>
                                No global images added yet. Use <strong>Add Image</strong> or <strong>Bulk CSV Import</strong> to get started.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($images->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $images->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Add Single Image Modal --}}
<div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super_admin.global_images.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="addImageModalLabel">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>Add Global Product Image
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Products imported by any store owner that contain this title will automatically get this image.</p>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium text-dark">Product Title</label>
                        <input type="text" class="form-control" id="title" name="title" required
                               placeholder="e.g. Tata Salt">
                        <div class="form-text">Saved as lowercase. Matching is case-insensitive.</div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label fw-medium text-dark">Image File</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Upload Image</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- CSV Bulk Import Modal --}}
<div class="modal fade" id="csvImportModal" tabindex="-1" aria-labelledby="csvImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super_admin.global_images.csv_import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark" id="csvImportModalLabel">
                        <i class="fas fa-file-csv me-2 text-success"></i>Bulk CSV Import
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border small mb-3">
                        <strong>CSV Format Required:</strong><br>
                        Your CSV must have exactly 2 columns:
                        <ul class="mb-1 mt-1">
                            <li><code>product_name</code> — The grocery item name (e.g. Tata Salt)</li>
                            <li><code>image_url</code> — A public URL to the image (e.g. https://...)</li>
                        </ul>
                        <a href="{{ route('super_admin.global_images.sample_csv') }}" class="text-primary fw-medium">
                            <i class="fas fa-download me-1"></i>Download Sample CSV Template
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-medium text-dark">Upload CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                        <div class="form-text">Images will be downloaded from the URLs and saved to your server automatically.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-upload me-1"></i> Import Now
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
