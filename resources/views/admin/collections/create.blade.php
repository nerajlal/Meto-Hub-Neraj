@extends('layouts.admin')

@section('title', 'Create Collection')

@section('content')
<div class="container pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.collections') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-dark">Create collection</h1>
    </div>

    <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
        @csrf
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            <div class="vstack gap-4">
            
                <!-- Basic Info -->
                <div class="card border shadow-sm p-4">
                    <div class="vstack gap-3">
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Title</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Summer Collection" required>
                            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label fw-medium text-secondary small mb-1">Description</label>
                            <textarea name="description" rows="6" class="form-control"></textarea>
                            @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
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
                     <h2 class="h6 fw-bold text-secondary mb-3">Status</h2>
                     <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                        <label class="form-check-label" for="status">Active</label>
                     </div>
                </div>

                <!-- Collection Image -->
                <div class="card border shadow-sm p-4">
                    <h2 class="h6 fw-bold text-secondary mb-3">Collection Image</h2>
                    <div class="border-2 border-dashed border-secondary border-opacity-25 rounded p-4 text-center hover-bg-light transition-colors cursor-pointer" onclick="document.getElementById('collection_image').click()">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-image text-secondary opacity-50 display-6 mb-2"></i>
                            <span class="text-secondary fw-medium small mb-1">Add image</span>
                            <p class="small text-muted mb-0">1200 x 1200px recommended</p>
                        </div>
                        <input type="file" name="image" id="collection_image" class="d-none" accept="image/webp, image/png, image/jpeg, image/jpg" onchange="previewImage(this)">
                    </div>
                    <div id="image_preview" class="mt-3 d-none">
                        <img src="" alt="Preview" class="w-100 rounded">
                    </div>
                    @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>                
            </div>
        </div>
        
        <div class="col-12 d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
            <a href="{{ route('admin.collections') }}" class="btn btn-white border shadow-sm text-secondary fw-medium hover-bg-light">Discard</a>
            <button type="submit" class="btn btn-success shadow-sm fw-medium">Save</button>
        </div>
    </form>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById('image_preview');
                    preview.classList.remove('d-none');
                    preview.querySelector('img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .border-dashed { border-style: dashed !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
