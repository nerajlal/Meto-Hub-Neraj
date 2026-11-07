<?php

namespace App\Http\Controllers;

use App\Models\{CustomerPayment, TenantPaymentSetting};
use App\Services\CustomerPayments;
use Illuminate\Http\Request;

class CustomerPaymentWebhookController extends Controller
{
    public function __invoke(Request $request, int $tenant, CustomerPayments $payments)
    {
        $body = $request->getContent();
        abort_if(strlen($body) > 262144, 413);
        $orderId = $request->input('payload.payment.entity.order_id') ?? $request->input('payload.order.entity.id');
        $payment = is_string($orderId) ? CustomerPayment::where('gateway', 'razorpay')->where('tenant_id', $tenant)->where('provider_order_id', $orderId)->first() : null;
        // Outstanding checkouts retain their own encrypted webhook secret across key rotation.
        $secret = $payment?->credentials['webhook_secret'] ?? TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant)->first()?->razorpay_webhook_secret;
        abort_unless($secret && hash_equals(hash_hmac('sha256', $body, $secret), (string) $request->header('X-Razorpay-Signature')), 401);
        if (!in_array($request->input('event'), ['payment.captured', 'order.paid'], true)) return response()->json(['received' => true]);
        if (!$payment) return response()->json(['received' => true]); // Not an order created by this checkout.
        try {
            $payment = $payments->reconcile($payment);
        } catch (\Throwable $e) {
            return response()->json(['received' => false], 503); // Provider retries; scheduled reconciliation is a second recovery path.
        }
        return response()->json(['received' => true]);
    }
}
