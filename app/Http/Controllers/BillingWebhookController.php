<?php

namespace App\Http\Controllers;

use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Services\SubscriptionBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillingWebhookController extends Controller
{
    public function __invoke(Request $request, SubscriptionBilling $billing)
    {
        $secret = config('services.razorpay.webhook_secret');
        abort_unless($secret, 503, 'Billing webhook is not configured.');
        abort_unless(strlen($request->getContent()) <= 262144, 413);
        $signature = $request->header('X-Razorpay-Signature', '');
        abort_unless(is_string($signature) && hash_equals(hash_hmac('sha256', $request->getContent(), $secret), $signature), 401, 'Invalid webhook signature.');
        $data = $request->json()->all();
        $event = $data['event'] ?? '';
        if (! is_string($event) || ! str_starts_with($event, 'subscription.')) {
            return response()->json(['ignored' => true]);
        }
        $remoteId = data_get($data, 'payload.subscription.entity.id');
        abort_unless(is_string($remoteId) && strlen($remoteId) <= 191, 422);
        $eventId = $request->header('X-Razorpay-Event-Id') ?: hash('sha256', $request->getContent());
        abort_unless(is_string($eventId) && strlen($eventId) <= 191, 422);

        return Cache::lock('billing-webhook-'.$eventId, 120)->block(5, function () use ($data, $eventId, $event, $remoteId, $request, $billing) {
            if (DB::table('billing_webhook_events')->where('event_id', $eventId)->whereNotNull('processed_at')->exists()) {
                return response()->json(['duplicate' => true]);
            }
            $local = StoreSubscription::where('gateway_id', $remoteId)->first();
            if (! $local) {
                $tenant = Tenant::where('razorpay_subscription_id', $remoteId)->first();
                if ($tenant) {
                    $local = $billing->local($tenant);
                }
            }
            if (! $local) {
                // Only recover an uncertain creation using its persisted unguessable attempt reference.
                $attempt = data_get($data, 'payload.subscription.entity.notes.checkout_attempt');
                $local = is_string($attempt) ? StoreSubscription::where('checkout_attempt', $attempt)->whereNull('gateway_id')->first() : null;
                if ($local && (string) data_get($data, 'payload.subscription.entity.notes.tenant_id') !== (string) $local->tenant_id) {
                    $local = null;
                }
                if ($local) {
                    $local->update(['gateway_id' => $remoteId]);
                    $local->tenant->update(['razorpay_subscription_id' => $remoteId]);
                }
            }
            if (! $local) {
                return response()->json(['ignored' => true]);
            }
            Cache::lock('billing-tenant-'.$local->tenant_id, 120)->block(5, function () use ($local, $billing, $data, $remoteId) {
                // Fetch authoritative current state, rather than applying old/out-of-order event snapshots.
                $billing->sync($local);
                if ($paymentId = data_get($data, 'payload.payment.entity.id')) {
                    $payment = $billing->array($billing->api()->payment->fetch($paymentId));
                    abort_unless(($payment['id'] ?? '') === $paymentId, 502);
                    if (! empty($payment['invoice_id'])) {
                        $invoice = $billing->array($billing->api()->invoice->fetch($payment['invoice_id']));
                        abort_unless(($invoice['subscription_id'] ?? '') === $remoteId, 422, 'Payment ownership mismatch.');
                        $billing->invoice($local, $invoice);
                    }
                    $billing->payment($local, $payment);
                }
            });
            DB::table('billing_webhook_events')->updateOrInsert(['event_id' => $eventId], [
                'event_type' => $event, 'payload_hash' => hash('sha256', $request->getContent()), 'processed_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);

            return response()->json(['received' => true]);
        });
    }
}
