@php
    $pickerProducts = $products->map(fn ($product) => [
        'id' => $product->id, 'title' => $product->title, 'active' => $product->status === 'active',
        'variants' => $product->variants->map(fn ($variant) => ['id' => $variant->id, 'label' => $variant->size ?: 'Standard', 'price' => (float) $variant->price, 'stock' => (int) $variant->stock])->values(),
    ])->values();
    $existingRows = isset($bundle) ? $bundle->products->map(fn ($product) => ['product' => $product->id, 'variant' => $product->pivot->product_variant_id, 'quantity' => $product->pivot->quantity ?? 1])->values()->all() : [];
    $pickerRows = old('products') !== null ? collect(old('products'))->values()->map(fn ($id, $index) => ['product' => $id, 'variant' => old('variant_ids.'.$index), 'quantity' => old('quantities.'.$index, 1)])->all() : $existingRows;
@endphp
<div data-bundle-picker>
    <label class="form-label small fw-medium" for="bundle-product-search">Find a product</label>
    <input type="search" id="bundle-product-search" class="form-control mb-2" placeholder="Search products…" autocomplete="off">
    <div data-bundle-results class="list-group mb-3" style="max-height:220px;overflow-y:auto" hidden></div>
    <p class="small text-muted">Choose the variant and quantity included in each bundle. Add the same product again to include another variant.</p>
    <div data-bundle-rows class="vstack gap-3"></div>
    <script>
    window.bundlePickerData = {
        products: {{ \Illuminate\Support\Js::from($pickerProducts) }},
        rows: {{ \Illuminate\Support\Js::from($pickerRows) }},
        currency: {{ \Illuminate\Support\Js::from($currentTenant->currency ?? '₹') }}
    };
    </script>
    <script src="{{ asset('js/admin-bundle-picker.js').'?v='.filemtime(public_path('js/admin-bundle-picker.js')) }}" defer></script>
</div>
