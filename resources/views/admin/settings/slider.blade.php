@extends('layouts.admin')

@section('title', 'Hero Slider')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">Hero Slider</h1>
            <p class="text-muted small mb-0">Manage home page hero banner slides order and content.</p>
        </div>
        <a href="{{ route('admin.settings.slider.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i> Add New Slide
        </a>
    </div>

    <!-- Slides List (Draggable Style) -->
    <div class="card border shadow-sm">
        <div class="list-group list-group-flush" id="sortable-slides">
            
            @forelse($sliders as $slider)
            <div class="list-group-item p-3 d-flex flex-column flex-sm-row align-items-sm-center gap-3 hover-bg-light transition-colors draggable-item" draggable="true" data-id="{{ $slider->id }}">
                
                <!-- Visuals Row -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Drag Handle -->
                    <div class="cursor-move text-secondary px-2 handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>

                    <!-- Desktop Thumb -->
                    <div class="position-relative bg-light border rounded overflow-hidden flex-shrink-0" style="width: 120px; height: 50px;">
                        <img src="{{ Storage::url($slider->image_desktop) }}" class="w-100 h-100 object-fit-cover">
                        <span class="position-absolute bottom-0 end-0 bg-dark bg-opacity-50 text-white px-1 small" style="font-size: 10px;">Desktop</span>
                    </div>

                    <!-- Mobile Thumb -->
                    <div class="position-relative bg-light border rounded overflow-hidden flex-shrink-0" style="width: 32px; height: 50px;">
                        <img src="{{ Storage::url($slider->image_mobile) }}" class="w-100 h-100 object-fit-cover">
                        <span class="position-absolute bottom-0 end-0 bg-dark bg-opacity-50 text-white px-1 small" style="font-size: 10px;">Mob</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow-1 min-w-0">
                    <h3 class="h6 fw-bold text-primary mb-0 text-truncate">{{ $slider->title ?? 'Untitled Slide' }}</h3>
                </div>

                <!-- Meta Row (Status + Actions) -->
                <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-3 w-100 w-sm-auto border-top border-sm-0 pt-2 pt-sm-0 mt-2 mt-sm-0">
                    <form action="{{ route('admin.settings.slider.status', $slider->id) }}" method="POST" class="slider-status-form" data-enabled="{{ $slider->status ? '1' : '0' }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="0">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="slider-status-{{ $slider->id }}" name="status" value="1" @checked($slider->status) aria-label="Show {{ $slider->title ?: 'this slide' }} on storefront">
                            <label class="form-check-label small" for="slider-status-{{ $slider->id }}" data-status-label>{{ $slider->status ? 'Enabled' : 'Hidden' }}</label>
                        </div>
                        <span class="small d-block" data-status-message role="status" aria-live="polite"></span>
                        <noscript><button type="submit" class="btn btn-sm btn-outline-primary">Save status</button></noscript>
                    </form>

                    <!-- Actions -->
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('admin.settings.slider.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Delete this slide?');">
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
                No slides found. Click "Add New Slide" to get started.
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
        const container = document.getElementById('sortable-slides');
        let draggables = document.querySelectorAll('.draggable-item');

        function initDraggables() {
            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                    draggable.classList.add('bg-light'); // Keep hover style
                    draggable.style.opacity = '0.5';
                });

                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                    draggable.classList.remove('bg-light');
                    draggable.style.opacity = '1';
                    saveOrder();
                });
            });
        }

        initDraggables();

        document.querySelectorAll('.slider-status-form').forEach(form => {
            const toggle = form.querySelector('[role="switch"]');
            const label = form.querySelector('[data-status-label]');
            const message = form.querySelector('[data-status-message]');
            toggle.addEventListener('change', () => form.requestSubmit());
            form.addEventListener('submit', async event => {
                event.preventDefault();
                if (toggle.disabled) return;
                toggle.disabled = true;
                message.classList.remove('text-danger');
                message.textContent = 'Saving…';
                try {
                    const response = await fetch(form.action, {
                        method: 'PATCH',
                        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value},
                        body: JSON.stringify({status: toggle.checked})
                    });
                    if (!response.ok) throw new Error('Save failed');
                    const data = await response.json();
                    if (typeof data.status !== 'boolean') throw new Error('Invalid response');
                    toggle.checked = data.status;
                    form.dataset.enabled = data.status ? '1' : '0';
                    label.textContent = data.status ? 'Enabled' : 'Hidden';
                    message.textContent = data.status ? 'Visible on storefront.' : 'Hidden from storefront.';
                } catch (_) {
                    toggle.checked = form.dataset.enabled === '1';
                    message.classList.add('text-danger');
                    message.textContent = 'Could not save. Please try again.';
                } finally { toggle.disabled = false; }
            });
        });

        container.addEventListener('dragover', e => {
            if (!container.querySelector('.dragging')) return;
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

            fetch('{{ route("admin.settings.slider.reorder") }}', {
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
                    // Optional: Show toast or feedback
                }
            });
        }
    });
</script>
@endpush
