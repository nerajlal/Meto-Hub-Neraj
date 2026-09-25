@extends('layouts.admin')

@section('title', 'Collections')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <h1 class="h3 mb-0 text-primary">Collections</h1>
    <a href="{{ route('admin.collections.create') }}" class="btn btn-primary shadow-sm fw-medium">Create collection</a>
</div>

<div class="card border shadow-sm container-fluid p-0 overflow-hidden">
    <div class="card-header bg-light border-bottom p-3">
        <div class="d-flex flex-column flex-md-row gap-3">
            <div class="flex-grow-1">
                 <form action="{{ route('admin.collections') }}" method="GET">
                     @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                     @endforeach
                     <div class="input-group shadow-sm">
                         <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                         <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 shadow-none" placeholder="Filter collections">
                     </div>
                 </form>
            </div>
            
            <!-- Status Filter -->
            <div class="d-flex gap-2">
                <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i> {{ request('status') ? ucfirst(request('status')) : 'All Status' }}
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li><a class="dropdown-item small {{ !request('status') ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['status' => null, 'page' => 1])) }}">All Status</a></li>
                    <li><a class="dropdown-item small {{ request('status') == 'active' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['status' => 'active', 'page' => 1])) }}">Active</a></li>
                    <li><a class="dropdown-item small {{ request('status') == 'draft' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['status' => 'draft', 'page' => 1])) }}">Draft</a></li>
                </ul>
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm text-secondary bg-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-2"></i> Sort
                </button>
                <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end">
                    <li><a class="dropdown-item small {{ !request('sort') || request('sort') == 'newest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['sort' => 'newest', 'page' => 1])) }}">Newest First</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'oldest' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['sort' => 'oldest', 'page' => 1])) }}">Oldest First</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'name_asc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['sort' => 'name_asc', 'page' => 1])) }}">Name (A-Z)</a></li>
                    <li><a class="dropdown-item small {{ request('sort') == 'name_desc' ? 'active bg-light text-dark fw-bold' : '' }}" href="{{ route('admin.collections', array_merge(request()->query(), ['sort' => 'name_desc', 'page' => 1])) }}">Name (Z-A)</a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-start">
            <thead class="bg-light text-secondary small text-uppercase fw-medium">
                 <tr>
                    <th class="px-4 py-3" style="width: 50px;">
                        <input class="form-check-input" type="checkbox">
                    </th>
                    <th class="px-4 py-3 border-bottom">Title</th>
                    <th class="px-4 py-3 border-bottom">Products</th>
                    <th class="px-4 py-3 border-bottom">Conditions</th>
                    <th class="px-4 py-3 border-bottom" style="width: 100px;"></th>
                 </tr>
            </thead>
            <tbody class="divide-y" id="collections-table-body">
                @include('admin.collections.partials.table')
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const tableBody = document.getElementById('collections-table-body');
        let debounceTimer;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            const query = e.target.value;
            
            // Update URL without reloading
            const url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            url.searchParams.set('page', 1);
            window.history.pushState({}, '', url);

            debounceTimer = setTimeout(() => {
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    tableBody.innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
            }, 300);
        });
    });
</script>
<style>
    .text-decoration-underline-hover:hover { text-decoration: underline !important; }
    .hover-text-primary:hover { color: #8b5cf6 !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
    .cursor-pointer { cursor: pointer; }

    /* Pagination Overrides */
    .page-link {
        color: #8b5cf6;
        border-color: #dee2e6;
    }
    .page-link:hover {
        color: #6d28d9;
        background-color: #e6f2f0;
        border-color: #dee2e6;
    }
    .page-item.active .page-link {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
        color: white;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
@endsection
