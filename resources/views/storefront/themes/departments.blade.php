@php
    $departments = \App\Models\Collection::where('status', true)->orderBy('name')->get();
@endphp
<aside class="theme-department-sidebar" aria-label="Store departments">
    <span class="theme-eyebrow">EXPLORE THE STORE</span>
    <h2>Departments</h2>
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}"><i class="fa-solid fa-store" aria-hidden="true"></i> All products</a>
    @foreach($departments as $department)
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $department->slug]) }}"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i> {{ $department->name }}</a>
    @endforeach
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}"><i class="fa-solid fa-box-open" aria-hidden="true"></i> Bundles & combos</a>
    <div class="theme-sidebar-note">Make yourself at home.<br>There’s plenty to explore.</div>
</aside>
