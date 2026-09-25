@extends('layouts.admin')

@section('title', 'Blog & Articles')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">Blog & Articles</h1>
            <p class="small text-muted mt-1 mb-0">Manage your blog posts and news articles.</p>
        </div>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary shadow-sm fw-medium d-inline-flex align-items-center">
            <i class="fas fa-plus me-2"></i> Create New Post
        </a>
    </div>

    <div class="card border shadow-sm p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead class="bg-light text-secondary small text-uppercase fw-semibold">
                    <tr>
                        <th class="px-4 py-3 border-bottom">Post Details</th>
                        <th class="px-4 py-3 border-bottom">Status</th>
                        <th class="px-4 py-3 border-bottom">Date</th>
                        <th class="px-4 py-3 border-bottom text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <!-- Post 1 -->
                    <tr class="hover-bg-light transition-colors group">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light rounded overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                                    <img src="https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=150&q=80" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <h3 class="h6 fw-semibold text-primary mb-1">The Art of Layering Scents</h3>
                                    <p class="small text-muted mb-0">Expert tips on combining fragrances.</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-medium d-inline-flex align-items-center gap-1">
                                <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> Published
                            </span>
                        </td>
                        <td class="px-4 py-3 text-secondary small">
                            Oct 24, 2024
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.blog.edit', 1) }}" class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
    
                    <!-- Post 2 -->
                     <tr class="hover-bg-light transition-colors group">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light rounded overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                                    <img src="https://i.ytimg.com/vi/_2NfDSPAwVo/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLDVmdYRqpPdYDZYwqQKDmEr3Bw7DQ" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div>
                                    <h3 class="h6 fw-semibold text-primary mb-1">Top 5 Winter Fragrances for 2024</h3>
                                    <p class="small text-muted mb-0">Discover this season's most captivating scents.</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                             <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-medium d-inline-flex align-items-center gap-1">
                                <span class="rounded-circle bg-success" style="width: 6px; height: 6px;"></span> Published
                            </span>
                        </td>
                        <td class="px-4 py-3 text-secondary small">
                            Dec 15, 2024
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.blog.edit', 1) }}" class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
    
                    <!-- Post 3 (Draft) -->
                     <tr class="hover-bg-light transition-colors group bg-light bg-opacity-50">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light rounded overflow-hidden flex-shrink-0 d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                    <i class="fas fa-image text-secondary opacity-25"></i>
                                </div>
                                <div>
                                    <h3 class="h6 fw-semibold text-primary mb-1">Understanding Ouds: An Introduction</h3>
                                    <p class="small text-muted mb-0">A beginner's guide to the world of Oud.</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill fw-medium d-inline-flex align-items-center gap-1">
                                <span class="rounded-circle bg-secondary" style="width: 6px; height: 6px;"></span> Draft
                            </span>
                        </td>
                        <td class="px-4 py-3 text-secondary small">
                            -
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2 opacity-0 group-hover-opacity-100 transition-opacity show-on-hover">
                                <button class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-primary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-white border shadow-sm text-secondary hover-text-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
    
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .object-fit-cover { object-fit: cover; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .hover-text-primary:hover { color: var(--bs-primary) !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
    .group:hover .show-on-hover { opacity: 1 !important; }
</style>
@endsection
