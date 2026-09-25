@extends('layouts.admin')

@section('title', 'Import Customers')

@section('content')
@section('content')
<div class="container pb-5 mt-5" style="max-width: 600px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.customers') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Import customers</h1>
    </div>

    <div class="card border shadow-sm p-4 text-center">
        <div class="mb-4">
            <i class="fas fa-file-csv text-success display-4 mb-3"></i>
            <h2 class="h5 fw-bold text-primary">Upload CSV file</h2>
            <p class="small text-muted mt-1">Download a <a href="#" class="text-decoration-underline text-primary">sample CSV template</a> to see an example of the format required.</p>
        </div>

        <div class="border-2 border-dashed border-secondary border-opacity-25 rounded p-5 hover-bg-light transition-colors cursor-pointer mb-4" onclick="document.getElementById('file_upload').click()">
            <div class="d-flex flex-column align-items-center">
                <span class="bg-light p-3 rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-cloud-upload-alt text-secondary opacity-50 h4 mb-0"></i>
                </span>
                <span class="fw-medium text-dark mb-1">Click to upload</span>
                <span class="small text-muted">or drag and drop</span>
            </div>
            <input type="file" id="file_upload" class="d-none" accept=".csv">
        </div>

        <div class="d-flex align-items-center gap-2 mb-4 text-start p-3 bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded small">
            <i class="fas fa-exclamation-triangle"></i>
            <p class="mb-0">Existing customers with the same email or phone number will be updated.</p>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium small">Cancel</button>
            <button type="button" class="btn btn-primary shadow-sm">Import customers</button>
        </div>
    </div>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
    .border-dashed { border-style: dashed !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
@endsection
