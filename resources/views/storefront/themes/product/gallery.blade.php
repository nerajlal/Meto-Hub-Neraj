<div class="product-gallery pd-gallery {{ $detailData['images']->count() === 1 ? 'pd-single-image' : '' }}">
    <div class="main-image-display"><img id="p-main-img" src="{{ $detailData['images']->first() }}" alt="{{ $product->title }}"></div>
    @if($detailData['images']->count() > 1)<div class="thumb-grid">@foreach($detailData['images'] as $image)<button type="button" class="thumb-item {{ $loop->first ? 'active' : '' }}" onclick="updateImg({{ \Illuminate\Support\Js::from($image) }}, this)" aria-label="View image {{ $loop->iteration }} of {{ $product->title }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><img src="{{ $image }}" alt="" loading="lazy"></button>@endforeach</div>@endif
</div>
