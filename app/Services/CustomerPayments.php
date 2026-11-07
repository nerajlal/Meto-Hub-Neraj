<?php

namespace App\Services;

use App\Http\Controllers\OrderController;
use App\Models\{Bundle, Cart, CustomerPayment, Discount, Order, Product, Tenant, TenantPaymentSetting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerPayments
{
    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['payment' => $message]);
    }

    private function details(Request $request): array
    {
        $details = $request->only(['name', 'email', 'phone', 'address', 'apartment', 'city', 'state', 'pincode',
            'delivery_date', 'delivery_time_slot', 'order_notes', 'alternate_phone', 'company_name', 'gst_number', 'landmark']);
        ksort($details);
        return $details;
    }

    public function sessionHash(Request $request): string
    {
        if (!$request->session()->has('customer_payment_owner')) {
            $request->session()->put('customer_payment_owner', Str::random(64));
        }
        return hash('sha256', $request->session()->get('customer_payment_owner'));
    }

    public function owned(Request $request): CustomerPayment
    {
        $request->validate(['checkout_id' => 'required|uuid']);
        return CustomerPayment::whereKey($request->checkout_id)
            ->where('tenant_id', $request->attributes->get('resolved_tenant_id'))
            ->where('session_hash', $this->sessionHash($request))
            ->where('user_id', $request->user()?->id)->firstOrFail();
    }

    public function initiate(Request $request, Tenant $tenant): CustomerPayment
    {
        $request->validate(['gateway' => 'required|in:razorpay,paytm,phonepe', 'checkout_request_id' => 'required|uuid']);
        app(OrderController::class)->validateCheckout($request, $tenant);
        $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();
        $gateway = $request->string('gateway')->toString();
        $credentials = $gateway === 'razorpay'
            ? ['key' => $settings?->razorpay_key, 'secret' => $settings?->razorpay_secret, 'webhook_secret' => $settings?->razorpay_webhook_secret]
            : app(AlternativeCustomerGateways::class)->credentials($settings, $gateway);
        if (in_array(null, $credentials, true) || in_array('', $credentials, true)) $this->invalid('Configure all merchant credentials before enabling this gateway.');
        $currency = strtoupper($tenant->currency ?? 'INR');
        if (!in_array($currency, ['INR', '₹'], true)) $this->invalid('Online checkout currently supports INR stores only.');
        $owner = $this->sessionHash($request);
        // Serialize initiation per store. Duplicate requests reuse a persisted attempt, never create a second provider order.
        $payment = DB::transaction(function () use ($request, $tenant, $credentials, $gateway, $owner) {
            Tenant::whereKey($tenant->id)->lockForUpdate()->firstOrFail();
            $existing = CustomerPayment::whereKey($request->checkout_request_id)->first();
            if ($existing) {
                abort_unless($existing->tenant_id === $tenant->id && hash_equals($existing->session_hash, $owner)
                    && $existing->user_id === $request->user()?->id, 404);
                if ($existing->gateway !== $gateway) $this->invalid('Check the earlier payment status before switching gateways.');
                if ($existing->status !== 'completed' && $existing->snapshot['details'] !== $this->details($request)) {
                    $this->invalid('This checkout already has saved delivery details. Restore those details to resume, or contact the store before starting another payment. Check payment status first.');
                }
                return $existing;
            }
            $raw = $request->user()
                ? Cart::where('user_id', $request->user()->id)->orderBy('id')->get()->map(fn ($item) => [
                    'cart_row_id' => $item->id, 'product_id' => $item->product_id, 'bundle_id' => $item->bundle_id,
                    'variant_id' => $item->product_variant_id, 'size' => $item->size, 'quantity' => $item->quantity,
                    'type' => $item->bundle_id ? 'bundle' : 'product'])->all()
                : $request->session()->get('cart', []);
            if (!$raw) $this->invalid('Your cart is empty.');
            $cart = app(CheckoutCartService::class)->prepare($raw, false);
            $this->reserve($cart, $tenant->id, false);
            $totals = CartService::calculateTotal($cart);
            if ($totals['total'] < ($tenant->min_order_value ?? 0)) $this->invalid('Your cart does not meet the minimum order value.');
            $tax = $tenant->tax_name && $tenant->tax_rate > 0 ? round($totals['total'] * $tenant->tax_rate / 100, 2) : 0;
            $amount = (int) round(($totals['total'] + $tax) * 100);
            if ($amount < 100) $this->invalid('Online payment requires an order total of at least ₹1.');
            if ($amount > 9999999999 || $totals['subtotal'] > 99999999.99) $this->invalid('This order exceeds the supported checkout total.');
            foreach ($cart as &$item) {
                $item['coupon_code'] = $item['coupon']?->code ?? null;
                unset($item['coupon']);
            }
            unset($item);
            $details = $this->details($request);
            $fingerprint = hash('sha256', json_encode([$raw, $details, $amount]));
            $same = CustomerPayment::where('tenant_id', $tenant->id)->where('session_hash', $owner)
                ->where('fingerprint', $fingerprint)->where('gateway', $gateway)->whereIn('status', ['creating', 'ready', 'creation_unknown', 'captured', 'review'])
                ->latest()->first();
            if ($same) return $same;
            $checkoutPath = route('storefront.checkout', ['store_slug' => $tenant->slug], false);
            $checkoutBase = rtrim(config('app.url'), '/');
            if ($tenant->domain && strtolower($request->getHost()) === strtolower($tenant->domain)
                && preg_match('/^[a-zA-Z0-9.-]+$/', $tenant->domain)) $checkoutBase = 'https://'.strtolower($tenant->domain);
            return CustomerPayment::create(['id' => $request->checkout_request_id, 'tenant_id' => $tenant->id,
                'user_id' => $request->user()?->id, 'session_hash' => $owner, 'fingerprint' => $fingerprint,
                'amount' => $amount, 'currency' => 'INR', 'status' => 'creating', 'gateway' => $gateway,
                'credentials' => $credentials,
                'snapshot' => ['store_checkout_url' => $checkoutBase.$checkoutPath, 'cart' => $cart, 'raw_cart' => $raw, 'details' => $details, 'subtotal' => $totals['subtotal'],
                    'savings' => min($totals['subtotal'], $totals['savings']), 'tax_amount' => $tax,
                    'tax_name' => $tenant->tax_name, 'tax_rate' => $tenant->tax_rate]]);
        });
        if (!$payment->wasRecentlyCreated) {
            if ($gateway === 'paytm' && (($payment->status === 'ready' && ($payment->checkout_data['expires_at'] ?? 0) < time())
                || (in_array($payment->status, ['creating', 'creation_unknown'], true) && $payment->created_at->lt(now()->subMinute())))) {
                return app(AlternativeCustomerGateways::class)->resumePaytm($payment, $this);
            }
            return $payment;
        }
        if ($gateway !== 'razorpay') return app(AlternativeCustomerGateways::class)->initiate($payment);
        try {
            $remote = app(CustomerRazorpayApi::class)->call($payment->credentials, 'POST', 'orders', [
                'amount' => $payment->amount, 'currency' => $payment->currency, 'receipt' => $payment->id,
                'partial_payment' => false, 'notes' => ['tenant_id' => (string) $tenant->id, 'checkout_id' => $payment->id]]);
            if (!preg_match('/^order_[a-zA-Z0-9]+$/', $remote['id'] ?? '') || (int) ($remote['amount'] ?? 0) !== $payment->amount
                || ($remote['currency'] ?? '') !== $payment->currency) throw new \RuntimeException('Provider order mismatch');
            $payment->update(['provider_order_id' => $remote['id'], 'status' => 'ready']);
        } catch (\Throwable $e) {
            // No provider internals/secrets in responses or logs. Do not retry a possibly successful POST.
            $payment->update(['status' => 'creation_unknown', 'review_reason' => 'Provider order creation needs reconciliation.']);
        }
        return $payment->fresh();
    }

    public function verify(CustomerPayment $payment, string $paymentId, ?string $signature = null): CustomerPayment
    {
        abort_unless($payment->gateway === 'razorpay', 422);
        if (!preg_match('/^pay_[a-zA-Z0-9]+$/', $paymentId)) $this->invalid('Invalid payment reference.');
        if ($signature !== null) {
            $expected = hash_hmac('sha256', $payment->provider_order_id.'|'.$paymentId, $payment->credentials['secret']);
            if (!hash_equals($expected, $signature)) $this->invalid('Payment signature verification failed.');
        }
        $api = app(CustomerRazorpayApi::class);
        $remote = $api->call($payment->credentials, 'GET', 'payments/'.rawurlencode($paymentId));
        $order = $api->call($payment->credentials, 'GET', 'orders/'.rawurlencode($payment->provider_order_id));
        if (($remote['id'] ?? '') !== $paymentId || ($remote['order_id'] ?? '') !== $payment->provider_order_id
            || (int) ($remote['amount'] ?? -1) !== $payment->amount || ($remote['currency'] ?? '') !== $payment->currency
            || ($order['id'] ?? '') !== $payment->provider_order_id || (int) ($order['amount'] ?? -1) !== $payment->amount
            || ($order['currency'] ?? '') !== $payment->currency || ($order['receipt'] ?? '') !== $payment->id
            || (string) ($order['notes']['tenant_id'] ?? '') !== (string) $payment->tenant_id
            || ($order['notes']['checkout_id'] ?? '') !== $payment->id) $this->invalid('Payment does not match this store checkout.');
        if ((int) ($remote['amount_refunded'] ?? 0) === $payment->amount && $payment->status !== 'completed') {
            $payment->update(['status' => 'refunded', 'review_reason' => null, 'last_checked_at' => now()]);
            return $payment->fresh();
        }
        if (($remote['status'] ?? '') !== 'captured' || !($remote['captured'] ?? false)
            || ($order['status'] ?? '') !== 'paid' || (int) ($order['amount_paid'] ?? 0) !== $payment->amount
            || (int) ($remote['amount_refunded'] ?? 0) !== 0) $this->invalid('Payment is not captured in full. Please check payment status before retrying.');
        return $this->completeVerified($payment, $paymentId);
    }

    // Only provider adapters may call this after a successful authenticated status check.
    public function completeVerified(CustomerPayment $payment, string $paymentId, bool $allowPhonepeReview = false): CustomerPayment
    {
        DB::transaction(function () use ($payment, $paymentId, $allowPhonepeReview) {
            $locked = CustomerPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            if ($locked->gateway === 'phonepe' && $locked->status === 'review' && !$allowPhonepeReview) return;
            if ($locked->provider_payment_id && $locked->provider_payment_id !== $paymentId) $this->invalid('A different payment is already linked to this checkout.');
            if ($locked->status !== 'completed') $locked->update(['provider_payment_id' => $paymentId, 'status' => 'captured', 'last_checked_at' => now()]);
        });
        return $this->fulfil($payment->fresh());
    }

    public function reconcile(CustomerPayment $payment): CustomerPayment
    {
        if ($payment->status === 'completed') return $payment;
        if ($payment->gateway !== 'razorpay') return app(AlternativeCustomerGateways::class)->reconcile($payment, $this);
        if (!$payment->provider_order_id) {
            if ($payment->created_at->gt(now()->subMinute())) return $payment;
            $found = app(CustomerRazorpayApi::class)->call($payment->credentials, 'GET', 'orders', ['receipt' => $payment->id, 'count' => 100]);
            $matches = array_values(array_filter($found['items'] ?? [], fn ($order) =>
                ($order['receipt'] ?? '') === $payment->id && (int) ($order['amount'] ?? -1) === $payment->amount
                && ($order['currency'] ?? '') === $payment->currency
                && ($order['notes']['checkout_id'] ?? '') === $payment->id
                && (string) ($order['notes']['tenant_id'] ?? '') === (string) $payment->tenant_id
                && preg_match('/^order_[a-zA-Z0-9]+$/', $order['id'] ?? '')));
            if (count($matches) !== 1) {
                $payment->update(['last_checked_at' => now()]);
                if (empty($found['items'])) $payment->update(['status' => 'creation_failed', 'review_reason' => null]);
                return $payment->fresh();
            }
            DB::transaction(function () use ($payment, $matches) {
                $locked = CustomerPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                if (!$locked->provider_order_id) $locked->update(['provider_order_id' => $matches[0]['id'], 'status' => 'ready', 'review_reason' => null]);
            });
            $payment = $payment->fresh();
        }
        $payments = app(CustomerRazorpayApi::class)->call($payment->credentials, 'GET', 'orders/'.rawurlencode($payment->provider_order_id).'/payments');
        $payment->update(['last_checked_at' => now()]);
        foreach ($payments['items'] ?? [] as $remote) {
            if (($remote['status'] ?? '') === 'captured') return $this->verify($payment, $remote['id']);
        }
        return $payment->fresh();
    }

    private function reserve(array $cart, int $tenantId, bool $decrement): void
    {
        $rows = [];
        foreach ($cart as $item) foreach ($item['_reservations'] as $row) {
            $key = sprintf('%020d-%020d', $row['product_id'], $row['variant_id']);
            $rows[$key] ??= ['product_id' => $row['product_id'], 'variant_id' => $row['variant_id'], 'quantity' => 0];
            $rows[$key]['quantity'] += $row['quantity'];
        }
        ksort($rows);
        foreach ($rows as $row) {
            $product = Product::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('status', 'active')->lockForUpdate()->find($row['product_id']);
            $variant = $product?->variants()->whereKey($row['variant_id'])->lockForUpdate()->first();
            if (!$variant || (!$product->continue_selling_when_out_of_stock && $variant->stock < $row['quantity'])) {
                $this->invalid('A selected item has insufficient stock.');
            }
            if ($decrement) $variant->decrement('stock', $row['quantity']);
        }
    }

    public function fulfil(CustomerPayment $payment): CustomerPayment
    {
        // Console and webhook processing must never inherit a different visitor's tenant scope.
        $previous = request()->attributes->get('resolved_tenant_id');
        request()->attributes->set('resolved_tenant_id', $payment->tenant_id);
        try {
            DB::transaction(function () use ($payment) {
                $payment = CustomerPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                if ($payment->status === 'completed') return;
                if (!$payment->provider_payment_id || $payment->status !== 'captured') return;
                $snapshot = $payment->snapshot;
                $this->reserve($snapshot['cart'], $payment->tenant_id, true);
                $d = $snapshot['details'];
                $order = Order::withoutGlobalScopes()->create([
                    'tenant_id' => $payment->tenant_id, 'user_id' => $payment->user_id,
                    'order_number' => strtoupper(substr($payment->gateway, 0, 2)).'-'.strtoupper(str_replace('-', '', $payment->id)),
                    'status' => 'pending', 'payment_method' => $payment->gateway, 'payment_status' => 'paid',
                    'gateway_name' => $payment->gateway, 'gateway_order_id' => $payment->provider_order_id, 'gateway_payment_id' => $payment->provider_payment_id,
                    'subtotal' => $snapshot['subtotal'], 'shipping_cost' => 0, 'discount_amount' => $snapshot['savings'],
                    'total_amount' => $payment->amount / 100, 'tax_amount' => $snapshot['tax_amount'],
                    'tax_name' => $snapshot['tax_name'], 'tax_rate' => $snapshot['tax_rate'],
                    'customer_name' => $d['name'] ?? '', 'customer_email' => $d['email'] ?? '', 'customer_phone' => $d['phone'] ?? '',
                    'shipping_address' => ['address' => $d['address'] ?? '', 'apartment' => $d['apartment'] ?? null,
                        'city' => $d['city'] ?? '', 'state' => $d['state'] ?? '', 'zip' => $d['pincode'] ?? ''],
                    'custom_checkout_data' => array_intersect_key($d, array_flip(['alternate_phone', 'company_name', 'gst_number', 'landmark', 'order_notes'])),
                    'delivery_date' => $d['delivery_date'] ?? null, 'delivery_time_slot' => $d['delivery_time_slot'] ?? null,
                    'notes' => $d['order_notes'] ?? null, 'placed_at' => now(),
                ]);
                $codes = [];
                foreach ($snapshot['cart'] as $item) {
                    $order->items()->create(['product_id' => $item['product_id'] ?? null, 'bundle_id' => $item['bundle_id'] ?? null,
                        'name' => $item['name'], 'quantity' => $item['quantity'], 'price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'], 'size' => $item['size'] ?? null, 'type' => $item['type'],
                        'options' => ['coupon_code' => $item['coupon_code'], 'saved_amount' => $item['line_savings'] ?? 0,
                            'original_price' => $item['original_price'] ?? null, 'variant_id' => $item['variant_id'] ?? null,
                            'paid_selection' => $item['_reservations']]]);
                    if ($item['coupon_code']) $codes[] = $item['coupon_code'];
                    if ($item['type'] === 'bundle') Bundle::withoutGlobalScopes()->where('tenant_id', $payment->tenant_id)->whereKey($item['bundle_id'])->increment('total_sales', $item['quantity']);
                }
                Discount::withoutGlobalScopes()->where('tenant_id', $payment->tenant_id)->whereIn('code', array_unique($codes))->increment('uses_count');
                $payment->update(['status' => 'completed', 'order_id' => $order->id, 'review_reason' => null]);
                app(MerchantOrderNotifications::class)->record($order);
                DB::afterCommit(function () use ($order) {
                    try { if ($order->customer_email) Mail::to($order->customer_email)->send(new \App\Mail\OrderSummaryMail($order)); }
                    catch (\Throwable $e) { report($e); }
                });
            }, 3);
        } catch (\Throwable $e) {
            // Captured money remains durably recorded even if stock/order creation fails. Reconciliation retries it.
            \Illuminate\Support\Facades\Log::warning('Customer payment requires order review', ['checkout_id' => $payment->id, 'reason' => $e instanceof ValidationException ? 'stock_unavailable' : 'order_creation_failed']);
            CustomerPayment::whereKey($payment->id)->where('status', 'captured')->update([
                'status' => 'review', 'review_reason' => 'Payment captured; order creation needs review. Do not charge again.']);
        } finally {
            if ($previous === null) request()->attributes->remove('resolved_tenant_id');
            else request()->attributes->set('resolved_tenant_id', $previous);
        }
        return $payment->fresh();
    }
}
