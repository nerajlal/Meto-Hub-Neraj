@extends('template_1.layouts.app')
@section('title', $bundle->title . ' | ' . $currentTenant->name)
@section('content')
@php
    $presentation = app(\App\Services\StorefrontProduct::class);
    $total = round((float) $bundle->total_price, 2);
    $base = round((float) $bundle->base_price, 2);
    $saving = max(0, round($base - $total, 2));
    $included = $bundle->products->map(function ($item) use ($presentation) {
        $data = $presentation->data($item);
        $variant = $item->pivot->product_variant_id ? $data['variants']->firstWhere('id', $item->pivot->product_variant_id) : $data['selected'];
        return ['product' => $item, 'variant' => $variant, 'quantity' => (int) ($item->pivot->quantity ?? 1), 'image' => $variant['image_url'] ?? $data['images']->first()];
    });
    $images = collect($bundle->image ? [$presentation->imageUrl($bundle->image)] : [])->merge($included->pluck('image'))->filter()->unique()->values();
    if ($images->isEmpty()) $images = collect([asset('images/default.png')]);
    $count = $included->sum('quantity');
    $kind = $bundle->type === 'pack' ? 'Pack of '.$count : 'Combo selection';
    $available = !$bundle->is_out_of_stock;
@endphp
<article class="pd-page pd-pantryselect ps-combo-page" data-combo-detail="pantryselect" data-combo-key="bundle-{{ $bundle->id }}" data-combo-available="{{ $available ? '1' : '0' }}" data-combo-price="{{ $currentTenant->currency ?? '₹' }}{{ number_format($total, 2) }}">
    <nav class="pd-breadcrumb" aria-label="Breadcrumb"><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}">Home</a><span>/</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">Combos & packs</a><span>/</span><span aria-current="page">{{ $kind }}</span></nav>
    <div class="ps-product-layout">
        <div class="product-gallery pd-gallery {{ $images->count() === 1 ? 'pd-single-image' : '' }}">
            <div class="main-image-display"><img id="p-main-img" src="{{ $images->first() }}" alt="{{ $bundle->title }}"></div>
            @if($images->count() > 1)<div class="thumb-grid">@foreach($images as $image)<button type="button" class="thumb-item {{ $loop->first ? 'active' : '' }}" data-combo-image="{{ $image }}" aria-label="View image {{ $loop->iteration }} of {{ $bundle->title }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><img src="{{ $image }}" alt="" loading="lazy"></button>@endforeach</div>@endif
        </div>
        <div class="ps-product-order">
            <div class="ps-combo-heading"><span class="ps-combo-kind"><i class="fa-solid fa-box-open" aria-hidden="true"></i> {{ $kind }}</span><span class="ps-combo-count">{{ $count }} {{ $count === 1 ? 'item' : 'items' }} together</span></div>
            <h1 class="p-title">{{ $bundle->title }}</h1>
            <div class="pd-purchase">
                <div class="p-price-row"><strong class="p-current-price">{{ $currentTenant->currency ?? '₹' }}{{ number_format($total, 2) }}</strong>@if($saving > 0)<del class="p-compare-at" aria-label="Original total">{{ $currentTenant->currency ?? '₹' }}{{ number_format($base, 2) }}</del>@endif</div>
                @if($saving > 0)<p class="ps-combo-saving"><i class="fa-solid fa-tag" aria-hidden="true"></i> Save {{ $currentTenant->currency ?? '₹' }}{{ number_format($saving, 2) }} with this {{ $bundle->type === 'pack' ? 'pack' : 'combo' }}</p>@endif
                <p class="pd-stock">{{ $available ? 'Available to order' : 'Currently unavailable' }}</p>
                <div class="ps-combo-contents"><h2>Inside your {{ $bundle->type === 'pack' ? 'pack' : 'combo' }}</h2>
                    @foreach($included as $item)
                    <a class="ps-combo-item" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.product', ['id' => $item['product']->id]) }}"><img src="{{ $item['image'] }}" alt="" loading="lazy"><span><strong>{{ $item['product']->title }}</strong><small>{{ ($item['variant']['size'] ?? '') ?: 'Standard' }}</small></span><b>×{{ $item['quantity'] }}</b></a>
                    @endforeach
                </div>
                <div class="p-actions-row" id="main-product-actions" data-bundle-id="{{ $bundle->id }}"><div class="qty-control"><button type="button" onclick="updateMainCart(-1)" aria-label="Remove one {{ $kind }}">−</button><span class="page-qty" id="main-qty-value" aria-live="polite">0</span><button type="button" onclick="updateMainCart(1)" aria-label="Add one {{ $kind }}" @disabled(!$available)>+</button></div><button type="button" class="btn-add-to-cart" id="add-to-cart-bundle-btn" onclick="addOrOpenCart()" @disabled(!$available)>{{ $available ? 'Add to basket' : 'Out of stock' }} · {{ $currentTenant->currency ?? '₹' }}{{ number_format($total, 2) }}</button></div>
                <div class="delivery-note"><i class="fa-solid fa-truck" aria-hidden="true"></i><span>Estimated delivery {{ now()->addDays($currentTenant->delivery_days ?? 2)->format('D, M j') }}</span></div>
            </div>
            <div class="ps-product-help"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Have a question? Contact the store</a></div>
        </div>
    </div>
    <div class="ps-product-information ps-combo-information"><div><span class="pd-eyebrow">THE DETAILS</span><h2>About this {{ $bundle->type === 'pack' ? 'pack' : 'combo' }}</h2><p>{{ filled($bundle->description) ? $bundle->description : 'Explore the products included above, selected together by '.$currentTenant->name.'.' }}</p></div><div><span class="pd-eyebrow">SHOP WITH CONFIDENCE</span><h2>Freshness guarantee</h2><p>Freshness Guarantee: If any item is not up to your standard, get a refund at your door.</p><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.return-policy') }}">View return policy ↗</a></div></div>
    @if($relatedBundles->isNotEmpty())<section class="sf-shelf"><div class="sf-section-heading"><span>MORE TO EXPLORE</span><h2>Find your next favourite combo</h2><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">All combos & packs →</a></div><div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">@foreach($relatedBundles as $related)@include('template_1.partials.bundle_card', ['bundle' => $related])@endforeach</div></section>@endif
</article>
@endsection
@section('scripts')
<script src="{{ asset('js/pantry-combo.js') }}?v={{ filemtime(public_path('js/pantry-combo.js')) }}" defer></script>
@endsection
