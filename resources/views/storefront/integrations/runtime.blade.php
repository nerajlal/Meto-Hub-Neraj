@php($trackingConfig = app(\App\Services\StorefrontTracking::class)->bootstrap($currentTenant, get_defined_vars()))
@if($trackingConfig)
<script id="metohub-tracking-config" type="application/json">{!! json_encode($trackingConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<link rel="stylesheet" href="{{ asset('css/store-integrations.css') }}?v={{ is_file(public_path('css/store-integrations.css')) ? filemtime(public_path('css/store-integrations.css')) : 2 }}">
<dialog id="mh-privacy-dialog" class="mh-privacy" aria-labelledby="mh-privacy-title" aria-describedby="mh-privacy-description">
 <h2 id="mh-privacy-title">Your privacy choices</h2>
 <p id="mh-privacy-description">{{ $currentTenant->name }} uses optional analytics and advertising tools. Choose what to allow. Essential store features work whichever you choose.</p>
 <div id="mh-privacy-options" hidden>
 <label><input type="checkbox" id="mh-consent-analytics"> Analytics <span>Measure store visits, products and basket activity using Google Analytics.</span></label>
 <label><input type="checkbox" id="mh-consent-marketing"> Advertising <span>Measure product and basket activity with Meta Pixel.</span></label>
 @if($trackingConfig['mode']==='gtm')<p>Tag Manager loads only when both categories are allowed. Tags published by this store may use both categories.</p>@endif
 </div>
 <div class="mh-privacy-actions"><button type="button" data-mh-consent="accept">Accept all</button><button type="button" data-mh-consent="reject">Reject optional</button><button type="button" data-mh-consent="manage">Manage choices</button><button type="button" data-mh-consent="save" hidden>Save choices</button></div>
</dialog>
<button id="mh-privacy-open" class="mh-privacy-open" type="button">Privacy choices</button>
<script src="{{ asset('js/store-integrations.js') }}?v={{ is_file(public_path('js/store-integrations.js')) ? filemtime(public_path('js/store-integrations.js')) : 2 }}" defer></script>
@endif
