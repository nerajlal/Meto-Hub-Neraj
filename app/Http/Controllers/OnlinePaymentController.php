<?php

namespace App\Http\Controllers;

use App\Models\{CustomerPayment, Order, Tenant};
use App\Services\CustomerPayments;
use Illuminate\Http\Request;

class OnlinePaymentController extends Controller
{
    public function initiate(Request $request, CustomerPayments $payments)
    {
        $tenant = Tenant::findOrFail($request->attributes->get('resolved_tenant_id'));
        $payment = $payments->initiate($request, $tenant);
        if (in_array($payment->status, ['completed', 'creation_failed', 'failed'], true)) return $this->result($payment, $request);
        if ($payment->status !== 'ready') return response()->json(['success' => false, 'checkout_id' => $payment->id,
            'message' => 'Checkout is awaiting payment reconciliation. Check payment status before trying again.'], 409);
        if ($payment->gateway !== 'razorpay') {
            return response()->json(['success' => true, 'gateway' => $payment->gateway, 'checkout_id' => $payment->id,
                'order_id' => $payment->provider_order_id, 'amount' => $payment->amount, 'currency' => $payment->currency]
                + app(\App\Services\AlternativeCustomerGateways::class)->checkoutData($payment));
        }
        return response()->json(['success' => true, 'gateway' => 'razorpay', 'checkout_id' => $payment->id,
            'key' => $payment->credentials['key'], 'order_id' => $payment->provider_order_id,
            'amount' => $payment->amount, 'currency' => $payment->currency, 'store_name' => $tenant->name]);
    }

    public function verify(Request $request, CustomerPayments $payments)
    {
        $request->validate(['gateway' => 'required|in:razorpay', 'razorpay_payment_id' => 'required|string|max:100',
            'razorpay_order_id' => 'required|string|max:100', 'razorpay_signature' => 'required|string|size:64']);
        $payment = $payments->owned($request);
        abort_unless($payment->gateway === 'razorpay' && $payment->provider_order_id && hash_equals($payment->provider_order_id, $request->razorpay_order_id), 422, 'Payment order mismatch.');
        try {
            $payment = $payments->verify($payment, $request->razorpay_payment_id, $request->razorpay_signature);
        } catch (\Illuminate\Validation\ValidationException $e) { throw $e; }
        catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Payment could not be checked. Use Check payment status; do not pay again.'], 503);
        }
        return $this->result($payment, $request);
    }

    public function status(Request $request, CustomerPayments $payments)
    {
        $payment = $payments->owned($request);
        try { $payment = $payments->reconcile($payment); }
        catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Payment status is temporarily unavailable. Do not pay again; check again shortly.'], 503);
        }
        return $this->result($payment, $request);
    }

    public function result(CustomerPayment $payment, Request $request)
    {
        if ($payment->status !== 'completed') return response()->json(['success' => false, 'checkout_id' => $payment->id,
            'status' => $payment->status, 'restart_allowed' => in_array($payment->status, ['creation_failed', 'failed'], true),
            'message' => in_array($payment->status, ['creation_failed', 'failed'], true) ? 'No usable checkout was created, or the provider reports failure/expiry. Check the store settings before starting again.' : ($payment->status === 'refunded' ? 'This payment was refunded. Contact the store before starting a new checkout.' : ($payment->status === 'review'
                ? 'Your payment needs store review. Do not pay again. Reference: '.$payment->id
                : 'Payment is not confirmed yet. Check payment status before paying again.'))], 202);
        // Only clear the cart that was paid, never a different/new cart created after checkout started.
        if (!$request->session()->get('customer_payment_carts_cleared.'.$payment->id)) {
            $snapshot = $payment->snapshot;
            if (!$request->user() && $request->session()->get('cart', []) === $snapshot['raw_cart']) $request->session()->forget('cart');
            if ($request->user()) {
                foreach ($snapshot['raw_cart'] as $row) {
                    \App\Models\Cart::where('tenant_id', $payment->tenant_id)->where('user_id', $payment->user_id)
                        ->whereKey($row['cart_row_id'] ?? 0)->where('product_id', $row['product_id'])->where('bundle_id', $row['bundle_id'])
                        ->where('product_variant_id', $row['variant_id'])->where('size', $row['size'])
                        ->where('quantity', $row['quantity'])->delete();
                }
            }
            $request->session()->put('customer_payment_carts_cleared.'.$payment->id, true);
        }
        $order = Order::withoutGlobalScopes()->where('tenant_id', $payment->tenant_id)->findOrFail($payment->order_id);
        $tracking = null;
        try { $tracking = ['tenant' => (string) $payment->tenant_id,
            'events' => [app(\App\Services\StorefrontTracking::class)->order($order, Tenant::findOrFail($payment->tenant_id))]]; }
        catch (\Throwable $e) { report($e); }
        $url = $request->user() ? app(\App\Services\StorefrontTheme::class)->url('account.orders')
            : app(\App\Services\StorefrontTheme::class)->url('v1.home', ['order_success' => 1]);
        return response()->json(['success' => true, 'verified' => true, 'checkout_id' => $payment->id,
            'order_id' => $order->order_number, 'redirect_url' => $url, 'tracking' => $tracking]);
    }
}
