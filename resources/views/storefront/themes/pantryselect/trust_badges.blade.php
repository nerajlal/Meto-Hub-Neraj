@php
    $trustBadges = app(\App\Services\StorefrontTheme::class)->trustBadges($currentTenant);
@endphp
@if(count($trustBadges))
<section class="ps-trust-strip" aria-label="Store highlights" tabindex="0">
    <div class="ps-trust-track">
        @for($copy = 0; $copy < 4; $copy++)
            <ul class="ps-trust-row" @if($copy > 0) aria-hidden="true" @endif>
                @foreach($trustBadges as $label)
                    <li class="ps-trust-item"><span class="ps-trust-icon">@include('storefront.themes.pantryselect.trust_icon', ['iconIndex' => $loop->index])</span><span>{{ $label }}</span></li>
                @endforeach
            </ul>
        @endfor
    </div>
</section>
@endif
