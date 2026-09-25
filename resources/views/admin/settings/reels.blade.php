@extends('layouts.admin')

@section('title', 'Instagram Reels')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary"><i class="fab fa-instagram me-2"></i>Instagram Reels</h1>
            <p class="text-muted small mb-0">Add Instagram reel links and connect them to products or collections. Customers can watch reels and shop directly.</p>
        </div>
        <a href="{{ route('admin.settings.reels.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i> Add New Reel
        </a>
    </div>

    <!-- Reels List -->
    <div class="card border shadow-sm">
        <div class="list-group list-group-flush" id="sortable-reels">
            
            @forelse($reels as $reel)
            <div class="list-group-item p-3 d-flex flex-column flex-sm-row align-items-sm-center gap-3 hover-bg-light transition-colors draggable-item" draggable="true" data-id="{{ $reel->id }}">
                
                <!-- Drag Handle + Thumbnail -->
                <div class="d-flex align-items-center gap-3">
                    <div class="cursor-move text-secondary px-2 handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>

                    <!-- Thumbnail / Reel Icon -->
                    <div class="position-relative bg-dark border rounded overflow-hidden flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 55px; height: 80px;">
                        @if($reel->thumbnail)
                            <img src="{{ Storage::url($reel->thumbnail) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="fab fa-instagram text-white fa-lg"></i>
                        @endif
                        <span class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white text-center" style="font-size: 9px; padding: 1px;">Reel</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow-1 min-w-0">
                    <h3 class="h6 fw-bold text-primary mb-1 text-truncate">{{ $reel->title ?? 'Untitled Reel' }}</h3>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted small text-truncate" style="max-width: 280px;">
                            <i class="fas fa-link me-1"></i>{{ $reel->instagram_url }}
                        </span>
                    </div>
                    <div class="mt-1">
                        @if($reel->link_type === 'product' && $reel->product)
                            <span class="badge bg-primary-subtle text-primary rounded-pill">
                                <i class="fas fa-box me-1"></i>{{ $reel->product->title }}
                            </span>
                        @elseif($reel->link_type === 'collection' && $reel->collection)
                            <span class="badge bg-info-subtle text-info rounded-pill">
                                <i class="fas fa-layer-group me-1"></i>{{ $reel->collection->name }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning rounded-pill">
                                <i class="fas fa-unlink me-1"></i>Not linked
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Status + Actions -->
                <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3 w-100 w-sm-auto border-top border-sm-0 pt-2 pt-sm-0 mt-2 mt-sm-0">
                    <span class="badge {{ $reel->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill fw-normal">
                        {{ $reel->status ? 'Active' : 'Hidden' }}
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.settings.reels.edit', $reel->id) }}" class="btn btn-link btn-sm p-2 text-secondary" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.settings.reels.destroy', $reel->id) }}" method="POST" onsubmit="return confirm('Delete this reel?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link btn-sm p-2 text-secondary hover-text-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item p-5 text-center text-muted">
                <i class="fab fa-instagram fa-3x mb-3 opacity-25"></i>
                <p class="mb-2 fw-bold">No reels added yet</p>
                <p class="small">Add Instagram reel links to showcase your products through engaging video content.</p>
            </div>
            @endforelse

        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: var(--bs-light) !important; }
    .object-fit-cover { object-fit: cover; }
    .hover-text-danger:hover { color: var(--bs-danger) !important; }
    .handle:hover { color: var(--bs-dark) !important; }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('sortable-reels');
        if (!container) return;
        let draggables = document.querySelectorAll('.draggable-item');

        function initDraggables() {
            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                    draggable.style.opacity = '0.5';
                });
                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                    draggable.style.opacity = '1';
                    saveOrder();
                });
            });
        }

        initDraggables();

        container.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            const draggable = document.querySelector('.dragging');
            if (afterElement == null) {
                container.appendChild(draggable);
            } else {
                container.insertBefore(draggable, afterElement);
            }
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function saveOrder() {
            const items = container.querySelectorAll('.draggable-item');
            const order = Array.from(items).map(item => item.getAttribute('data-id'));

            fetch('{{ route("admin.settings.reels.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Reorder saved
                }
            });
        }
    });
</script>
@endpush
