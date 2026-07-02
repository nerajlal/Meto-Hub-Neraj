<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    private function tenantId()
    {
        return session('active_tenant_id') 
            ?? (auth()->check() ? auth()->user()->tenant_id : null) 
            ?? session('demo_tenant_id') 
            ?? 1;
    }

    private function getView($name)
    {
        $tenantId = $this->tenantId();
        $tenant = \App\Models\Tenant::find($tenantId);
        $theme = $tenant ? $tenant->theme : 'template_1';
        
        if ($theme === 'template_2') {
            return "template_2.{$name}";
        }
        return "template_1.{$name}";
    }

    public function about()
    {
        return view($this->getView('about'));
    }

    public function contact()
    {
        return view($this->getView('contact'));
    }

    public function home()
    {
        $tenantId = $this->tenantId();
        $sliders = \App\Models\Slider::where('tenant_id', $tenantId)->where('status', true)->orderBy('order', 'asc')->get();
        $bestsellers = \App\Models\HomeProduct::where('tenant_id', $tenantId)->with(['product.variants', 'product.images'])->orderBy('sort_order', 'asc')->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', true)->get();
        $bundles = \App\Models\Bundle::where('tenant_id', $tenantId)->where('status', 'active')->where('type', 'bundle')->with(['products.variants'])->latest()->take(4)->get();
        return view($this->getView('home'), compact('sliders', 'bestsellers', 'collections', 'bundles'));
    }

    public function v3Home()
    {
        $tenantId = $this->tenantId();
        $sliders = \App\Models\Slider::where('tenant_id', $tenantId)->where('status', true)->orderBy('order', 'asc')->get();
        $bestsellers = \App\Models\HomeProduct::where('tenant_id', $tenantId)->with(['product.variants', 'product.images'])->orderBy('sort_order', 'asc')->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', true)->get();
        $bundles = \App\Models\Bundle::where('tenant_id', $tenantId)->where('status', 'active')->where('type', 'bundle')->with(['products.variants'])->latest()->take(4)->get();
        return view($this->getView('home'), compact('sliders', 'bestsellers', 'collections', 'bundles'));
    }

    public function ajmalHome()
    {
        $tenantId = $this->tenantId();
        $sliders = \App\Models\Slider::where('tenant_id', $tenantId)->where('status', true)->orderBy('order', 'asc')->get();
        // Fetching only a limited set for home page sections to improve performance
        $products = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->with(['variants', 'images'])->latest()->take(20)->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', true)->take(8)->get();
        $bestsellers = \App\Models\HomeProduct::where('tenant_id', $tenantId)->with(['product.variants', 'product.images'])->orderBy('sort_order', 'asc')->get();
        return view('v4.home', compact('products', 'collections', 'sliders', 'bestsellers'));
    }

    public function collection(Request $request)
    {
        return $this->handleCollection($request, $this->getView('collection'));
    }

    public function v3Collection(Request $request)
    {
        return $this->handleCollection($request, $this->getView('collection'));
    }

    public function ajmalCollection(Request $request)
    {
        if (!$request->has('slug') && !$request->has('category') && !$request->has('gender')) {
            $collections = \App\Models\Collection::where('tenant_id', $this->tenantId())->where('status', true)->get();
            return view('v4.collections-index', compact('collections'));
        }
        return $this->handleCollection($request, 'v4.collection');
    }

    private function handleCollection(Request $request, $view)
    {
        $title = 'Collection';
        $tenantId = $this->tenantId();
        $query = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->with(['variants', 'images']);
        $collection = null;
        if ($request->has('slug')) {
            $collection = \App\Models\Collection::where('tenant_id', $tenantId)->where('slug', $request->query('slug'))->first();
            if ($collection) { $title = $collection->name; $query->where('collection_id', $collection->id); }
        } elseif ($request->has('category')) {
            $category = $request->query('category');
            if ($category == 'fresh') { $title = 'Fresh Collection'; $query->where('olfactory_family', 'LIKE', '%Fresh%'); }
            elseif ($category == 'oriental-woody') { $title = 'Oriental & Woody Collection'; $query->where(function($q) { $q->where('olfactory_family', 'LIKE', '%Oriental%')->orWhere('olfactory_family', 'LIKE', '%Woody%'); }); }
            elseif ($category == 'floral') { $title = 'Floral Collection'; $query->where('olfactory_family', 'LIKE', '%Floral%'); }
            else {
                $collection = \App\Models\Collection::where('tenant_id', $tenantId)->where('slug', $category)->first();
                if ($collection) { $title = $collection->name; $query->where('collection_id', $collection->id); }
            }
        } elseif ($request->has('gender')) {
            $gender = $request->query('gender');
            if ($gender == 'for-him') { $title = 'Perfumes For Him'; $query->whereIn('gender', ['Men', 'Male', 'Him']); }
            elseif ($gender == 'for-her') { $title = 'Perfumes For Her'; $query->whereIn('gender', ['Women', 'Female', 'Her']); }
            elseif ($gender == 'unisex') { $title = 'Unisex Collection'; $query->whereIn('gender', ['Unisex', 'All']); }
        }
        $products = $query->latest()->get();
        $counts = ['stock_in' => 0, 'stock_out' => 0, 'gender_him' => 0, 'gender_her' => 0, 'gender_unisex' => 0, 'size_50ml' => 0, 'size_100ml' => 0];
        foreach($products as $product) {
            $inStock = $product->variants->sum('stock') > 0;
            if($inStock) $counts['stock_in']++; else $counts['stock_out']++;
            $g = strtolower($product->gender);
            if(in_array($g, ['men', 'man', 'him', 'male'])) $counts['gender_him']++;
            elseif(in_array($g, ['women', 'woman', 'her', 'female'])) $counts['gender_her']++;
            else $counts['gender_unisex']++;
            $sizes = $product->variants->pluck('size')->map(fn($s) => strtolower($s))->toArray();
            if(in_array('50ml', $sizes)) $counts['size_50ml']++;
            if(in_array('100ml', $sizes)) $counts['size_100ml']++;
        }
        $bundles = \App\Models\Bundle::where('tenant_id', $tenantId)->where('status', 'active')->where('type', '!=', 'pool')->with(['products.variants'])->latest()->get();
        return view($view, ['title' => $title, 'products' => $products, 'counts' => $counts, 'bundles' => $bundles]);
    }

    public function allProducts(Request $request)
    {
        return $this->handleAllProducts($this->getView('all-products'), $request);
    }

    public function v3AllProducts(Request $request)
    {
        return $this->handleAllProducts($this->getView('all-products'), $request);
    }

    private function handleAllProducts($view, Request $request)
    {
        $tenantId = $this->tenantId();
        $query    = \App\Models\Product::where('tenant_id', $tenantId)
                        ->where('status', 'active')
                        ->with(['variants']);

        // Keyword search
        $keyword = $request->query('q', '');
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        $products = $query->latest()->get();

        $counts = [
            'stock_in'       => 0, 'stock_out'    => 0,
            'gender_him'     => 0, 'gender_her'   => 0, 'gender_unisex' => 0,
            'size_50ml'      => 0, 'size_100ml'   => 0,
        ];
        foreach ($products as $product) {
            $inStock = $product->variants->sum('stock') > 0;
            if ($inStock) $counts['stock_in']++; else $counts['stock_out']++;
            $g = strtolower($product->gender ?? '');
            if (in_array($g, ['men', 'man', 'him']))         $counts['gender_him']++;
            elseif (in_array($g, ['women', 'woman', 'her'])) $counts['gender_her']++;
            else                                              $counts['gender_unisex']++;
            $sizes = $product->variants->pluck('size')->map(fn($s) => strtolower($s))->toArray();
            if (in_array('50ml', $sizes))  $counts['size_50ml']++;
            if (in_array('100ml', $sizes)) $counts['size_100ml']++;
        }

        $bundlesQuery = \App\Models\Bundle::where('tenant_id', $tenantId)
                        ->where('status', 'active')
                        ->where('type', '!=', 'pool')
                        ->with(['products.variants'])
                        ->latest();
        
        // If we have search results, only show bundles that include at least one of those products
        if ($keyword !== '') {
            $productIds = $products->pluck('id')->toArray();
            $bundlesQuery->whereHas('products', function($q) use ($productIds) {
                $q->whereIn('products.id', $productIds);
            });
        }
        
        $bundles = $bundlesQuery->get();

        return view($view, [
            'title'    => $keyword ? "Search results for \"$keyword\"" : 'All Products',
            'products' => $products,
            'counts'   => $counts,
            'bundles'  => $bundles,
            'keyword'  => $keyword,
        ]);
    }

    public function combos()
    {
        return $this->handleCombos($this->getView('combos'));
    }

    public function v3Combos()
    {
        return $this->handleCombos($this->getView('combos'));
    }

    private function handleCombos($view)
    {
        $query = \App\Models\Bundle::where('tenant_id', $this->tenantId())->where('status', 'active');
        
        if (str_starts_with($view, 'v3.') || str_starts_with($view, 'v4.')) {
            $query->where('type', 'bundle');
        } else {
            $query->where('type', '!=', 'pool');
        }

        $bundles = $query->with(['products.variants'])->orderBy('type', 'asc')->latest()->get();
        return view($view, ['title' => 'Combos & Bundles', 'bundles' => $bundles]);
    }

    public function combo(Request $request)
    {
        return $this->handleCombo($request, $this->getView('bundle-main'));
    }

    public function v3Combo(Request $request)
    {
        return $this->handleCombo($request, $this->getView('bundle-main'));
    }

    private function handleCombo(Request $request, $view)
    {
        $id = $request->query('id');
        $tenantId = $this->tenantId();
        $bundle = \App\Models\Bundle::where('tenant_id', $tenantId)->where('status', 'active')->with(['products.images', 'products.variants'])->findOrFail($id);
        $relatedBundles = \App\Models\Bundle::where('tenant_id', $tenantId)->where('status', 'active')->where('id', '!=', $id)->inRandomOrder()->limit(4)->get();
        return view($view, compact('bundle', 'relatedBundles'));
    }

    public function ajmalCombos()
    {
        return $this->handleCombos('v4.combos');
    }

    public function ajmalCombo(Request $request)
    {
        return $this->handleCombo($request, 'v4.bundle-main');
    }

    public function product(Request $request)
    {
        return $this->handleProduct($request, $this->getView('product-main'));
    }

    public function v3Product(Request $request)
    {
        return $this->handleProduct($request, $this->getView('product-main'));
    }

    public function ajmalProduct(Request $request)
    {
        return $this->handleProduct($request, 'v4.product-main');
    }

    private function handleProduct(Request $request, $view)
    {
        $id = $request->query('id');
        $tenantId = $this->tenantId();
        $product = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->with(['variants', 'images'])->findOrFail($id);
        $recentlyViewed = session()->get('recently_viewed', []);
        if(($key = array_search($id, $recentlyViewed)) !== false) { unset($recentlyViewed[$key]); }
        array_unshift($recentlyViewed, $id);
        $recentlyViewed = array_slice($recentlyViewed, 0, 4);
        session()->put('recently_viewed', $recentlyViewed);
        $relatedIds = array_diff($recentlyViewed, [$id]);
        if(count($relatedIds) > 0) {
            $relatedProducts = \App\Models\Product::where('tenant_id', $tenantId)->whereIn('id', $relatedIds)->where('status', 'active')->with(['images', 'variants'])->get()->sortBy(function($model) use ($relatedIds) { return array_search($model->id, $relatedIds); });
        } else { $relatedProducts = collect(); }
        $coupon = $this->getActiveCoupon($product);
        $bundle = $product->bundles()->where('status', 'active')->where('type', 'bundle')->first();
        $packBundles = $product->bundles()->where('status', 'active')->where('type', 'pack')->get();
        $poolBundles = $product->bundles()->where('status', 'active')->where('type', 'pool')->with('products')->get();
        return view($view, compact('product', 'relatedProducts', 'coupon', 'bundle', 'packBundles', 'poolBundles'));
    }

    public function velvetProduct($id)
    {
        $tenantId = $this->tenantId();
        $product = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->with(['variants', 'images'])->findOrFail($id);
        $bundle = $product->bundles()->where('status', 'active')->where('type', 'bundle')->first();
        $packBundles = $product->bundles()->where('status', 'active')->where('type', 'pack')->get();
        $poolBundles = $product->bundles()->where('status', 'active')->where('type', 'pool')->with('products')->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', 1)->get();
        return view('velvet.product-main', compact('product', 'bundle', 'packBundles', 'poolBundles', 'collections'));
    }

    public function shippingPolicy() { return view($this->getView('shipping-policy')); }
    public function returnPolicy() { return view($this->getView('return-policy')); }
    public function termsOfService() { return view($this->getView('terms-of-service')); }

    public function checkout()
    {
        return $this->handleCheckout('Checkout.checkout', $this->getView('layouts.app'));
    }

    public function v3Checkout()
    {
        return $this->handleCheckout('Checkout.checkout', $this->getView('layouts.app'));
    }

    public function ajmalCheckout()
    {
        return $this->handleCheckout('Checkout.checkout', 'layouts.storefront');
    }

    public function afnanHome()
    {
        $tenantId = $this->tenantId();
        $sliders = \App\Models\Slider::where('tenant_id', $tenantId)->where('status', true)->orderBy('order', 'asc')->get();
        $products = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->with(['variants', 'images'])->latest()->take(20)->get();
        $collections = \App\Models\Collection::where('tenant_id', $tenantId)->where('status', true)->take(8)->get();
        return view('v5.home', compact('products', 'collections', 'sliders'));
    }

    public function afnanCollection(Request $request)
    {
        if (!$request->has('slug') && !$request->has('category') && !$request->has('gender')) {
            $collections = \App\Models\Collection::where('tenant_id', $this->tenantId())->where('status', true)->get();
            return view('v5.collections-index', compact('collections'));
        }
        return $this->handleCollection($request, 'v5.collection');
    }

    public function afnanAllProducts(Request $request)
    {
        return $this->handleAllProducts('v5.all-products', $request);
    }

    public function afnanProduct(Request $request)
    {
        return $this->handleProduct($request, 'v5.product-main');
    }

    public function afnanCombos()
    {
        return $this->handleCombos('v5.combos');
    }

    public function afnanCombo(Request $request)
    {
        return $this->handleCombo($request, 'v5.bundle-main');
    }

    public function afnanCheckout()
    {
        return $this->handleCheckout('v5.checkout');
    }

    private function handleCheckout($view, $layout = 'template_1.layouts.app')
    {
        $cart = [];
        $address = null;
        $tenantId = $this->tenantId();
        if(\Illuminate\Support\Facades\Auth::check()) {
             $address = \App\Models\UserAddress::where('user_id', \Illuminate\Support\Facades\Auth::id())->where('is_default', true)->first();
             if(!$address) { $address = \App\Models\UserAddress::where('user_id', \Illuminate\Support\Facades\Auth::id())->first(); }
             $items = \App\Models\Cart::where('tenant_id', $tenantId)->where('user_id', \Illuminate\Support\Facades\Auth::id())->with(['product.discounts', 'product.images', 'product.variants', 'bundle'])->get();
             foreach($items as $item) {
                 $stock = 0;
                 if($item->product_id && $item->product) {
                    $price = $item->product->starting_price;
                    $variantId = null;
                    if ($item->size) { 
                        $variant = $item->product->variants->where('size', $item->size)->first(); 
                        $stock = $variant ? $variant->stock : 0; 
                        if ($variant) {
                            $price = $variant->price;
                            $variantId = $variant->id;
                        }
                    }
                    else { $stock = $item->product->variants->sum('stock'); }
                    
                    if($stock > 0) {
                        $cart[$item->product_id . '-' . $item->size] = [
                            "name" => $item->product->title, 
                            "quantity" => $item->quantity, 
                            "price" => $price, 
                            "image" => $item->product->main_image_url, 
                            "product_id" => $item->product_id, 
                            "variant_id" => $variantId,
                            "bundle_id" => null, 
                            "size" => $item->size, 
                            "type" => "product", 
                            "coupon" => $this->getActiveCoupon($item->product)
                        ];
                    }
                } elseif ($item->bundle_id && $item->bundle) {
                    if (!$item->bundle->is_out_of_stock) {
                        $cart['bundle-' . $item->bundle_id] = ["name" => $item->bundle->title, "quantity" => $item->quantity, "price" => $item->bundle->total_price, "image" => \Illuminate\Support\Facades\Storage::url($item->bundle->image), "product_id" => null, "bundle_id" => $item->bundle_id, "size" => null, "type" => "bundle"];
                    }
                }
             }
        } else {
            $sessionCart = session()->get('cart', []);
            foreach($sessionCart as $key => $item) {
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = \App\Models\Product::where('tenant_id', $tenantId)->find($item['product_id']);
                    if($product) {
                        $stock = 0;
                        if(isset($item['size']) && $item['size']) { $variant = $product->variants->where('size', $item['size'])->first(); $stock = $variant ? $variant->stock : 0; }
                        else { $stock = $product->variants->sum('stock'); }
                        if($stock > 0) { $item['coupon'] = $this->getActiveCoupon($product); $cart[$key] = $item; }
                    }
                } elseif(isset($item['type']) && $item['type'] == 'bundle') {
                     if(isset($item['bundle_id'])) { $bundle = \App\Models\Bundle::where('tenant_id', $tenantId)->find($item['bundle_id']); if ($bundle && !$bundle->is_out_of_stock) { $cart[$key] = $item; } }
                }
            }
        }
        $cartData = \App\Services\CartService::calculateTotal($cart);
        $subtotal = $cartData['total'];
        $savings = $cartData['savings'];

        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
        }
        $total = $subtotal + $taxAmount;

        return view($view, compact('cart', 'total', 'subtotal', 'savings', 'address', 'layout', 'taxAmount', 'taxRate', 'taxName'));
    }

    private function getActiveCoupon($product)
    {
        if(!$product) return null;
        return $product->discounts()->where('status', 'active')->where(function($q) { $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()); })->where(function($q) { $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()); })->orderByDesc('value')->first();
    }
}
