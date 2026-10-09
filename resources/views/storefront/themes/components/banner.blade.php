@if($sliders->isNotEmpty())
<div class="sf-merchant-banners" aria-label="Store banners">
    @foreach($sliders as $slider)
        @php
            $desktop = $slider->image_desktop;
            $mobile = $slider->image_mobile ?: $desktop;
            $imageUrl = fn ($path) => \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) ? $path : (\Illuminate\Support\Str::startsWith(strtolower($path), 'images/') ? asset(strtolower(substr($path, 0, 6)).substr($path, 6)) : Storage::url($path));
        @endphp
        <figure class="sf-merchant-banner">
            <picture><source media="(max-width:768px)" srcset="{{ $imageUrl($mobile) }}"><img src="{{ $imageUrl($desktop) }}" alt="{{ $slider->title ?: $currentTenant->name . ' store banner' }}" {{ $loop->first ? 'fetchpriority=high' : 'loading=lazy' }}></picture>
        </figure>
    @endforeach
</div>
@endif
