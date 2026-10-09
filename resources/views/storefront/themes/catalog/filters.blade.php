<aside class="ct-filters" id="catalog-filters"><div class="ct-filter-heading"><span>Refine your selection</span><a href="{{ $clearUrl }}">Reset</a></div>
    <form action="{{ request()->url() }}" method="GET" id="filter-form">
        @include('storefront.themes.preview_fields')
        @foreach(['slug', 'category', 'gender', 'collection_id'] as $field)@if(request()->filled($field))<input type="hidden" name="{{ $field }}" value="{{ request()->query($field) }}">@endif @endforeach
        @if(!empty($keyword))<input type="hidden" name="q" value="{{ $keyword }}">@endif
        <div class="ct-filter-group"><label for="ct-sort">Sort by</label><select id="ct-sort" name="sort" onchange="this.form.submit()">@foreach(['latest'=>'Newest arrivals','price_asc'=>'Price: low to high','price_desc'=>'Price: high to low','name_asc'=>'Name: A–Z','name_desc'=>'Name: Z–A'] as $value=>$label)<option value="{{ $value }}" @selected(($currentSort ?? 'latest') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="ct-filter-group"><span>Price range · {{ $currentTenant->currency ?? '₹' }}</span><div class="ct-price-inputs"><input type="number" min="0" step="any" name="min_price" value="{{ $currentMinPrice ?? '' }}" placeholder="Min" aria-label="Minimum price"><span>—</span><input type="number" min="0" step="any" name="max_price" value="{{ $currentMaxPrice ?? '' }}" placeholder="Max" aria-label="Maximum price"></div><button type="submit">Apply price</button></div>
        @if(!empty($allTags))<fieldset class="ct-filter-group"><legend>Product tags</legend><div class="ct-tags">@foreach($allTags as $tag)<label><input type="checkbox" name="tags[]" value="{{ $tag }}" @checked(in_array($tag, $currentTags ?? [])) onchange="this.form.submit()"><span>{{ $tag }}</span></label>@endforeach</div></fieldset>@endif
    </form>
</aside>
