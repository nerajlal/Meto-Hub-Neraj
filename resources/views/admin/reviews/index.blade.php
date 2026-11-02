@extends('layouts.admin')

@section('title', 'Product Reviews')

@section('content')
<div class="container-fluid">

    <!-- Header & Action Button -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Reviews</h1>
            <p class="text-muted small mb-0">Manage, edit, and add customer & store reviews.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Filter Tabs -->
            <div class="bg-white p-1 border rounded shadow-sm d-inline-flex me-2">
                <a href="{{ route('admin.reviews') }}" class="btn btn-sm {{ empty($status) ? 'btn-light fw-medium text-dark shadow-sm' : 'btn-white border-0 text-secondary hover-bg-light' }}">
                    All <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill ms-1">{{ $counts['all'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.reviews', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-light fw-medium text-dark shadow-sm' : 'btn-white border-0 text-secondary hover-bg-light' }}">
                    Pending <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill ms-1">{{ $counts['pending'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.reviews', ['status' => 'published']) }}" class="btn btn-sm {{ $status === 'published' ? 'btn-light fw-medium text-dark shadow-sm' : 'btn-white border-0 text-secondary hover-bg-light' }}">
                    Published <span class="badge bg-success bg-opacity-25 text-success rounded-pill ms-1">{{ $counts['published'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.reviews', ['status' => 'hidden']) }}" class="btn btn-sm {{ $status === 'hidden' ? 'btn-light fw-medium text-dark shadow-sm' : 'btn-white border-0 text-secondary hover-bg-light' }}">
                    Hidden <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill ms-1">{{ $counts['hidden'] ?? 0 }}</span>
                </a>
            </div>
            
            <!-- Add Review Button -->
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                <i class="fas fa-plus small"></i>
                <span>Add Review</span>
            </button>
        </div>
    </div>

    <!-- Reviews List Card -->
    <div class="card border shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($reviews as $item)
            <div class="list-group-item p-4 hover-bg-light transition-colors">
                <div class="d-flex gap-3 align-items-start">
                    <!-- Product Avatar / Image -->
                    <div class="bg-light border rounded flex-shrink-0 overflow-hidden d-flex align-items-center justify-content-center text-secondary" style="width: 48px; height: 48px;">
                        @if($item->product && $item->product->images->isNotEmpty())
                            <img src="{{ Storage::url($item->product->images->first()->image_path) }}" alt="{{ $item->product->title }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="fas fa-user-circle fs-4 opacity-50"></i>
                        @endif
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 fw-bold text-primary mb-1">
                                    {{ $item->name }}
                                    @if($item->product)
                                        <span class="text-muted fw-normal small ms-2">on <strong>{{ $item->product->title }}</strong></span>
                                    @endif
                                </h3>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-warning small" style="line-height: 1;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $item->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="small text-muted border-start ps-2">{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div>
                                @if($item->status === 'published')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill fw-normal">Published</span>
                                @elseif($item->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-normal">Pending</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill fw-normal">Hidden</span>
                                @endif
                            </div>
                        </div>
                        
                        <p class="mt-3 mb-0 small text-secondary">
                            "{{ $item->review }}"
                        </p>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top border-dashed">
                            @if($item->status !== 'published')
                            <form action="{{ route('admin.reviews.status', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="published">
                                <button type="submit" class="btn btn-link btn-sm p-0 text-success text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                    <i class="fas fa-check"></i> Approve / Publish
                                </button>
                            </form>
                            @endif

                            @if($item->status !== 'hidden')
                            <form action="{{ route('admin.reviews.status', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="hidden">
                                <button type="submit" class="btn btn-link btn-sm p-0 text-secondary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                    <i class="fas fa-eye-slash"></i> Hide
                                </button>
                            </form>
                            @endif

                            <button type="button" class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editReviewModal"
                                data-action="{{ route('admin.reviews.update', $item->id) }}"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-product-id="{{ $item->product_id ?? '' }}"
                                data-rating="{{ $item->rating }}"
                                data-review="{{ $item->review }}"
                                data-status="{{ $item->status }}"
                                onclick="populateEditModal(this)">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <form action="{{ route('admin.reviews.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark ms-auto">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted small">
                <i class="fas fa-star-half-alt text-secondary opacity-50 mb-3 fs-3"></i><br>
                No reviews found. Click <strong>Add Review</strong> above to create your first review.
            </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Add Review -->
<div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.reviews.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary" id="addReviewModalLabel"><i class="fas fa-star text-warning me-2"></i>Add Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Customer / Reviewer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Rahul Sharma" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Product (Optional)</label>
                        <select name="product_id" class="form-select form-select-sm">
                            <option value="">General Store Review</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Rating</label>
                        <select name="rating" class="form-select form-select-sm">
                            <option value="5" selected>★★★★★ (5 Stars - Excellent)</option>
                            <option value="4">★★★★☆ (4 Stars - Good)</option>
                            <option value="3">★★★☆☆ (3 Stars - Average)</option>
                            <option value="2">★★☆☆☆ (2 Stars - Below Average)</option>
                            <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Review Message <span class="text-danger">*</span></label>
                        <textarea name="review" rows="4" class="form-control form-control-sm" placeholder="Write customer feedback or review text..." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium small text-dark">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="published" selected>Published</option>
                            <option value="pending">Pending Approval</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-white btn-sm border text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Review -->
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editReviewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-primary" id="editReviewModalLabel"><i class="fas fa-edit text-primary me-2"></i>Edit Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Customer / Reviewer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Product (Optional)</label>
                        <select name="product_id" id="edit_product_id" class="form-select form-select-sm">
                            <option value="">General Store Review</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Rating</label>
                        <select name="rating" id="edit_rating" class="form-select form-select-sm">
                            <option value="5">★★★★★ (5 Stars - Excellent)</option>
                            <option value="4">★★★★☆ (4 Stars - Good)</option>
                            <option value="3">★★★☆☆ (3 Stars - Average)</option>
                            <option value="2">★★☆☆☆ (2 Stars - Below Average)</option>
                            <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-dark">Review Message <span class="text-danger">*</span></label>
                        <textarea name="review" id="edit_review" rows="4" class="form-control form-control-sm" required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium small text-dark">Status</label>
                        <select name="status" id="edit_status" class="form-select form-select-sm">
                            <option value="published">Published</option>
                            <option value="pending">Pending Approval</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-white btn-sm border text-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .hover-text-dark:hover { color: #212529 !important; }
    .object-fit-cover { object-fit: cover; }
</style>

<script>
    function populateEditModal(btn) {
        const action = btn.getAttribute('data-action');
        const name = btn.getAttribute('data-name');
        const productId = btn.getAttribute('data-product-id');
        const rating = btn.getAttribute('data-rating');
        const review = btn.getAttribute('data-review');
        const status = btn.getAttribute('data-status');

        const form = document.getElementById('editReviewForm');
        form.action = action;

        document.getElementById('edit_name').value = name || '';
        document.getElementById('edit_product_id').value = productId || '';
        document.getElementById('edit_rating').value = rating || '5';
        document.getElementById('edit_review').value = review || '';
        document.getElementById('edit_status').value = status || 'published';
    }
</script>
@endsection
