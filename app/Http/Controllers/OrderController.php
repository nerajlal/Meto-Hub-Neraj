<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\CheckoutCartService;
use App\Http\Controllers\CartController;
use App\Services\CartService;
use App\Services\WhatsAppService;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Map alternative names to standard field names if they exist
        if (!$request->has('name') && ($request->has('first_name') || $request->has('last_name'))) {
            $request->merge([
                'name' => trim($request->input('first_name', '') . ' ' . $request->input('last_name', ''))
            ]);
        }
        if (!$request->has('pincode') && $request->has('zip')) {
            $request->merge([
                'pincode' => $request->input('zip')
            ]);
        }
        if (!$request->has('payment_method')) {
            $request->merge([
                'payment_method' => $request->input('payment') ?? 'cod'
            ]);
        }

        // 1. Validate Input
        $tenantId = $request->attributes->get('resolved_tenant_id') ?? session('active_tenant_id');
        $tenant = \App\Models\Tenant::findOrFail($tenantId);
        $cf = $tenant ? ($tenant->checkout_fields ?? []) : [];
        
        $rules = [
            'payment_method' => ['required', 'string', \Illuminate\Validation\Rule::in(app(\App\Services\CheckoutPaymentPolicy::class)->availableMethods($tenant))],
        ];

        // Delivery schedule validation
        $deliveryDateEnabled = (bool) ($cf['delivery_date']['enabled'] ?? false);
        if ($deliveryDateEnabled) {
            $rules['delivery_date'] = 'required|date';
        }
        
        $deliveryTimeSlotEnabled = (bool) ($cf['delivery_time_slot']['enabled'] ?? false);
        if ($deliveryTimeSlotEnabled) {
            $rules['delivery_time_slot'] = 'required|string';
        }

        // Standard fields validation based on configuration
        $standardFields = ['name', 'email', 'phone', 'address', 'city', 'state', 'pincode'];
        foreach ($standardFields as $f) {
            $isEnabled = !isset($cf[$f]) || (isset($cf[$f]['enabled']) && $cf[$f]['enabled']);
            $isRequired = !isset($cf[$f]) || (isset($cf[$f]['required']) && $cf[$f]['required']);
            
            if ($isEnabled && $isRequired) {
                $rules[$f] = ($f === 'email') ? 'required|email' : 'required';
            } elseif ($isEnabled && $f === 'email') {
                $rules[$f] = 'nullable|email';
            }
        }

        if (isset($cf['alternate_phone']['enabled']) && $cf['alternate_phone']['enabled'] && isset($cf['alternate_phone']['required']) && $cf['alternate_phone']['required']) {
            $rules['alternate_phone'] = 'required';
        }
        if (isset($cf['company_name']['enabled']) && $cf['company_name']['enabled'] && isset($cf['company_name']['required']) && $cf['company_name']['required']) {
            $rules['company_name'] = 'required';
        }
        if (isset($cf['gst_number']['enabled']) && $cf['gst_number']['enabled'] && isset($cf['gst_number']['required']) && $cf['gst_number']['required']) {
            $rules['gst_number'] = 'required';
        }
        if (isset($cf['landmark']['enabled']) && $cf['landmark']['enabled'] && isset($cf['landmark']['required']) && $cf['landmark']['required']) {
            $rules['landmark'] = 'required';
        }
        if (isset($cf['order_notes']['enabled']) && $cf['order_notes']['enabled'] && isset($cf['order_notes']['required']) && $cf['order_notes']['required']) {
            $rules['order_notes'] = 'required';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();
            if (Auth::check()) {
                \App\Models\User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            }
            // 2. Get Cart Data
        // Ideally we reuse CartController logic, but for now we'll fetch manually to be safe or call the helper
        // 2. Get Cart Data
        $cart = [];
        if(Auth::check()) {
            $items = \App\Models\Cart::where('user_id', Auth::id())
                ->with(['product.discounts', 'product.images', 'product.variants', 'bundle'])
                ->get();

            foreach($items as $item) {
                 if($item->product_id && $item->product) {
                    $cart[$item->product_id . '-' . $item->size] = [
                        "name" => $item->product->title,
                        "quantity" => $item->quantity,
                        "price" => $item->product->starting_price,
                        "original_price" => $item->product->starting_price,
                        "product_id" => $item->product_id,
                        "bundle_id" => null,
                        "size" => $item->size,
                        "variant_id" => $item->product_variant_id,
                        "type" => "product",
                        "coupon" => CartService::getActiveCoupon($item->product)
                    ];
                } elseif ($item->bundle_id && $item->bundle) {
                    $cart['bundle-' . $item->bundle_id] = [
                        "name" => $item->bundle->title,
                        "quantity" => $item->quantity,
                        "price" => $item->bundle->total_price,
                        "image" => \Illuminate\Support\Facades\Storage::url($item->bundle->image),
                        "product_id" => null,
                        "bundle_id" => $item->bundle_id,
                        "size" => null,
                        "type" => "bundle"
                    ];
                }
            }
        } else {
            $cart = session()->get('cart', []);
        }

        if(empty($cart)) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Your cart is empty'], 400);
        }

        $cart = app(CheckoutCartService::class)->prepare($cart);

        // 3. Calculate Totals
        $cartData = CartService::calculateTotal($cart);
        $subtotal = $cartData['total'];
        $savings = min($cartData['subtotal'], $cartData['savings']);
        if ($subtotal < ($tenant->min_order_value ?? 0)) {
            throw ValidationException::withMessages(['cart' => 'Your cart does not meet the minimum order value.']);
        }
        $shipping = 0; // Free shipping
        
        $tenantId = session('active_tenant_id') ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $taxAmount = 0.00;
        $taxRate = null;
        $taxName = null;
        if ($tenant && $tenant->tax_name && $tenant->tax_rate > 0) {
            $taxRate = $tenant->tax_rate;
            $taxName = $tenant->tax_name;
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
        }
        
        $total = $subtotal + $shipping + $taxAmount;

        // 3.5 Generate Custom Order Number
        // Format: NR-{Date 2digit}-{ProductID}-{Random 2digit 2alpha}
        $firstItem = reset($cart);
        $pid = $firstItem['product_id'] ?? $firstItem['bundle_id'] ?? 'X';
        $dateCtx = now()->format('d');
        
        $rDigits = rand(10, 99); // 2 Digits
        $rLetters = strtoupper(Str::random(2)); // Might be alphanumeric, let's ensure letters
        // Strictly letters
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rLetters = substr(str_shuffle($pool), 0, 2);
        
        $randomMix = str_shuffle($rDigits . $rLetters);
        
        $orderNumber = "NR-{$dateCtx}-{$pid}-{$randomMix}";

        // Split name for customer_name
        $customerName = $request->name ?? '';
        $nameParts = explode(' ', $customerName);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

        // Extract custom checkout data
        $customCheckoutData = [];
        if (isset($cf['alternate_phone']['enabled']) && $cf['alternate_phone']['enabled'] && $request->filled('alternate_phone')) {
            $customCheckoutData['alternate_phone'] = $request->alternate_phone;
        }
        if (isset($cf['company_name']['enabled']) && $cf['company_name']['enabled'] && $request->filled('company_name')) {
            $customCheckoutData['company_name'] = $request->company_name;
        }
        if (isset($cf['gst_number']['enabled']) && $cf['gst_number']['enabled'] && $request->filled('gst_number')) {
            $customCheckoutData['gst_number'] = $request->gst_number;
        }
        if (isset($cf['landmark']['enabled']) && $cf['landmark']['enabled'] && $request->filled('landmark')) {
            $customCheckoutData['landmark'] = $request->landmark;
        }
        if (isset($cf['order_notes']['enabled']) && $cf['order_notes']['enabled'] && $request->filled('order_notes')) {
            $customCheckoutData['order_notes'] = $request->order_notes;
        }

        // 4. Create Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => $orderNumber,
            'status' => 'pending',
            'payment_method' => $request->payment_method ?? 'cod',
            'payment_status' => 'pending',
            'subtotal' => $cartData['subtotal'],
            'discount_amount' => $savings,
            'shipping_cost' => $shipping,
            'total_amount' => $total,
            'tax_name' => $taxName,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'customer_name' => $customerName,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'shipping_address' => [
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->pincode,
            ],
            'custom_checkout_data' => $customCheckoutData,
            'delivery_date' => $request->delivery_date,
            'delivery_time_slot' => $request->delivery_time_slot,
            'notes' => $request->order_notes ?? $request->notes,
            'placed_at' => now(),
            'tenant_id' => $tenantId,
        ]);

        // 4.1 Save User Address if not exists or Update Phone on existing default
        if(Auth::check()) {
            $defaultAddress = \App\Models\UserAddress::where('user_id', Auth::id())->where('is_default', true)->first();

            if ($defaultAddress) {
                // Ensure latest phone is saved
                $defaultAddress->update(['phone' => $request->phone]);
            } else {
                $hasAddress = \App\Models\UserAddress::where('user_id', Auth::id())->exists();
                if(!$hasAddress) {
                    \App\Models\UserAddress::create([
                        'user_id' => Auth::id(),
                        'phone' => $request->phone ?? '',
                        'address_line1' => $request->address ?? '',
                        'address_line2' => $request->apartment ?? null,
                        'city' => $request->city ?? '',
                        'state' => $request->state ?? '',
                        'zip' => $request->pincode ?? '',
                        'country' => 'India',
                        'is_default' => true,
                    ]);
                }
            }
        }

        // 5. Create Order Items & Update Stock
        foreach($cart as $key => $details) {
            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $details['product_id'] ?? null,
                'bundle_id' => $details['bundle_id'] ?? null,
                'name' => $details['name'],
                'quantity' => $details['quantity'],
                'price' => $details['price'], 
                'total' => $details['price'] * $details['quantity'],
                'size' => $details['size'] ?? null,
                'type' => $details['type'] ?? 'product',
                'options' => [
                    'coupon_code' => isset($details['coupon']) ? $details['coupon']->code : (isset($details['coupon_code']) ? $details['coupon_code'] : null),
                    'saved_amount' => $details['line_savings'] ?? 0,
                    'original_price' => $details['original_price'] ?? null
                ]
            ]);

        }

        // 6. Increment Discount Usage
        // Check for applied coupons in the cart items we just processed
        $usedDiscountIds = [];
        foreach($cart as $item) {
            $code = isset($item['coupon']) ? $item['coupon']->code : (isset($item['coupon_code']) ? $item['coupon_code'] : null);
            if($code) {
                 $usedDiscountIds[$code] = true; 
            }
        }

        if(!empty($usedDiscountIds)) {
            \App\Models\Discount::whereIn('code', array_keys($usedDiscountIds))->increment('uses_count');
        }

        // 7. Clear Cart
        if(Auth::check()) {
            \App\Models\Cart::where('user_id', Auth::id())->delete();
        }
        app(\App\Services\MerchantOrderNotifications::class)->record($order);
        DB::commit();
        session()->forget('cart');

        // 7.1 Send Order Summary Email
        if (!empty($request->email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OrderSummaryMail($order));
            } catch (\Exception $e) {
                // Log error but don't stop the checkout flow
                \Illuminate\Support\Facades\Log::error("Failed to send order summary email to {$request->email}: " . $e->getMessage());
            }
        }

        // 8. Send WhatsApp Notification to Owner
        try {
            $waService = app(WhatsAppService::class);
            $waService->sendOrderNotification($order);
        } catch (\Exception $e) {
            Log::error("WhatsApp Notification Failed: " . $e->getMessage());
            // We don't fail the order if notification fails
        }

        } catch (ValidationException $e) {
            if (DB::transactionLevel()) DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            if (DB::transactionLevel()) DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Unable to place your order. Please try again.'
            ], 500);
        }

        // 7. Return Success
        $redirectUrl = Auth::check() ? app(\App\Services\StorefrontTheme::class)->url('account.orders') : app(\App\Services\StorefrontTheme::class)->url('v1.home', ['order_success' => 1]);

        $tracking = null;
        try {
            $tracking = ['tenant' => (string) $order->tenant_id, 'events' => [app(\App\Services\StorefrontTracking::class)->order($order, \App\Models\Tenant::findOrFail($order->tenant_id))]];
        } catch (\Throwable $e) { report($e); }

        return response()->json([
            'success' => true,
            'order_id' => $order->order_number,
            'tracking' => $tracking,
            'redirect_url' => $redirectUrl
        ]);
    }

}
