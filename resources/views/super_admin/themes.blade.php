@extends('super_admin.layouts.app')
@section('title', 'Themes')
@section('content')
<div class="mb-4"><h1 class="h3 fw-bold text-dark">Storefront Themes</h1><p class="text-muted mb-1">{{ $themes->count() }} themes · {{ $themes->sum('stores') }} store assignments</p><p class="small text-muted">Browse all available designs. Illustrations show the layout; live previews use demo content and do not change any store’s selected theme.</p></div>
<div class="row g-4">
@foreach($themes as $id => $theme)
<div class="col-sm-6 col-xl-4"><article class="card border h-100 overflow-hidden" data-theme-id="{{ $id }}">
    <img src="{{ asset('images/themes/'.$id.'.svg') }}" alt="{{ $theme['name'] }} layout illustration" loading="lazy" width="640" height="400" style="width:100%;height:auto;aspect-ratio:8/5;object-fit:contain;background:{{ $theme['background'] }}">
    <div class="p-3 d-flex flex-column flex-grow-1"><div class="d-flex justify-content-between gap-2 mb-2"><span class="small text-muted">{{ $theme['version'] }}</span><span class="badge bg-primary-subtle text-primary">{{ $theme['stores'] }} stores</span></div>
    <h2 class="h5 fw-bold text-dark">{{ $theme['name'] }}</h2><p class="small text-muted">{{ $theme['description'] }}</p><span class="small text-muted mb-3">{{ $id }}</span>
    @if($theme['preview_url'])<a href="{{ $theme['preview_url'] }}" class="btn btn-outline-primary mt-auto" target="_blank" rel="noopener noreferrer">Live preview <i class="fas fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i></a>@else<p class="small text-muted mt-auto mb-0">Live preview available once a demo store is active.</p>@endif
    </div>
</article></div>
@endforeach
</div>
@endsection
