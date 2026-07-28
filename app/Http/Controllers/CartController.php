<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Bundle;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        if (Auth::check()) {
            self::syncSession(Auth::id());
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
            $cart = array_reverse($cart, true); // Last added first
            
            // Enrich session cart with coupons and stock
            foreach($cart as $key => &$item) {
                $item['stock'] = 100; // Default
                
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if($product) {
                        $item['coupon'] = $this->getActiveCoupon($product);
                        
                        // Stock Check
                        if(isset($item['size']) && $item['size']) {
                            $variant = $product->variants->where('size', $item['size'])->first();
                            $item['stock'] = $variant ? $variant->stock : 0;
                        } else {
                            $item['stock'] = $product->variants->sum('stock');
                        }
                        
                        if ($product->continue_selling_when_out_of_stock) {
                            $item['stock'] = 999;
                        }
                    }
                } elseif (isset($item['type']) && $item['type'] == 'bundle' && isset($item['bundle_id'])) {
                    $bundle = Bundle::find($item['bundle_id']);
                    if ($bundle) {
                        $item['stock'] = $bundle->is_out_of_stock ? 0 : 100;
                    }
                }
            }
        }

        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        $subtotal = $cartData['subtotal'];
        $savings = $cartData['savings'];
        
        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($cartTotalBeforeTax * ($taxRate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;
        
        $minOrderValue = $tenant ? ($tenant->min_order_value ?? 0) : 0;
        return view('template_1.cart', compact('cart', 'total', 'subtotal', 'savings', 'taxAmount', 'taxRate', 'taxName', 'cartTotalBeforeTax', 'minOrderValue'));
    }

    /**
     * Display the shopping cart for V3 theme.
     */
    public function v3Index()
    {
        if (Auth::check()) {
            self::syncSession(Auth::id());
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
            $cart = array_reverse($cart, true); 
            
            foreach($cart as $key => &$item) {
                $item['stock'] = 100; 
                
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if($product) {
                        $item['coupon'] = $this->getActiveCoupon($product);
                        
                        if(isset($item['size']) && $item['size']) {
                            $variant = $product->variants->where('size', $item['size'])->first();
                            $item['stock'] = $variant ? $variant->stock : 0;
                        } else {
                            $item['stock'] = $product->variants->sum('stock');
                        }
                        
                        if ($product->continue_selling_when_out_of_stock) {
                            $item['stock'] = 999;
                        }
                    }
                } elseif (isset($item['type']) && $item['type'] == 'bundle' && isset($item['bundle_id'])) {
                    $bundle = Bundle::find($item['bundle_id']);
                    if ($bundle) {
                        $item['stock'] = $bundle->is_out_of_stock ? 0 : 100;
                    }
                }
            }
        }

        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        $subtotal = $cartData['subtotal'];
        $savings = $cartData['savings'];
        
        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $theme = $tenant ? $tenant->theme : 'template_1';
        $view = ($theme === 'template_2') ? 'template_2.cart' : 'template_1.cart';
        
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($cartTotalBeforeTax * ($taxRate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;

        $minOrderValue = $tenant ? ($tenant->min_order_value ?? 0) : 0;
        return view($view, compact('cart', 'total', 'subtotal', 'savings', 'taxAmount', 'taxRate', 'taxName', 'cartTotalBeforeTax', 'minOrderValue'));
    }

    /**
     * Display the shopping cart for V4 theme.
     */
    public function ajmalCart()
    {
        if (Auth::check()) {
            self::syncSession(Auth::id());
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
            $cart = array_reverse($cart, true); 
            
            foreach($cart as $key => &$item) {
                $item['stock'] = 100; 
                
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if($product) {                        $item['coupon'] = $this->getActiveCoupon($product);
                        
                        if(isset($item['size']) && $item['size']) {
                            $variant = $product->variants->where('size', $item['size'])->first();
                            $item['stock'] = $variant ? $variant->stock : 0;
                        } else {
                            $item['stock'] = $product->variants->sum('stock');
                        }
                        
                        if ($product->continue_selling_when_out_of_stock) {
                            $item['stock'] = 999;
                        }
                    }
                } elseif (isset($item['type']) && $item['type'] == 'bundle' && isset($item['bundle_id'])) {
                    $bundle = Bundle::find($item['bundle_id']);
                    if ($bundle) {
                        $item['stock'] = $bundle->is_out_of_stock ? 0 : 100;
                    }
                }
            }
        }

        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        $subtotal = $cartData['subtotal'];
        $savings = $cartData['savings'];
        
        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($cartTotalBeforeTax * ($taxRate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;
        
        $minOrderValue = $tenant ? ($tenant->min_order_value ?? 0) : 0;
        return view('v4.cart', compact('cart', 'total', 'subtotal', 'savings', 'taxAmount', 'taxRate', 'taxName', 'cartTotalBeforeTax', 'minOrderValue'));
    }

    /**
     * Display the shopping cart for V5 theme.
     */
    public function afnanCart()
    {
        if (Auth::check()) {
            self::syncSession(Auth::id());
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
            $cart = array_reverse($cart, true); 
            
            foreach($cart as $key => &$item) {
                $item['stock'] = 100; 
                
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if($product) {
                        $item['coupon'] = $this->getActiveCoupon($product);
                        
                        if(isset($item['size']) && $item['size']) {
                            $variant = $product->variants->where('size', $item['size'])->first();
                            $item['stock'] = $variant ? $variant->stock : 0;
                        } else {
                            $item['stock'] = $product->variants->sum('stock');
                        }
                        
                        if ($product->continue_selling_when_out_of_stock) {
                            $item['stock'] = 999;
                        }
                    }
                } elseif (isset($item['type']) && $item['type'] == 'bundle' && isset($item['bundle_id'])) {
                    $bundle = Bundle::find($item['bundle_id']);
                    if ($bundle) {
                        $item['stock'] = $bundle->is_out_of_stock ? 0 : 100;
                    }
                }
            }
        }

        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        $subtotal = $cartData['subtotal'];
        $savings = $cartData['savings'];
        
        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($cartTotalBeforeTax * ($taxRate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;
        
        $minOrderValue = $tenant ? ($tenant->min_order_value ?? 0) : 0;
        return view('v5.cart', compact('cart', 'total', 'subtotal', 'savings', 'taxAmount', 'taxRate', 'taxName', 'cartTotalBeforeTax', 'minOrderValue'));
    }

    private function calculateTotal(&$cart)
    {
        return \App\Services\CartService::calculateTotal($cart);
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request)
    {
        // Handle multiple products (for splitting bundles)
        if ($request->has('multi_products')) {
            $cart = Auth::check() ? $this->getCartFromDb() : session()->get('cart', []);
            
            foreach ($request->multi_products as $itemData) {
                $pId = $itemData['id'];
                $qty = $itemData['quantity'] ?? 1;
                $size = $itemData['size'] ?? null;
                $variantId = $itemData['variant_id'] ?? null;

                $product = Product::find($pId);
                if (!$product) continue;

                $price = $product->starting_price;
                if ($size) {
                    $v = $product->variants()->where('size', $size)->first();
                    if ($v) { $price = $v->price; $variantId = $v->id; }
                }

                $cartKey = $pId . ($size ? '-' . $size : '');

                if (Auth::check()) {
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $pId)
                        ->where('size', $size)
                        ->first();
                    if ($cartItem) {
                        $cartItem->quantity += $qty;
                        $cartItem->save();
                    } else {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'product_id' => $pId,
                            'quantity' => $qty,
                            'size' => $size,
                            'product_variant_id' => $variantId
                        ]);
                    }
                } else {
                    if (isset($cart[$cartKey])) {
                        $cart[$cartKey]['quantity'] += $qty;
                    } else {
                        $cart[$cartKey] = [
                            "product_id" => $product->id,
                            "variant_id" => $variantId,
                            "name" => $product->title,
                            "quantity" => $qty,
                            "price" => $price,
                            "image" => $product->main_image_url,
                            "size" => $size,
                            "type" => "product"
                        ];
                    }
                }
            }
            
            if (!Auth::check()) {
                session()->put('cart', $cart);
            } else {
                $cart = $this->getCartFromDb();
            }

            $count = 0;
            $cartItemsMap = [];
            $cartImages = [];
            foreach($cart as $key => $item) {
                $count += $item['quantity'];
                $cartItemsMap[$key] = $item['quantity'];
                if (isset($item['image']) && $item['image']) $cartImages[] = $item['image'];
            }
            $cartImages = array_slice(array_unique($cartImages), 0, 3);
            $cartData = $this->calculateTotal($cart);

            return response()->json([
                'success' => true, 
                'message' => 'Collection added to Bag!',
                'cartCount' => $count,
                'cartTotal' => $cartData['total'],
                'cartItemsMap' => $cartItemsMap,
                'cartImages' => $cartImages
            ]);
        }

        $id = $request->id;
        $quantity = $request->quantity ?? 1;
        $size = $request->size;
        
        if($request->type == 'bundle') {
            $bundle = Bundle::find($id);
            if(!$bundle) return response()->json(['success' => false, 'message' => 'Bundle not found!'], 404);
            if($bundle->is_out_of_stock) return response()->json(['success' => false, 'message' => 'This bundle is currently out of stock.'], 400);
            
            $cartKey = 'bundle-' . $id;
            
            // Check purchase quantity limits for Bundle
            $currentQty = 0;
            if (Auth::check()) {
                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('bundle_id', $id)
                    ->first();
                if ($cartItem) {
                    $currentQty = $cartItem->quantity;
                }
            } else {
                $cart = session()->get('cart', []);
                if (isset($cart[$cartKey])) {
                    $currentQty = $cart[$cartKey]['quantity'];
                }
            }

            $targetQty = $currentQty + $quantity;

            // Auto-boost initial addition to minimum required quantity
            if ($currentQty == 0 && $bundle->min_order_qty && $targetQty < $bundle->min_order_qty) {
                $quantity = $bundle->min_order_qty;
                $targetQty = $bundle->min_order_qty;
            }

            if ($bundle->min_order_qty && $targetQty < $bundle->min_order_qty) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum order quantity for {$bundle->title} is {$bundle->min_order_qty}."
                ], 400);
            }

            if ($bundle->max_order_qty && $targetQty > $bundle->max_order_qty) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum order quantity for {$bundle->title} is {$bundle->max_order_qty}."
                ], 400);
            }

             if (Auth::check()) {
                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('bundle_id', $id)
                    ->first();
                    
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    Cart::create([
                        'user_id' => Auth::id(),
                        'bundle_id' => $id,
                        'quantity' => $quantity,
                        'product_id' => null
                    ]);
                }
                $cart = $this->getCartFromDb();
             } else {
                $cart = session()->get('cart', []);
                if(isset($cart[$cartKey])) {
                    $cart[$cartKey]['quantity'] += $quantity;
                } else {
                    $bundleImage = $bundle->image ? \Illuminate\Support\Facades\Storage::url($bundle->image) : null;
                    if (!$bundleImage && $bundle->type == 'pack') {
                        $firstProd = $bundle->products->first();
                        $bundleImage = $firstProd ? $firstProd->main_image_url : null;
                    }
                    
                    $cart[$cartKey] = [
                        "bundle_id" => $bundle->id,
                        "name" => $bundle->title,
                        "quantity" => $quantity,
                        "price" => $bundle->total_price,
                        "image" => $bundleImage,
                        "size" => null,
                        "type" => "bundle",
                        "min_order_qty" => $bundle->min_order_qty,
                        "max_order_qty" => $bundle->max_order_qty
                    ];
                }
                session()->put('cart', $cart);
             }
             
            // Calculate count
            $count = 0;
            $cartItemsMap = [];
            $cartImages = [];
            foreach($cart as $key => $item) {
                $count += $item['quantity'];
                $cartItemsMap[$key] = $item['quantity'];
                if (isset($item['image']) && $item['image']) $cartImages[] = $item['image'];
            }
            $cartImages = array_slice(array_unique($cartImages), 0, 3);
            
            return response()->json([
                'success' => true, 
                'message' => 'Bundle added to cart!',
                'cartCount' => $count,
                'cartItemsMap' => $cartItemsMap,
                'cartImages' => $cartImages
            ]);
        } 
        
        // Product Logic
        $product = Product::find($id);
        
        if(!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found!'], 404);
        }

        // Check purchase quantity limits
        $cartKey = $id . ($size ? '-' . $size : '');
        $currentQty = 0;
        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->where('size', $size)
                ->first();
            if ($cartItem) {
                $currentQty = $cartItem->quantity;
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$cartKey])) {
                $currentQty = $cart[$cartKey]['quantity'];
            }
        }

        $targetQty = $currentQty + $quantity;

        // Auto-boost initial addition to minimum required quantity to prevent blocking UX
        if ($currentQty == 0 && $product->min_order_qty && $targetQty < $product->min_order_qty) {
            $quantity = $product->min_order_qty;
            $targetQty = $product->min_order_qty;
        }

        if ($product->min_order_qty && $targetQty < $product->min_order_qty) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order quantity for {$product->title} is {$product->min_order_qty}."
            ], 400);
        }

        if ($product->max_order_qty && $targetQty > $product->max_order_qty) {
            return response()->json([
                'success' => false,
                'message' => "Maximum order quantity for {$product->title} is {$product->max_order_qty}."
            ], 400);
        }
        
        // Stock Validation
        if (!$product->continue_selling_when_out_of_stock) {
            $availableStock = 0;
            if ($size) {
                $variant = $product->variants()->where('size', $size)->first();
                if ($variant) $availableStock = $variant->stock;
            } else {
                $availableStock = $product->variants()->sum('stock');
            }

            if ($targetQty > $availableStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot add more than available stock."
                ], 400);
            }
        }
        
        // Price Logic
        $price = $product->starting_price;
        if($size) {
            $variant = $product->variants()->where('size', $size)->first();
            if($variant) $price = $variant->price;
        }

        // Cart Key for Session
        $cartKey = $id . ($size ? '-' . $size : '');

        if (Auth::check()) {
            // DB Logic
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $id)
                ->where('size', $size)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $id,
                    'quantity' => $quantity,
                    'size' => $size,
                    'product_variant_id' => $request->variant_id
                ]);
            }
            
            // Re-fetch full cart for count/consistency
            $cart = $this->getCartFromDb();
            
        } else {
            $cart = session()->get('cart', []);
            
            if(isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $quantity;
            } else {
                $cart[$cartKey] = [
                    "product_id" => $id,
                    "variant_id" => $request->variant_id,
                    "name" => $product->title,
                    "quantity" => $quantity,
                    "price" => $price,
                    "image" => $product->main_image_url,
                    "size" => $size,
                    "type" => "product",
                    "min_order_qty" => $product->min_order_qty,
                    "max_order_qty" => $product->max_order_qty
                ];
            }
            session()->put('cart', $cart);
        }
        
        // Calculate count
        $count = 0;
        $cartItemsMap = [];
        $cartImages = [];
        $cartData = $this->calculateTotal($cart);
        foreach($cart as $key => $item) {
            $count += $item['quantity'];
            $cartItemsMap[$key] = $item['quantity'];
            if (isset($item['image']) && $item['image']) $cartImages[] = $item['image'];
        }
        $cartImages = array_slice(array_unique($cartImages), 0, 3);
        
        return response()->json([
            'success' => true, 
            'message' => 'Product added to cart!',
            'cartCount' => $count,
            'cartTotal' => $cartData['total'],
            'cartItemsMap' => $cartItemsMap,
            'cartImages' => $cartImages
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        if($request->id && $request->quantity) {
            // Check purchase quantity limits
            $productId = null;
            $bundleId = null;
            if (!str_starts_with($request->id, 'bundle-')) {
                $parts = explode('-', $request->id, 2);
                $productId = $parts[0];
            } else {
                $bundleId = str_replace('bundle-', '', $request->id);
            }

            if ($bundleId) {
                $bundle = Bundle::find($bundleId);
                if ($bundle) {
                    if ($bundle->min_order_qty && $request->quantity < $bundle->min_order_qty) {
                        return response()->json([
                            'success' => false,
                            'message' => "Minimum order quantity for {$bundle->title} is {$bundle->min_order_qty}."
                        ], 400);
                    }
                    if ($bundle->max_order_qty && $request->quantity > $bundle->max_order_qty) {
                        return response()->json([
                            'success' => false,
                            'message' => "Maximum order quantity for {$bundle->title} is {$bundle->max_order_qty}."
                        ], 400);
                    }
                }
            }

            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    if ($product->min_order_qty && $request->quantity < $product->min_order_qty) {
                        return response()->json([
                            'success' => false,
                            'message' => "Minimum order quantity for {$product->title} is {$product->min_order_qty}."
                        ], 400);
                    }
                    if ($product->max_order_qty && $request->quantity > $product->max_order_qty) {
                        return response()->json([
                            'success' => false,
                            'message' => "Maximum order quantity for {$product->title} is {$product->max_order_qty}."
                        ], 400);
                    }
                    
                    // Stock Validation
                    if (!$product->continue_selling_when_out_of_stock) {
                        $availableStock = 0;
                        $parts = explode('-', $request->id, 2);
                        $size = isset($parts[1]) ? $parts[1] : null;
                        
                        if ($size) {
                            $variant = $product->variants()->where('size', $size)->first();
                            if ($variant) $availableStock = $variant->stock;
                        } else {
                            $availableStock = $product->variants()->sum('stock');
                        }

                        if ($request->quantity > $availableStock) {
                            return response()->json([
                                'success' => false,
                                'message' => "Cannot add more than available stock."
                            ], 400);
                        }
                    }
                }
            }

            $cart = [];
             if (Auth::check()) {
                $cartItem = null;
                
                if (str_starts_with($request->id, 'bundle-')) {
                    $bundleId = str_replace('bundle-', '', $request->id);
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('bundle_id', $bundleId)
                        ->first();
                } else {
                    $parts = explode('-', $request->id, 2);
                    $productId = $parts[0];
                    $size = isset($parts[1]) ? $parts[1] : null;

                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $productId)
                        ->where('size', $size)
                        ->first();
                }
                
                if ($cartItem) {
                    $cartItem->quantity = $request->quantity;
                    $cartItem->save();
                }
                
                $cart = $this->getCartFromDb();

            } else {
                $cart = session()->get('cart', []);
                $cartKey = $request->id;
                
                if(isset($cart[$cartKey])) {
                    $cart[$cartKey]['quantity'] = $request->quantity;
                    session()->put('cart', $cart);
                }
            }
            
            $itemTotal = 0;
            if(isset($cart[$request->id])) {
                $itemTotal = $cart[$request->id]['price'] * $cart[$request->id]['quantity'];
            }

            $cartData = $this->calculateTotal($cart);
            $cartTotalBeforeTax = $cartData['total'];
            $count = 0;
            $cartItemsMap = [];
            $cartImages = [];
            foreach($cart as $key => $item) {
                $count += $item['quantity'];
                $cartItemsMap[$key] = $item['quantity'];
                if (isset($item['image']) && $item['image']) $cartImages[] = $item['image'];
            }
            $cartImages = array_slice(array_unique($cartImages), 0, 3);
            
            $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
            $tenant = \App\Models\Tenant::find($tenantId);
            $taxAmount = 0.00;
            if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
                $taxAmount = round($cartTotalBeforeTax * ($tenant->tax_rate / 100), 2);
            }
            $total = $cartTotalBeforeTax + $taxAmount;
            
            return response()->json([
                'success' => true, 
                'itemTotal' => $itemTotal,
                'cartTotal' => $total,
                'cartTotalBeforeTax' => $cartTotalBeforeTax,
                'taxAmount' => $taxAmount,
                'cartCount' => $count,
                'savings' => $cartData['savings'],
                'subtotal' => $cartData['subtotal'],
                'cartItemsMap' => $cartItemsMap,
                'cartImages' => $cartImages
            ]);
        }
        
        return response()->json(['success' => false], 400);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = [];
            if (Auth::check()) {
                 if (str_starts_with($request->id, 'bundle-')) {
                    $bundleId = str_replace('bundle-', '', $request->id);
                    Cart::where('user_id', Auth::id())
                        ->where('bundle_id', $bundleId)
                        ->delete();
                } else {
                    $parts = explode('-', $request->id, 2);
                    $productId = $parts[0];
                    $size = isset($parts[1]) ? $parts[1] : null;

                    Cart::where('user_id', Auth::id())
                        ->where('product_id', $productId)
                        ->where('size', $size)
                        ->delete();
                }
                $cart = $this->getCartFromDb();

            } else {
                $cart = session()->get('cart', []);
                if(isset($cart[$request->id])) {
                    unset($cart[$request->id]);
                    session()->put('cart', $cart);
                }
            }
            
            $cartData = $this->calculateTotal($cart);
            $cartTotalBeforeTax = $cartData['total'];
            $count = 0;
            $cartItemsMap = [];
            $cartImages = [];
            if($cart) {
                foreach($cart as $key => $item) {
                    $count += $item['quantity'];
                    $cartItemsMap[$key] = $item['quantity'];
                    if (isset($item['image']) && $item['image']) {
                        $cartImages[] = $item['image'];
                    }
                }
            }
            $cartImages = array_slice(array_unique($cartImages), 0, 3);
            
            $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
            $tenant = \App\Models\Tenant::find($tenantId);
            $taxAmount = 0.00;
            if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
                $taxAmount = round($cartTotalBeforeTax * ($tenant->tax_rate / 100), 2);
            }
            $total = $cartTotalBeforeTax + $taxAmount;

            return response()->json([
                'success' => true,
                'cartTotal' => $total,
                'cartTotalBeforeTax' => $cartTotalBeforeTax,
                'taxAmount' => $taxAmount,
                'cartCount' => $count,
                'isEmpty' => empty($cart),
                'savings' => $cartData['savings'],
                'subtotal' => $cartData['subtotal'],
                'cartItemsMap' => $cartItemsMap,
                'cartImages' => $cartImages
            ]);
        }
        
        return response()->json(['success' => false], 400);
    }
    
    public static function syncSession($userId)
    {
        $sessionCart = session()->get('cart', []);
        
        if (empty($sessionCart)) return;
        
        foreach ($sessionCart as $key => $details) {
            $quantity = $details['quantity'];
            
            if (isset($details['bundle_id'])) {
                $bundleId = $details['bundle_id'];
                $cartItem = Cart::where('user_id', $userId)->where('bundle_id', $bundleId)->first();
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    Cart::create([
                        'user_id' => $userId,
                        'bundle_id' => $bundleId,
                        'quantity' => $quantity
                    ]);
                }
            } else {
                $productId = $details['product_id'];
                $size = $details['size'];
                
                $cartItem = Cart::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->where('size', $size)
                    ->first();
                    
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    Cart::create([
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'size' => $size
                    ]);
                }
            }
        }
        
        session()->forget('cart');
    }
    
    private function getCartFromDb()
    {
        $dbItems = Cart::where('user_id', Auth::id())
            ->with(['product.variants', 'product.images', 'product.discounts', 'bundle'])
            ->latest()
            ->get();
        $cart = [];
        
        foreach ($dbItems as $item) {
            
            if ($item->bundle_id && $item->bundle) {
                $key = 'bundle-' . $item->bundle_id;
                
                $bundleImage = $item->bundle->image ? \Illuminate\Support\Facades\Storage::url($item->bundle->image) : null;
                if (!$bundleImage && $item->bundle->type == 'pack') {
                    $firstProd = $item->bundle->products->first();
                    $bundleImage = $firstProd ? $firstProd->main_image_url : null;
                }

                if ($item->bundle->is_out_of_stock) {
                    continue;
                }

                $cart[$key] = [
                    "bundle_id" => $item->bundle_id,
                    "name" => $item->bundle->title,
                    "quantity" => $item->quantity,
                    "price" => $item->bundle->total_price,
                    "image" => $bundleImage,
                    "size" => null,
                    "type" => "bundle",
                    "stock" => 100,
                    "min_order_qty" => $item->bundle->min_order_qty,
                    "max_order_qty" => $item->bundle->max_order_qty
                ];
            }
            elseif ($item->product_id && $item->product) {
                $key = $item->product_id . ($item->size ? '-' . $item->size : '');
                
                $price = $item->product->starting_price;
                $stock = $item->product->variants->sum('stock');

                if ($item->size) {
                    $variant = $item->product->variants->where('size', $item->size)->first();
                    if ($variant) {
                        $price = $variant->price;
                        $stock = $variant->stock;
                    }
                }
                
                if ($item->product->continue_selling_when_out_of_stock) {
                    $stock = 999;
                }
                
                if ($stock <= 0) {
                    continue;
                }
                
                $cart[$key] = [
                    "product_id" => $item->product_id,
                    "variant_id" => $item->product_variant_id,
                    "name" => $item->product->title,
                    "quantity" => $item->quantity,
                    "price" => $price,
                    "image" => $item->product->main_image_url,
                    "size" => $item->size,
                    "type" => "product",
                    "coupon" => $this->getActiveCoupon($item->product),
                    "stock" => $stock,
                    "min_order_qty" => $item->product->min_order_qty,
                    "max_order_qty" => $item->product->max_order_qty
                ];
            }
        }
        
        return $cart;
    }

    /**
     * Get the JSON representation of the cart state.
     */
    public function state(Request $request)
    {
        if (Auth::check()) {
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
        }
        
        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        
        $count = 0;
        $cartItemsMap = [];
        $cartImages = [];
        if($cart) {
            foreach($cart as $key => $item) {
                $count += $item['quantity'];
                $cartItemsMap[$key] = $item['quantity'];
                if (isset($item['image']) && $item['image']) $cartImages[] = $item['image'];
            }
        }
        $cartImages = array_slice(array_unique($cartImages), 0, 3);
        
        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxAmount = round($cartTotalBeforeTax * ($tenant->tax_rate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;

        return response()->json([
            'success' => true,
            'cartCount' => $count,
            'cartTotal' => $total,
            'cartItemsMap' => $cartItemsMap,
            'cartImages' => $cartImages
        ]);
    }

    /**
     * Reorder all items from a previous order.
     */
    public function reorder(\App\Models\Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            return back()->with('error', 'You are not authorized to reorder this order!');
        }

        // Get current cart (keep existing items)
        if (Auth::check()) {
            self::syncSession(Auth::id());
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
        }

        // Loop through order items and add to cart
        foreach ($order->items as $orderItem) {
            $quantity = $orderItem->quantity;
            $size = $orderItem->size;

            if ($orderItem->product_id) {
                // Product item
                $product = Product::find($orderItem->product_id);
                if (!$product) continue;

                $price = $product->starting_price;
                $variantId = null;
                if ($size) {
                    $v = $product->variants()->where('size', $size)->first();
                    if ($v) { 
                        $price = $v->price; 
                        $variantId = $v->id; 
                    }
                }

                $cartKey = $product->id . ($size ? '-' . $size : '');

                if (Auth::check()) {
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $product->id)
                        ->where('size', $size)
                        ->first();
                    if ($cartItem) {
                        $cartItem->quantity += $quantity;
                        $cartItem->save();
                    } else {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'size' => $size,
                            'product_variant_id' => $variantId
                        ]);
                    }
                } else {
                    $cart[$cartKey] = [
                        "product_id" => $product->id,
                        "variant_id" => $variantId,
                        "name" => $product->title,
                        "quantity" => $quantity,
                        "price" => $price,
                        "image" => $product->main_image_url,
                        "size" => $size,
                        "type" => "product"
                    ];
                }
            } elseif ($orderItem->bundle_id) {
                // Bundle item
                $bundle = Bundle::find($orderItem->bundle_id);
                if (!$bundle) continue;

                $cartKey = 'bundle-' . $bundle->id;

                if (Auth::check()) {
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('bundle_id', $bundle->id)
                        ->first();
                        
                    if ($cartItem) {
                        $cartItem->quantity += $quantity;
                        $cartItem->save();
                    } else {
                        Cart::create([
                            'user_id' => Auth::id(),
                            'bundle_id' => $bundle->id,
                            'quantity' => $quantity,
                            'product_id' => null
                        ]);
                    }
                } else {
                    $bundleImage = $bundle->image ? \Illuminate\Support\Facades\Storage::url($bundle->image) : null;
                    if (!$bundleImage && $bundle->type == 'pack') {
                        $firstProd = $bundle->products->first();
                        $bundleImage = $firstProd ? $firstProd->main_image_url : null;
                    }
                    
                    $cart[$cartKey] = [
                        "bundle_id" => $bundle->id,
                        "name" => $bundle->title,
                        "quantity" => $quantity,
                        "price" => $bundle->total_price,
                        "image" => $bundleImage,
                        "size" => null,
                        "type" => "bundle"
                    ];
                }
            }
        }

        if (!Auth::check()) {
            session()->put('cart', $cart);
        }

        // Determine correct checkout route based on order tenant!
        $tenantId = $order->tenant_id;
        $tenant = \App\Models\Tenant::find($tenantId);
        $theme = $tenant ? $tenant->theme : 'template_1';
        
        $checkoutRoute = match(true) {
            $theme === 'template_2' || $theme === 'v3' => 'v3.checkout',
            $theme === 'v4' => 'v4.checkout',
            $theme === 'v5' => 'v5.checkout',
            default => 'v1.checkout'
        };

        return redirect()->route($checkoutRoute)->with('success', 'Order items added to cart!');
    }

    public function fetch(Request $request)
    {
        if (Auth::check()) {
            $cart = $this->getCartFromDb();
        } else {
            $cart = session()->get('cart', []);
            $cart = array_reverse($cart, true); 
            
            foreach($cart as $key => &$item) {
                $item['stock'] = 100; 
                
                if(isset($item['type']) && $item['type'] == 'product' && isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if($product) {
                        $item['coupon'] = $this->getActiveCoupon($product);
                        
                        $stock = 0;
                        if(isset($item['size']) && $item['size']) {
                            $variant = $product->variants->where('size', $item['size'])->first();
                            $stock = $variant ? $variant->stock : 0;
                        } else {
                            $stock = $product->variants->sum('stock');
                        }
                        
                        if ($product->continue_selling_when_out_of_stock) {
                            $stock = 999;
                        }

                        $item['stock'] = $stock;

                        if ($stock <= 0) {
                            unset($cart[$key]);
                        }
                    } else {
                        unset($cart[$key]);
                    }
                } elseif (isset($item['type']) && $item['type'] == 'bundle' && isset($item['bundle_id'])) {
                    $bundle = Bundle::find($item['bundle_id']);
                    if ($bundle && !$bundle->is_out_of_stock) {
                        $item['stock'] = 100;
                    } else {
                        unset($cart[$key]);
                    }
                }
            }
        }

        $cartData = $this->calculateTotal($cart);
        $cartTotalBeforeTax = $cartData['total'];
        $subtotal = $cartData['subtotal'];
        $savings = $cartData['savings'];

        $tenantId = session('active_tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : null) ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($cartTotalBeforeTax * ($tenant->tax_rate / 100), 2);
        }
        $total = $cartTotalBeforeTax + $taxAmount;

        $theme = $request->theme ?? 'template_1';
        if ($theme == 'v4') {
            $view = 'v4.partials.cart_drawer_items';
        } elseif ($theme == 'velvet') {
            $view = 'velvet.partials.cart_drawer_items';
        } elseif ($theme == 'template_2') {
            $view = 'template_2.partials.cart_drawer_items';
        } else {
            $view = 'template_1.partials.cart_drawer_items';
        }

        $minOrderValue = $tenant ? ($tenant->min_order_value ?? 0) : 0;
        return view($view, compact('cart', 'total', 'subtotal', 'savings', 'taxAmount', 'taxRate', 'taxName', 'cartTotalBeforeTax', 'minOrderValue'))->render();
    }

    private function getActiveCoupon($product)
    {
        return \App\Services\CartService::getActiveCoupon($product);
    }
}
