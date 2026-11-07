<?php

namespace App\Http\Controllers;

use App\Models\{CustomerPayment, TenantPaymentSetting};
use App\Services\{CustomerPayments, PaytmChecksum};
use Illuminate\Http\Request;

class AlternativePaymentCallbackController extends Controller
{
    private function paytmData(Request $request): ?array
    {
        $raw = $request->getContent();
        if ($raw !== '' && str_contains((string) $request->header('Content-Type'), 'application/json')) $data = json_decode($raw, true);
        elseif ($raw !== '') parse_str($raw, $data);
        else $data = $request->post();
        return is_array($data) ? $data : null;
    }

    private function paytmParameters(Request $request, CustomerPayment $payment): bool
    {
        $data = $this->paytmData($request);
        return $data && ($data['MID'] ?? '') === $payment->credentials['mid']
            && ($data['ORDERID'] ?? '') === $payment->provider_order_id
            && PaytmChecksum::valid($data, $payment->credentials['merchant_key'], $data['CHECKSUMHASH'] ?? null);
    }

    public function paytmCallback(Request $request, string $checkout, CustomerPayments $payments)
    {
        abort_if(strlen($request->getContent()) > 262144, 413);
        $payment = CustomerPayment::whereKey($checkout)->where('gateway', 'paytm')->firstOrFail();
        abort_unless($this->paytmParameters($request, $payment), 401);
        try { $payments->reconcile($payment); } catch (\Throwable $e) { /* Status page/scheduler can retry. */ }
        // The callback can be a cross-site POST without the shopper's cookie. Never reveal order details here.
        return redirect()->to(rtrim(config('app.url'), '/').route('payment.return', ['checkout' => $payment->id], false), 303);
    }

    public function paytmWebhook(Request $request, int $tenant, CustomerPayments $payments)
    {
        abort_if(strlen($request->getContent()) > 262144, 413);
        $payment = CustomerPayment::where('tenant_id', $tenant)->where('gateway', 'paytm')->where('provider_order_id', $request->input('ORDERID'))->first();
        if (!$payment) {
            $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant)->first();
            $data = $this->paytmData($request);
            abort_unless($settings?->paytm_merchant_key && $data && ($data['MID'] ?? '') === $settings->paytm_mid
                && PaytmChecksum::valid($data, $settings->paytm_merchant_key, $data['CHECKSUMHASH'] ?? null), 401);
            return response()->json(['received' => true]);
        }
        abort_unless($this->paytmParameters($request, $payment), 401);
        try { $payments->reconcile($payment); }
        catch (\Throwable $e) { return response()->json(['received' => false], 503); }
        return response()->json(['received' => true]);
    }

    public function phonepeWebhook(Request $request, int $tenant, CustomerPayments $payments)
    {
        abort_if(strlen($request->getContent()) > 262144, 413);
        $data = $request->input('payload');
        if (!is_array($data)) $data = $request->input('data', []);
        if (!is_array($data)) $data = [];
        $reference = $data['merchantOrderId'] ?? $data['originalMerchantOrderId'] ?? null;
        $payment = is_string($reference) ? CustomerPayment::where('tenant_id', $tenant)->where('gateway', 'phonepe')->where('provider_order_id', $reference)->first() : null;
        $credentials = $payment?->credentials;
        if (!$credentials) {
            $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant)->first();
            $credentials = ['webhook_username' => $settings?->phonepe_webhook_username, 'webhook_password' => $settings?->phonepe_webhook_password];
        }
        abort_unless($credentials['webhook_username'] && $credentials['webhook_password']
            && hash_equals(hash('sha256', $credentials['webhook_username'].':'.$credentials['webhook_password']), (string) $request->header('Authorization')), 401);
        $event = $request->input('event') ?? $request->input('type');
        $refundNotice = !empty($data['merchantRefundId']) || !empty($data['refundId']) || !empty($data['originalMerchantOrderId'])
            || (is_string($event) && str_contains(strtolower($event), 'refund'));
        if ($payment && $refundNotice) {
            CustomerPayment::whereKey($payment->id)->where('status', '!=', 'completed')->update([
                'status' => 'review', 'review_reason' => 'PhonePe refund notice received; verify refund at provider before any fulfilment.']);
            return response()->json(['received' => true]);
        }
        $orderEvent = in_array($event, ['checkout.order.completed', 'checkout.order.failed', 'CHECKOUT_ORDER_COMPLETED', 'CHECKOUT_ORDER_FAILED'], true)
            || ($event === null && isset($data['orderId']) && in_array($data['state'] ?? '', ['COMPLETED', 'FAILED'], true));
        // The SDK also documents a data envelope. Never treat a refund/subscription callback as an order payment.
        if (!$payment || !$orderEvent || !empty($data['merchantRefundId']) || !empty($data['refundId']) || !empty($data['originalMerchantOrderId'])
            || !empty($data['merchantSubscriptionId'])) return response()->json(['received' => true]);
        try { $payments->reconcile($payment); }
        catch (\Throwable $e) { return response()->json(['received' => false], 503); }
        return response()->json(['received' => true]);
    }

    public function returnPage(Request $request, string $checkout, CustomerPayments $payments)
    {
        $payment = CustomerPayment::whereKey($checkout)->firstOrFail();
        $owned = $request->session()->has('customer_payment_owner')
            && hash_equals($payment->session_hash, hash('sha256', $request->session()->get('customer_payment_owner')))
            && $payment->user_id === $request->user()?->id;
        if ($owned) {
            try { $payment = $payments->reconcile($payment); } catch (\Throwable $e) { /* Safe generic pending message. */ }
        }
        $orderUrl = null;
        if ($owned && $payment->status === 'completed') {
            $request->attributes->set('resolved_tenant_id', $payment->tenant_id);
            $result = app(OnlinePaymentController::class)->result($payment, $request)->getData(true);
            $orderUrl = $result['redirect_url'];
        }
        return response()->view('storefront.payment-return', ['payment' => $payment, 'owned' => $owned, 'orderUrl' => $orderUrl,
            'storeCheckoutUrl' => $payment->snapshot['store_checkout_url'] ?? rtrim(config('app.url'), '/').route('storefront.checkout', ['store_slug' => \App\Models\Tenant::findOrFail($payment->tenant_id)->slug], false)], 200,
            ['Cache-Control' => 'private, no-store', 'X-Robots-Tag' => 'noindex, nofollow', 'Referrer-Policy' => 'no-referrer']);
    }
}
