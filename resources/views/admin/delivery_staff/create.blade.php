@extends('layouts.admin')

@section('title', 'Add Delivery Staff')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.delivery-staff.index') }}" class="text-muted text-decoration-none me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-1 text-primary">Add Delivery Staff</h1>
            <p class="text-muted small mb-0">Create a new delivery boy account.</p>
        </div>
    </div>

    <div class="card border shadow-sm max-w-2xl">
        <div class="card-body p-4">
            <form action="{{ route('admin.delivery-staff.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium text-dark">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <div class="form-text">Minimum 8 characters. Make sure to share this with the delivery boy.</div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.delivery-staff.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
