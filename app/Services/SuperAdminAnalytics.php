<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SuperAdminAnalytics
{
    public function paymentQuery(array $filters): Builder
    {
        $query = SubscriptionPayment::with('subscription.tenant');
        if (!empty($filters['from'])) $query->where('attempted_at', '>=', Carbon::parse($filters['from'])->startOfDay());
        if (!empty($filters['to'])) $query->where('attempted_at', '<', Carbon::parse($filters['to'])->addDay()->startOfDay());
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['currency'])) $query->where('currency', $filters['currency']);
        if (!empty($filters['q'])) {
            $search = '%'.$filters['q'].'%';
            $query->where(fn ($q) => $q->where('gateway_id', 'like', $search)->orWhere('invoice_id', 'like', $search)
                ->orWhereHas('subscription.tenant', fn ($t) => $t->where('name', 'like', $search)->orWhere('slug', 'like', $search)));
        }
        return $query;
    }

    private function collections(Carbon $from, Carbon $until): array
    {
        // Refunds are cumulative on these payments; no refund event date is stored.
        return SubscriptionPayment::whereIn('status', ['captured', 'refunded'])->whereNotNull('paid_at')
            ->where('paid_at', '<=', now())->where('paid_at', '>=', $from)->where('paid_at', '<', $until)
            ->selectRaw('currency, SUM(amount) as gross, SUM(amount_refunded) as refunds')->groupBy('currency')->get()
            ->map(fn ($row) => ['currency' => $row->currency, 'gross' => (int) $row->gross,
                'refunds' => (int) $row->refunds, 'net' => (int) $row->gross - (int) $row->refunds])->all();
    }

    public function dashboard(string $from, string $to): array
    {
        $access = app(PlanAccess::class);
        $stores = Tenant::with('subscriptions')->get();
        $plans = collect(config('saas.plans'))->map(fn ($plan) => ['name' => $plan['name'], 'total' => 0, 'paid' => 0, 'trial' => 0, 'other' => 0])->all();
        $totals = ['paid' => 0, 'trial' => 0, 'overdue' => 0, 'admin_grace' => 0, 'storefront_grace' => 0, 'renewing' => 0, 'review' => 0];
        $cycles = ['monthly' => 0, 'yearly' => 0, 'unknown' => 0];
        $followups = [];
        $newStores = $converted = 0;
        $start = Carbon::parse($from)->startOfDay();
        $until = Carbon::parse($to)->addDay()->startOfDay();
        foreach ($stores as $store) {
            $subscription = $store->subscriptions->firstWhere('gateway_id', $store->razorpay_subscription_id);
            $paid = $access->hasPaidAccess($store);
            $latestPaid = $store->subscriptions->where('paid_count', '>', 0)->filter(fn ($s) => $s->paid_through && $s->paid_from?->lte(now()))->sortByDesc('paid_through')->first();
            $trial = !$paid && !$latestPaid && $access->inTrial($store);
            $end = $latestPaid?->paid_through;
            $overdue = !$paid && $end?->lte(now());
            $adminGrace = $overdue && now()->lt($end->copy()->addDays(config('saas.subscription_grace_days', 7)));
            $storeGrace = $overdue && now()->lt($end->copy()->addDays(config('saas.post_paid_storefront_grace_days', 30)));
            $renewing = $paid && !$subscription->cancel_at_cycle_end && $subscription->status === 'active'
                && $subscription->next_charge_at?->gte(now()) && $subscription->next_charge_at->lte(now()->addDays(7));
            foreach (['paid' => $paid, 'trial' => $trial, 'overdue' => $overdue, 'admin_grace' => $adminGrace, 'storefront_grace' => $storeGrace, 'renewing' => $renewing] as $key => $value) $totals[$key] += (int) $value;
            if ($store->subscription_status === 'active' && !$paid) $totals['review']++;
            $plan = $paid ? ($subscription->plan ?: $store->plan) : $store->plan;
            $plan = $plan ?: 'unassigned';
            $plans[$plan] ??= ['name' => ucfirst($plan), 'total' => 0, 'paid' => 0, 'trial' => 0, 'other' => 0];
            $plans[$plan]['total']++;
            $plans[$plan][$paid ? 'paid' : ($trial ? 'trial' : 'other')]++;
            if ($paid) $cycles[in_array($subscription->cycle, ['monthly', 'yearly'], true) ? $subscription->cycle : 'unknown']++;
            if ($store->created_at->gte($start) && $store->created_at->lt($until)) {
                $newStores++;
                if ($store->subscriptions->contains(fn ($s) => $s->paid_count > 0 && $s->paid_from?->lte(now()))) $converted++;
            }
            if ($overdue || $renewing) $followups[] = ['tenant' => $store, 'reason' => $overdue ? ($adminGrace ? 'Coverage expired · admin grace' : ($storeGrace ? 'Coverage expired · storefront grace' : 'Paid coverage expired')) : 'Renewal due', 'date' => $overdue ? $end : $subscription->next_charge_at];
        }
        usort($followups, fn ($a, $b) => $a['date']->getTimestamp() <=> $b['date']->getTimestamp());
        return ['plans' => $plans, 'totals' => $totals, 'cycles' => $cycles, 'followups' => array_slice($followups, 0, 12),
            'new_stores' => $newStores, 'converted' => $converted,
            'conversion' => $newStores ? round($converted / $newStores * 100, 1) : 0,
            'selected' => $this->collections($start, $until),
            'this_month' => $this->collections(now()->startOfMonth(), now()->startOfMonth()->addMonth()),
            'last_month' => $this->collections(now()->subMonthNoOverflow()->startOfMonth(), now()->startOfMonth()),
            'overdue_invoices' => SubscriptionInvoice::whereIn('status', ['issued', 'partially_paid'])->where('amount_due', '>', 0)->where('due_at', '<', now())->selectRaw('currency, COUNT(*) as invoice_count, SUM(amount_due) as due')->groupBy('currency')->get(),
            'undated' => SubscriptionPayment::whereIn('status', ['captured', 'refunded'])->whereNull('paid_at')->count()];
    }
}
