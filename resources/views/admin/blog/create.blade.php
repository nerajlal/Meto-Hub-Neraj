@extends('layouts.admin')

@section('title', 'Create New Post')

@section('content')
<div class="container pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.blog') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Create New Post</h1>
    </div>

    <form action="#" method="POST" class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            <div class="vstack gap-4">
            
                <div class="card border shadow-sm p-4">
                    <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Title</label>
                            <input type="text" class="form-control" placeholder="e.g. 5 Tips for a Long-Lasting Scent">
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Content</label>
                            <textarea rows="15" class="form-control font-monospace" placeholder="Write your post content here..."></textarea>
                            <p class="small text-muted mt-1 mb-0">Markdown is supported.</p>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm p-4">
                    <div class="vstack gap-3">
                        <h2 class="h6 fw-bold text-secondary mb-2">SEO Preview</h2>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Meta Title</label>
                            <input type="text" class="form-control">
                        </div>
                        <div>
                             <label class="form-label fw-medium text-secondary small mb-1">Meta Description</label>
                            <textarea rows="3" class="form-control"></textarea>
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
                                <option value="published">Published</option>
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
                    <div class="border-2 border-dashed border-secondary border-opacity-25 rounded p-4 text-center hover-bg-light transition-colors cursor-pointer">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-image text-secondary opacity-50 display-6 mb-2"></i>
                            <span class="text-secondary fw-medium small mb-1">Add image</span>
                        </div>
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
                                <option value="tips">Tips & Tricks</option>
                                <option value="news">News</option>
                                <option value="education">Fragrance Education</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Tags</label>
                            <input type="text" class="form-control" placeholder="e.g. oud, floral, winter">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        <div class="col-12 d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
            <button type="button" class="btn btn-white border shadow-sm text-secondary fw-medium hover-bg-light">Discard</button>
            <button type="submit" class="btn btn-primary shadow-sm fw-medium">Save Post</button>
        </div>
    </form>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .border-dashed { border-style: dashed !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .cursor-pointer { cursor: pointer; }
    .font-monospace { font-family: var(--bs-font-monospace) !important; }
</style>
@endsection
