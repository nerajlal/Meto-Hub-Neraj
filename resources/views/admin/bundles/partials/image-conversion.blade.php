@once
@push('scripts')
<script src="{{ asset('js/bundle-image-upload.js') }}?v={{ filemtime(public_path('js/bundle-image-upload.js')) }}" defer></script>
@endpush
@endonce
