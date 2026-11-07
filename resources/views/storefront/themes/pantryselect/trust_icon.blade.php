@php
    $highlightArtwork = ['vacuum-packed', 'oil-free', 'carefully-selected', 'quality-checked', 'delivery', 'freshness', 'certification', 'quality-checked'];
@endphp
<img src="{{ asset('images/storefront/highlights/'.$highlightArtwork[$iconIndex % count($highlightArtwork)].'.svg') }}" width="40" height="40" alt="" aria-hidden="true" loading="lazy">
