@if($product->olfactory_family || $product->notes_top || $product->notes_heart || $product->notes_base || $product->intensity || $product->oil_concentration)
<section class="rb-fragrance-notes"><h2>Fragrance profile</h2><dl>
@foreach(['olfactory_family' => 'Family', 'notes_top' => 'Top notes', 'notes_heart' => 'Heart notes', 'notes_base' => 'Base notes', 'intensity' => 'Intensity', 'oil_concentration' => 'Concentration'] as $field => $label)
@if($product->$field)<div><dt>{{ $label }}</dt><dd>{{ $product->$field }}</dd></div>@endif
@endforeach
</dl></section>
@endif
