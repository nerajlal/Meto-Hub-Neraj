@extends('layouts.admin')

@section('title', 'Delivery Staff')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Delivery Staff</h1>
            <p class="text-muted small mb-0">Manage your local delivery boys and their access.</p>
        </div>
        <a href="{{ route('admin.delivery-staff.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-user-plus"></i> Add Delivery Boy
        </a>
    </div>

    <!-- Staff List -->
    <div class="card border shadow-sm container-fluid p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase fw-medium">
                    <tr>
                        <th class="px-4 py-3 border-bottom">Name</th>
                        <th class="px-4 py-3 border-bottom">Email</th>
                        <th class="px-4 py-3 border-bottom">Phone</th>
                        <th class="px-4 py-3 border-bottom">Joined</th>
                        <th class="px-4 py-3 border-bottom text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($staff as $member)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle fw-bold small" style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                                <span class="fw-medium text-dark">{{ $member->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $member->email }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $member->phone ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $member->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.delivery-staff.edit', $member->id) }}" class="btn btn-sm btn-light text-primary fw-semibold text-uppercase small px-3 py-1 rounded transition-colors hover-bg-primary-soft">Edit</a>
                                <form action="{{ route('admin.delivery-staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery staff?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger fw-semibold text-uppercase small px-3 py-1 rounded transition-colors hover-bg-danger-soft">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-5 text-center text-muted">
                            <div class="mb-3"><i class="fa-solid fa-truck-ramp-box fs-1 text-light-gray"></i></div>
                            <p class="mb-0">No delivery staff found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
<style>
    .hover-bg-danger-soft:hover { background-color: #fef2f2 !important; color: #dc3545 !important; }
    .hover-bg-primary-soft:hover { background-color: #ede9fe !important; color: #0d6efd !important; }
    .text-light-gray { color: #cbd5e1; }
</style>
@endsection
