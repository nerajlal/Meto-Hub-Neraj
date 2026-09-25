@extends('layouts.admin')

@section('title', 'Add Site Manager')

@section('content')
<div class="container pb-5" style="max-width: 700px;">
    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.settings.managers') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Add Site Manager</h1>
    </div>

    <form class="card border shadow-sm p-4">
        <div class="vstack gap-4">
            
            <!-- Name -->
            <div>
                <label class="form-label fw-medium text-secondary small">Full Name</label>
                <input type="text" class="form-control" placeholder="e.g. John Doe">
            </div>

            <!-- Email -->
            <div>
                <label class="form-label fw-medium text-secondary small">Email Address</label>
                <input type="email" class="form-control" placeholder="e.g. john@example.com">
            </div>

            <!-- Password -->
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium text-secondary small">Password</label>
                    <input type="password" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium text-secondary small">Confirm Password</label>
                    <input type="password" class="form-control">
                </div>
            </div>

            <!-- Permissions (Mock) -->
            <div>
                <span class="d-block form-label fw-medium text-secondary small mb-3">Permissions</span>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked id="perm1">
                            <label class="form-check-label text-dark small" for="perm1">Manage Orders</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked id="perm2">
                            <label class="form-check-label text-dark small" for="perm2">Manage Products</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm3">
                            <label class="form-check-label text-dark small" for="perm3">Manage Collections</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm4">
                            <label class="form-check-label text-dark small" for="perm4">Manage Bundles</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm5">
                            <label class="form-check-label text-dark small" for="perm5">Manage Discounts</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm6">
                            <label class="form-check-label text-dark small" for="perm6">Manage Hero Section</label>
                         </div>
                    </div>
                     <div class="col-12 col-sm-6">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="perm7">
                            <label class="form-check-label text-dark small" for="perm7">Manage Reviews</label>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-top mt-4 d-flex justify-content-end gap-3">
             <a href="{{ route('admin.settings.managers') }}" class="btn btn-white border text-secondary shadow-sm">Cancel</a>
            <button type="button" class="btn btn-primary shadow-sm">Create Manager</button>
        </div>

    </form>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
</style>
@endsection
