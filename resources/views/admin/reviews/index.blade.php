@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Product Reviews</h1>
            <p class="text-muted small mb-0">Manage, approve, and reply to customer reviews.</p>
        </div>
        <!-- Tabs -->
        <div class="bg-white p-1 border rounded shadow-sm d-inline-flex">
            <button class="btn btn-sm btn-light fw-medium text-dark shadow-sm">All</button>
            <button class="btn btn-sm btn-white border-0 text-secondary hover-bg-light">
                Pending <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill ms-1">2</span>
            </button>
            <button class="btn btn-sm btn-white border-0 text-secondary hover-bg-light">Published</button>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card border shadow-sm">
        <div class="list-group list-group-flush">
            
            @if(isset($currentTenant) && $currentTenant->id != 1)
            <div class="text-center py-5 text-muted small">
                <i class="fas fa-star-half-alt text-secondary opacity-50 mb-3 fs-3"></i><br>
                No product reviews submitted yet.
            </div>
            @else
            <!-- Review 1 (Pending) -->
            <div class="list-group-item p-4 hover-bg-light transition-colors">
                <div class="d-flex gap-3 align-items-start">
                    <!-- Product Image -->
                    <div class="bg-light border rounded flex-shrink-0 overflow-hidden" style="width: 48px; height: 48px;">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSltgN6yAexGTP-YJYfecKvEyy3hX_5Ge0riQ&s" class="w-100 h-100 object-fit-cover">
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 fw-bold text-primary mb-1">White Oud</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-warning small" style="line-height: 1;">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="small text-muted">by <span class="fw-medium text-dark">Ravi Kumar</span></span>
                                    <span class="small text-muted border-start ps-2">2 hours ago</span>
                                </div>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-normal">
                                Pending
                            </span>
                        </div>
                        
                        <p class="mt-3 mb-0 small text-secondary">
                            "Absolutely love the fragrance. Lasts all day and feels very premium. Highly recommended for daily wear!"
                        </p>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top border-dashed">
                            <button class="btn btn-link btn-sm p-0 text-success text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-link btn-sm p-0 text-secondary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-eye-slash"></i> Hide
                            </button>
                            <button class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review 2 (Published) -->
             <div class="list-group-item p-4 hover-bg-light transition-colors">
                <div class="d-flex gap-3 align-items-start">
                    <!-- Product Image -->
                   <div class="bg-light border rounded flex-shrink-0 overflow-hidden" style="width: 48px; height: 48px;">
                         <img src="https://photodpshare.com/wp-content/uploads/2025/10/whatsapp-dp-photo-hd.jpg" class="w-100 h-100 object-fit-cover">
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 fw-bold text-primary mb-1">Sea Breeze</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-warning small" style="line-height: 1;">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="small text-muted">by <span class="fw-medium text-dark">Sarah J.</span></span>
                                    <span class="small text-muted border-start ps-2">Yesterday</span>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill fw-normal">
                                Published
                            </span>
                        </div>
                        
                        <p class="mt-3 mb-0 small text-secondary">
                            "Refreshing scent, perfect for summer. Taking one star off because the delivery was slightly delayed."
                        </p>

                          <!-- Actions -->
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top border-dashed">
                            <button class="btn btn-link btn-sm p-0 text-secondary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-eye-slash"></i> Hide
                            </button>
                            <button onclick="toggleReply('reply-2')" class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        </div>

                         <!-- Reply Box -->
                        <div id="reply-2" class="d-none mt-3 bg-light p-3 rounded border">
                            <textarea rows="3" class="form-control form-control-sm mb-2" placeholder="Write a reply to the customer..."></textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button onclick="toggleReply('reply-2')" class="btn btn-white btn-sm border text-secondary">Cancel</button>
                                <button class="btn btn-primary btn-sm">Send Reply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Review 3 (Pending) -->
            <div class="list-group-item p-4 hover-bg-light transition-colors">
                <div class="d-flex gap-3 align-items-start">
                    <!-- Product Image -->
                    <div class="bg-light border rounded flex-shrink-0 overflow-hidden" style="width: 48px; height: 48px;">
                       <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcREwA43h1BQGkzeXDIUKNwg-IZep7fBxR9PHQ&s" class="w-100 h-100 object-fit-cover">
                    </div>
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 fw-bold text-primary mb-1">Oud Al Arab</h3>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="text-warning small" style="line-height: 1;">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i> <!-- Empty Start -->
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="small text-muted">by <span class="fw-medium text-dark">Anonymous</span></span>
                                    <span class="small text-muted border-start ps-2">3 hours ago</span>
                                </div>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill fw-normal">
                                Pending
                            </span>
                        </div>
                        
                        <p class="mt-3 mb-0 small text-secondary">
                            "Too strong for my taste. Not what I expected based on the description."
                        </p>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top border-dashed">
                            <button class="btn btn-link btn-sm p-0 text-success text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-check"></i> Approve
                            </button>
                             <button class="btn btn-link btn-sm p-0 text-secondary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-eye-slash"></i> Hide
                            </button>
                             <button onclick="toggleReply('reply-3')" class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold text-uppercase small d-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                        </div>
                        
                        <!-- Reply Box -->
                        <div id="reply-3" class="d-none mt-3 bg-light p-3 rounded border">
                            <textarea rows="3" class="form-control form-control-sm mb-2" placeholder="Write a reply to the customer..."></textarea>
                            <div class="d-flex justify-content-end gap-2">
                                <button onclick="toggleReply('reply-3')" class="btn btn-white btn-sm border text-secondary">Cancel</button>
                                <button class="btn btn-primary btn-sm">Send Reply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .hover-text-dark:hover { color: #212529 !important; }
    .object-fit-cover { object-fit: cover; }
</style>

<script>
    function toggleReply(id) {
        const el = document.getElementById(id);
        if (el.classList.contains('d-none')) {
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
        }
    }
</script>
@endsection
