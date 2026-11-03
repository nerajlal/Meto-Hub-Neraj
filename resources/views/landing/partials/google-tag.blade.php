@php
    $googleTagId = trim((string) config('marketing.analytics.google_tag', 'G-BRLSZ74E9Y'));
@endphp
@if(!empty($googleTagId) && preg_match('/^[A-Za-z0-9\-_]+$/', $googleTagId))
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleTagId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{{ $googleTagId }}');
</script>
@endif

