@extends('layouts.admin')

@section('title', 'Attributes & Product Tags')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Attributes & Product Tags</h1>
            <p class="text-muted small mb-0">Manage grocery departments, categories, and tags for your products.</p>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        
        <!-- Families Column -->
        <div class="col-12 col-lg-6">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h2 class="h5 fw-bold text-primary mb-0">Grocery Departments / Families</h2>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ count($families) }} Departments</span>
                </div>
                
                <div class="card-body p-3">
                    <!-- Add Form -->
                    <form action="{{ route('admin.attributes.store') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="family">
                        <div class="input-group">
                            <input type="color" name="color" class="form-control form-control-color" value="#563d7c" style="max-width: 50px;" title="Choose color">
                            <input type="text" name="name" placeholder="Add new department (e.g. Fruits & Vegetables)" class="form-control form-control-sm" required>
                            <button class="btn btn-dark btn-sm">Add</button>
                        </div>
                    </form>

                    <!-- List -->
                    <div class="vstack gap-2">
                        @foreach($families as $family)
                        <!-- View Mode -->
                        <div class="d-flex items-center justify-content-between p-2 bg-light border rounded group-hover-border transition-colors item-row" id="family-view-{{ $family->id }}">
                            <div class="d-flex items-center gap-3">
                                <span class="rounded-circle" style="width: 12px; height: 12px; background-color: {{ $family->color ?? '#ccc' }};"></span>
                                <span class="small fw-medium text-dark item-text">{{ $family->name }}</span>
                            </div>
                            @if(!isset($currentTenant) || $currentTenant->id == 1)
                            <div class="d-flex gap-2 opacity-0 group-hover-opacity transition-opacity text-nowrap item-actions">
                                <button onclick="toggleEdit('family', {{ $family->id }})" class="btn btn-link btn-sm p-0 text-secondary hover-text-primary"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('admin.attributes.destroy', $family->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>

                        @if(!isset($currentTenant) || $currentTenant->id == 1)
                        <!-- Edit Mode (Hidden) -->
                        <div class="d-none p-2 bg-light border rounded" id="family-edit-{{ $family->id }}">
                            <form action="{{ route('admin.attributes.update', $family->id) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                @method('PUT')
                                <input type="color" name="color" class="form-control form-control-color form-control-sm" value="{{ $family->color ?? '#563d7c' }}" title="Choose color">
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $family->name }}" required>
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></button>
                                <button type="button" onclick="toggleEdit('family', {{ $family->id }})" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Column -->
        <div class="col-12 col-lg-6">
            <div class="card border shadow-sm h-100">
                 <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h2 class="h5 fw-bold text-primary mb-0">Product Tags & Attributes</h2>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ count($notes) }} Tags</span>
                </div>

                <div class="card-body p-3">
                    <!-- Add Form -->
                    <form action="{{ route('admin.attributes.store') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="type" value="note">
                        <div class="input-group">
                            <input type="text" name="name" placeholder="Add new tag (e.g. Organic, Fresh, Vegan)" class="form-control form-control-sm" required>
                            <button class="btn btn-primary btn-sm">Add</button>
                        </div>
                    </form>

                    <!-- List with Search -->
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="noteSearch" onkeyup="filterNotes()" placeholder="Search tags..." class="form-control border-start-0 shadow-none">
                    </div>

                    <div class="vstack gap-1 overflow-auto pe-2 custom-scrollbar" style="max-height: 400px;" id="notesList">
                        @foreach($notes as $note)
                        <div class="note-item-container" data-name="{{ strtolower($note->name) }}">
                            <!-- View Mode -->
                            <div class="d-flex items-center justify-content-between px-3 py-2 hover-bg-light rounded transition-colors item-row" id="note-view-{{ $note->id }}">
                                <span class="small text-dark item-text">{{ $note->name }}</span>
                                @if(!isset($currentTenant) || $currentTenant->id == 1)
                                <div class="d-flex gap-2 opacity-0 group-hover-opacity transition-opacity text-nowrap item-actions">
                                    <button onclick="toggleEdit('note', {{ $note->id }})" class="btn btn-link btn-sm p-0 text-secondary hover-text-primary"><i class="fas fa-edit small"></i></button>
                                    <form action="{{ route('admin.attributes.destroy', $note->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this note?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-secondary hover-text-danger"><i class="fas fa-trash small"></i></button>
                                    </form>
                                </div>
                                @endif
                            </div>

                            @if(!isset($currentTenant) || $currentTenant->id == 1)
                            <!-- Edit Mode (Hidden) -->
                            <div class="d-none px-3 py-2 bg-light rounded" id="note-edit-{{ $note->id }}">
                                <form action="{{ route('admin.attributes.update', $note->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $note->name }}" required>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></button>
                                    <button type="button" onclick="toggleEdit('note', {{ $note->id }})" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .hover-text-primary:hover { color: var(--bs-primary) !important; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
    .hover-text-success:hover { color: var(--bs-success) !important; }
    .group-hover-border:hover { border-color: #dee2e6 !important; }
    .item-row:hover .group-hover-opacity { opacity: 1 !important; }
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
</style>

<script>
    function toggleEdit(type, id) {
        const viewEl = document.getElementById(`${type}-view-${id}`);
        const editEl = document.getElementById(`${type}-edit-${id}`);
        
        if (viewEl.classList.contains('d-none')) {
            viewEl.classList.remove('d-none');
            // Check if flexible display is needed
            if(type === 'family') viewEl.classList.add('d-flex');
            
            editEl.classList.add('d-none');
        } else {
            viewEl.classList.add('d-none');
            viewEl.classList.remove('d-flex');
            
            editEl.classList.remove('d-none');
        }
    }

    function filterNotes() {
        const input = document.getElementById('noteSearch');
        const filter = input.value.toLowerCase();
        const nodes = document.querySelectorAll('.note-item-container');

        nodes.forEach(node => {
            const name = node.getAttribute('data-name');
            if (name.includes(filter)) {
                node.style.display = 'block';
            } else {
                node.style.display = 'none';
            }
        });
    }
</script>
@endsection
