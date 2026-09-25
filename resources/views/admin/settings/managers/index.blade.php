@extends('layouts.admin')

@section('title', 'Store Managers')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Site Managers</h1>
            <p class="text-muted small mb-0">Manage site managers and their permissions.</p>
        </div>
        <a href="{{ route('admin.settings.managers.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-user-plus"></i> Add Manager
        </a>
    </div>

    <!-- Managers List -->
    <div class="card border shadow-sm container-fluid p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase fw-medium">
                    <tr>
                        <th class="px-4 py-3 border-bottom">Name</th>
                        <th class="px-4 py-3 border-bottom">Email</th>
                        <th class="px-4 py-3 border-bottom">Status</th>
                        <th class="px-4 py-3 border-bottom text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <!-- Mock Manager 1 -->
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle fw-bold small" style="width: 32px; height: 32px;">
                                    JD
                                </div>
                                <span class="fw-medium text-dark">John Doe</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">john.doe@example.com</td>
                        <td class="px-4 py-3">
                            <!-- Toggle Switch -->
                            <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="activeSwitch1" checked>
                                <label class="form-check-label small fw-medium text-dark" for="activeSwitch1">Active</label>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <button class="btn btn-sm btn-light text-danger fw-semibold text-uppercase small px-3 py-1 rounded transition-colors hover-bg-danger-soft">Remove</button>
                        </td>
                    </tr>

                    <!-- Mock Manager 2 -->
                    <tr>
                        <td class="px-4 py-3">
                             <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-circle fw-bold small" style="width: 32px; height: 32px;">
                                    AS
                                </div>
                                <span class="fw-medium text-dark">Alice Smith</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">alice.smith@example.com</td>
                        <td class="px-4 py-3">
                             <!-- Toggle Switch -->
                            <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="activeSwitch2" checked>
                                <label class="form-check-label small fw-medium text-dark" for="activeSwitch2">Active</label>
                            </div>
                        </td>
                         <td class="px-4 py-3 text-end">
                            <button class="btn btn-sm btn-light text-danger fw-semibold text-uppercase small px-3 py-1 rounded transition-colors hover-bg-danger-soft">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
<style>
    .hover-bg-danger-soft:hover { background-color: #fef2f2 !important; color: #dc3545 !important; }
</style>
@endsection
