@extends('layouts.admin')
@section('title', 'Search Readiness')
@section('content')
<div class="container-fluid py-4">
 <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div><h1 class="h3">Search Readiness</h1><p class="text-muted mb-0">Control how your store appears in search and social shares.</p></div>
  <a class="btn btn-outline-primary" href="{{ route('admin.settings.integrations') }}">Integrations</a>
  <button form="seo-settings" class="btn btn-primary" type="submit">Save Changes</button>
 </div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
 <div class="row g-4"><div class="col-lg-7">
 <form id="seo-settings" method="POST" action="{{ route('admin.settings.seo.update') }}">@csrf
 <div class="card mb-4"><div class="card-body">
  @include('admin.settings.partials.seo-fields', ['entity'=>$tenant])
  <input type="hidden" name="search_indexable" value="0">
  <div class="form-check mt-3"><input class="form-check-input" id="search_indexable" name="search_indexable" type="checkbox" value="1" @checked(old('search_indexable', $tenant->search_indexable ?? true))><label class="form-check-label" for="search_indexable">Allow search engines to index this store</label></div>
  <p class="small text-muted mt-2">Turning this off adds noindex and excludes the store from sitemaps. Removal from search can take time. This does not make your store private.</p>
  @unless(config('search.indexable'))<div class="alert alert-warning">Indexing is disabled by the server. Your store preference cannot override this.</div>@endunless
  <button class="btn btn-primary mt-2" type="submit">Save Changes</button>
 </div></div></form>
 <div class="card"><div class="card-body"><h2 class="h5">Search preview</h2><p class="small text-muted">Approximation; search engines may choose different text.</p><div style="overflow-wrap:anywhere" class="small text-muted">{{ $seo->url($tenant) }}</div><div id="seo-preview-title" class="h5 text-primary mt-2">{{ $tenant->seo_title ?: $tenant->name }}</div><p id="seo-preview-description">{{ $tenant->seo_description ?: 'Explore products and collections from '.$tenant->name.'.' }}</p></div></div>
 </div><div class="col-lg-5">
 <div class="card mb-4"><div class="card-body"><h2 class="h5">Readiness checks</h2><p class="small text-muted">Checked against saved settings now. These checks do not measure rankings or external search visibility.</p>
 @foreach($checks as [$label,$passed,$hint])<div class="py-2 border-bottom"><strong>{{ $label }}</strong><span class="badge {{ $passed ? 'bg-success' : 'bg-warning text-dark' }} ms-1">{{ $passed ? 'Ready' : 'Needs attention' }}</span>@unless($passed)<div class="small text-muted mt-1">{{ $hint }}</div>@endunless</div>@endforeach
 <p class="mt-3 mb-0">{{ $missingDescriptions }} published products have no description.</p>
 </div></div>
 <div class="card"><div class="card-body"><h2 class="h5">Sitemap and next steps</h2><a href="{{ $seo->url($tenant, 'sitemap.xml') }}" target="_blank" rel="noopener">Open store sitemap</a><p class="mt-3">Add accurate product descriptions, sizing, delivery and return information. Product and collection editors include their own search settings.</p><p class="small text-muted mb-0">Structured data and clear content help search engines understand your store. Rankings and AI citations are not guaranteed. Connect tracking IDs and Search Console verification in Integrations.</p></div></div>
 </div></div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>{for(const [field,target] of [['seo_title','seo-preview-title'],['seo_description','seo-preview-description']]){const input=document.getElementById(field),preview=document.getElementById(target),fallback=preview.textContent;input.addEventListener('input',()=>{preview.textContent=input.value||fallback;});}});</script>
@endsection
