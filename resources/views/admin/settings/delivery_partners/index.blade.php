@extends('layouts.admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold text-primary mb-0">Delivery Partners</h1>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPartnerModal">Add Partner</button>
</div>

<div class="card border shadow-sm">
    <div class="card-header bg-light border-bottom p-3">
        <div class="d-flex gap-3">
            <div class="flex-grow-1">
                 <div class="input-group shadow-sm">
                     <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                     <input type="text" id="searchInput" class="form-control border-start-0 ps-0 shadow-none" placeholder="Search delivery partners...">
                 </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="px-3 py-3 w-auto">ID</th>
                    <th class="px-3 py-3 border-0 fw-medium">Name</th>
                    <th class="px-3 py-3 border-0 fw-medium">Tracking URL</th>
                    <th class="px-3 py-3 border-0 fw-medium">Default</th>
                    <th class="px-3 py-3 border-0 fw-medium">Status</th>
                    <th class="px-3 py-3 text-end" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody id="partnersTableBody">
                @include('admin.settings.delivery_partners.partials.table')
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('partnersTableBody');
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

<!-- Add Modal -->
<div class="modal fade" id="addPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.settings.delivery-partners.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Delivery Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Site URL</label>
                        <input type="url" name="site_url" class="form-control" placeholder="https://example.com">
                    </div>
                    <div class="mb-3">
                        <label>Tracking URL Template</label>
                        <input type="text" name="tracking_url_template" class="form-control" placeholder="https://example.com/track?id={tracking_number}">
                        <small class="text-muted">Use {tracking_number} as placeholder</small>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editPartnerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Delivery Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Site URL</label>
                        <input type="url" name="site_url" id="edit_site_url" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Tracking URL Template</label>
                        <input type="text" name="tracking_url_template" id="edit_tracking_url_template" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function editPartner(partner) {
        document.getElementById('edit_name').value = partner.name;
        document.getElementById('edit_site_url').value = partner.site_url;
        document.getElementById('edit_tracking_url_template').value = partner.tracking_url_template;
        document.getElementById('edit_status').value = partner.status;
        
        document.getElementById('editForm').action = `/admin/settings/delivery-partners/${partner.id}`;
        
        new bootstrap.Modal(document.getElementById('editPartnerModal')).show();
    }
</script>
@endsection
