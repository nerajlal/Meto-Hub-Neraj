<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Services\SubscriptionBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class BillingController extends Controller
{
    public function index(SubscriptionBilling $billing)
    {
        $tenant = $this->tenant();
        $subscription = $billing->local($tenant);
        $payments = SubscriptionPayment::whereHas('subscription', fn ($q) => $q->where('tenant_id', $tenant->id))->latest('attempted_at')->paginate(10);

        $billingConfigured = (bool) (config('services.razorpay.key') && config('services.razorpay.secret') && config('services.razorpay.webhook_secret'));
        $key = (string) config('services.razorpay.key');
        $billingMode = str_starts_with($key, 'rzp_test_') ? 'Test mode' : (str_starts_with($key, 'rzp_live_') ? 'Live mode' : 'Configured');
        $terminal = ! $subscription || in_array($subscription->status, ['cancelled', 'completed', 'expired'], true);
        $canChoosePlan = $billingConfigured && $terminal && ! ($subscription?->paid_count > 0 && $subscription?->paid_through?->isFuture());
        $canResumeCheckout = $billingConfigured && $subscription?->gateway_id && ! $subscription->paid_count
            && in_array($subscription->status, ['created', 'pending'], true) && isset(config('saas.plans')[$subscription->plan])
            && in_array($subscription->cycle, ['monthly', 'yearly'], true);

        return view('admin.billing.index', compact('tenant', 'subscription', 'payments', 'billingConfigured', 'billingMode', 'canChoosePlan', 'canResumeCheckout'));
    }

    public function subscribe(Request $request, SubscriptionBilling $billing)
    {
        $request->validate(['cycle' => 'required|in:monthly,yearly', 'plan_tier' => ['nullable', Rule::in(array_keys(config('saas.plans')))]]);
        $tenant = $this->tenant();
        $tier = $request->input('plan_tier') ?: $tenant->plan;
        abort_unless(isset(config('saas.plans')[$tier]), 422, 'Choose a current subscription plan.');
        try {
            $subscription = $billing->start($tenant, $tier, $request->cycle);

            return response()->json(['success' => true, 'subscription_id' => $subscription->gateway_id, 'key' => config('services.razorpay.key'), 'plan_name' => config('saas.plans.'.$subscription->plan.'.name'), 'cycle' => $subscription->cycle]);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Subscription checkout requires reconciliation.', ['tenant_id' => $tenant->id, 'exception' => get_class($e)]);

            return response()->json(['success' => false, 'message' => 'Unable to confirm checkout with the payment provider. Refresh Billing status before retrying.'], 503);
        }
    }

    public function verify(Request $request, SubscriptionBilling $billing)
    {
        $request->validate(['razorpay_payment_id' => 'required|string|max:191', 'razorpay_subscription_id' => 'required|string|max:191', 'razorpay_signature' => 'required|string|max:256']);
        $tenant = $this->tenant();
        abort_unless($tenant->razorpay_subscription_id && hash_equals($tenant->razorpay_subscription_id, $request->razorpay_subscription_id), 422, 'This subscription does not belong to this store.');
        $expected = hash_hmac('sha256', $request->razorpay_payment_id.'|'.$tenant->razorpay_subscription_id, (string) config('services.razorpay.secret'));
        abort_unless(config('services.razorpay.secret') && hash_equals($expected, $request->razorpay_signature), 422, 'Payment verification failed.');
        try {
            $local = $billing->local($tenant);
            $local = $billing->sync($local);
            // Signature proves checkout association, not payment capture or a paid billing period.
            $payment = $billing->array($billing->api()->payment->fetch($request->razorpay_payment_id));
            abort_unless(($payment['id'] ?? '') === $request->razorpay_payment_id, 502, 'Payment reference mismatch.');
            if (! empty($payment['invoice_id'])) {
                $invoice = $billing->array($billing->api()->invoice->fetch($payment['invoice_id']));
                abort_unless(($invoice['subscription_id'] ?? '') === $local->gateway_id, 422, 'Payment ownership mismatch.');
                $billing->invoice($local, $invoice);
            }
            $billing->payment($local, $payment);

            return response()->json(['success' => true] + $this->state($tenant->fresh()));
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Subscription verification could not synchronize.', ['tenant_id' => $tenant->id, 'exception' => get_class($e)]);

            return response()->json(['success' => false, 'message' => 'Payment confirmation is awaiting provider synchronization. Do not pay again; refresh Billing shortly.'], 503);
        }
    }

    public function refresh(SubscriptionBilling $billing)
    {
        $local = $billing->local($this->tenant());
        abort_unless($local, 404, 'No subscription is recorded.');
        try {
            $billing->reconcile($local);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            return back()->withErrors(['billing' => 'Provider synchronization failed. Please retry later.']);
        }

        return back()->with('success', 'Subscription, invoices and payment records refreshed.');
    }

    public function cancel(SubscriptionBilling $billing)
    {
        $local = $billing->local($this->tenant());
        abort_unless($local, 404);
        try {
            $billing->cancel($local);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            return back()->withErrors(['billing' => 'Cancellation could not be confirmed. Refresh status before retrying.']);
        }

        return back()->with('success', 'Cancellation recorded. Any remaining verified paid period remains available.');
    }

    public function receipt(SubscriptionPayment $payment)
    {
        abort_unless((int) $payment->subscription->tenant_id === (int) $this->tenant()->id && $payment->status === 'captured', 404);

        return view('admin.billing.receipt', ['payment' => $payment, 'tenant' => $this->tenant()]);
    }
    public function status()
    {
        return response()->json($this->state($this->tenant()), 200, ['Cache-Control' => 'private, no-store']);
    }

    private function tenant(): \App\Models\Tenant
    {
        return \App\Models\Tenant::findOrFail(request()->attributes->get('resolved_tenant_id'));
    }

    private function state(\App\Models\Tenant $tenant): array
    {
        $local = $tenant->razorpay_subscription_id
            ? $tenant->subscriptions()->where('gateway_id', $tenant->razorpay_subscription_id)->first()
            : $tenant->subscriptions()->latest('id')->first();
        $paid = app(\App\Services\PlanAccess::class)->hasPaidAccess($tenant);

        return ['status' => $local?->status ?? 'not_subscribed', 'paid_access' => $paid,
            'paid_through' => $paid ? $local?->paid_through?->toIso8601String() : null,
            'dashboard_url' => route('admin.dashboard', ['tenant' => $tenant->id]),
            'message' => $paid ? 'Your paid subscription is confirmed and your dashboard is unlocked.'
                : 'Paid access is not confirmed yet. If you already completed checkout, do not pay again. Refresh provider status if confirmation is delayed.'];
    }

}
