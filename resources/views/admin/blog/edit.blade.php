@extends('layouts.admin')

@section('title', 'Edit Post')

@section('content')
<div class="container pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.blog') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Edit Post</h1>
        <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase small tracking-wide" style="letter-spacing: 0.05em;">Published</span>
    </div>

    <form action="#" method="POST" class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            <div class="vstack gap-4">
            
                <div class="card border shadow-sm p-4">
                    <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Title</label>
                            <input type="text" value="The Art of Layering Scents" class="form-control">
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Content</label>
                            <textarea rows="15" class="form-control font-monospace">
Layering scents, or mixing fragrances, is an ancient art that allows you to create a truly unique signature scent. It's not just about spraying two perfumes on top of each other; it's about finding complementary notes that enhance one another.

## Why Layer?
Layering allows you to:
* **Personalize your scent:** Create something that no one else is wearing.
* **Enhance longevity:** Using a lotion base or a heavier wood oil can make lighter citrus notes last longer.
* **Transition day to night:** Add a spicy or musk note to your fresh day scent for the evening.

## How to Start
1. **Start Simple:** Combine a single-note scent (like vanilla or rose) with a complex perfume.
2. **Heavy First:** Apply the heavier, stronger scent first (Oud, Musk, Amber) and let it settle.
3. **Light Top:** Spray the lighter scent (Citrus, Floral) on top.

**Pro Tip:** Don't rub your wrists together! It breaks down the fragrance molecules.
                            </textarea>
                            <p class="small text-muted mt-1 mb-0">Markdown is supported.</p>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm p-4">
                    <div class="vstack gap-3">
                        <h2 class="h6 fw-bold text-secondary mb-2">SEO Preview</h2>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Meta Title</label>
                            <input type="text" value="The Art of Layering Scents | Nurah Perfumes" class="form-control">
                        </div>
                        <div>
                             <label class="form-label fw-medium text-secondary small mb-1">Meta Description</label>
                            <textarea rows="3" class="form-control">Learn the secrets of fragrance layering to create your own unique signature scent. Discover tips on combining woody, floral, and citrus notes.</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column -->
        <div class="col-12 col-lg-4">
            <div class="vstack gap-4">
                
                <!-- Status -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Publishing</h2>
                    <div class="vstack gap-3">
                         <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Status</label>
                            <select class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published" selected>Published</option>
                            </select>
                        </div>
                         <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Author</label>
                            <select class="form-select">
                                <option value="1">John Doe</option>
                                <option value="2" selected>Neraj Lal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Featured Image</h2>
                     <div class="ratio ratio-16x9 bg-light rounded border position-relative group overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=600&q=80" class="w-100 h-100 object-fit-cover">
                         <button type="button" class="btn btn-light bg-white border shadow-sm rounded-circle p-1 position-absolute top-0 end-0 mt-2 me-2 opacity-0 hover-opacity-100 transition-opacity text-danger show-on-hover" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-trash" style="font-size: 10px;"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-link btn-sm text-decoration-none small">Replace image</button>
                    </div>
                </div>

                 <!-- Tags -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Organization</h2>
                     <div class="vstack gap-3">
                         <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Category</label>
                             <select class="form-select">
                                <option value="">Select Category</option>
                                <option value="tips" selected>Tips & Tricks</option>
                                <option value="news">News</option>
                                <option value="education">Fragrance Education</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Tags</label>
                            <input type="text" value="layering, education, tips" class="form-control">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="col-12 d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
             <button type="button" class="btn btn-danger bg-danger bg-opacity-10 text-danger border-0 hover-bg-danger-soft me-auto">Delete post</button>
            <button type="button" class="btn btn-white border shadow-sm text-secondary fw-medium hover-bg-light">Discard</button>
            <button type="submit" class="btn btn-primary shadow-sm fw-medium">Update Post</button>
        </div>
    </form>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .font-monospace { font-family: var(--bs-font-monospace) !important; }
    .object-fit-cover { object-fit: cover; }
    .group:hover .show-on-hover { opacity: 1 !important; }
    .hover-bg-danger-soft:hover { background-color: rgba(220, 53, 69, 0.2) !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
</style>
@endsection
