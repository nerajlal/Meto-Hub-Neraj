<a class="sf-brand" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}">
    @if($currentTenant->logo)
        <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}">
    @else
        <span class="sf-brand-mark" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</span>
    @endif
    <span>{{ $currentTenant->name }}</span>
</a>
