<?php

namespace Tests\Feature;

use App\Models\{Tenant, User, StoreSubscription, SubscriptionPayment};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperAdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 11, 8)->setTime(12, 0));
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]));
    }

    private function subscription(string $name, string $status = 'active', string $cycle = 'monthly', int $endDays = 10): StoreSubscription
    {
        $store = Tenant::create(['name' => $name, 'plan' => 'pro', 'subscription_status' => $status]);
        $gateway = 'sub_'.Str::random(12);
        $store->update(['razorpay_subscription_id' => $gateway]);
        return StoreSubscription::create(['tenant_id' => $store->id, 'checkout_attempt' => Str::uuid(), 'gateway_id' => $gateway,
            'plan' => 'pro', 'cycle' => $cycle, 'status' => $status, 'paid_count' => 1, 'synced_at' => now(),
            'paid_from' => now()->subDays(20), 'paid_through' => now()->addDays($endDays), 'next_charge_at' => now()->addDays(3)]);
    }

    private function payment(StoreSubscription $subscription, string $id, string $status, int $amount, string $date, string $currency = 'INR', int $refunded = 0): void
    {
        SubscriptionPayment::create(['store_subscription_id' => $subscription->id, 'gateway_id' => $id,
            'amount' => $amount, 'currency' => $currency, 'amount_refunded' => $refunded, 'status' => $status,
            'paid_at' => in_array($status, ['captured', 'refunded']) ? $date : null, 'attempted_at' => $date]);
    }

    public function test_revenue_uses_confirmed_paid_dates_currency_and_cumulative_refunds(): void
    {
        $s = $this->subscription('Revenue Store');
        $this->payment($s, 'pay_first', 'captured', 10000, '2026-11-01 00:00:00', 'INR', 2000);
        $this->payment($s, 'pay_last', 'refunded', 5000, '2026-11-07 23:59:59', 'INR', 5000);
        $this->payment($s, 'pay_outside', 'captured', 90000, '2026-11-08 00:00:00');
        $this->payment($s, 'pay_failed', 'failed', 99999, '2026-11-04 12:00:00');
        $this->payment($s, 'pay_auth', 'authorized', 99999, '2026-11-04 12:00:00');
        $this->payment($s, 'pay_usd', 'captured', 1234, '2026-11-03 12:00:00', 'USD');
        $this->payment($s, 'pay_previous', 'captured', 4000, '2026-10-31 23:59:59');
        $this->get('/superadmin?from=2026-11-01&to=2026-11-07')->assertOk()->assertViewHas('analyticsReport', function ($r) {
            $money = collect($r['selected'])->keyBy('currency');
            return $money['INR']['gross'] === 15000 && $money['INR']['refunds'] === 7000 && $money['INR']['net'] === 8000
                && $money['USD']['net'] === 1234 && $r['last_month'][0]['net'] === 4000;
        });
    }

    public function test_coverage_not_active_flag_drives_paid_plans_cycles_and_renewals(): void
    {
        $this->subscription('Monthly store');
        $this->subscription('Yearly store', 'active', 'yearly');
        $cancelled = $this->subscription('Cancelled still paid', 'cancelled', 'yearly');
        $cancelled->update(['cancel_at_cycle_end' => true]);
        $this->subscription('Expired active flag', 'active', 'monthly', -2);
        $this->subscription('Long expired', 'halted', 'monthly', -40);
        Tenant::create(['name' => 'Legacy status only', 'plan' => 'base', 'subscription_status' => 'active'])->forceFill(['trial_ends_at' => now()->subDay()])->save();
        Tenant::create(['name' => 'New trial', 'plan' => 'base'])->forceFill(['trial_ends_at' => now()->addDays(10)])->save();
        $r = $this->get('/superadmin')->assertOk()->viewData('analyticsReport');
        $this->assertSame(['paid' => 3, 'trial' => 1, 'overdue' => 2, 'admin_grace' => 1, 'storefront_grace' => 1, 'renewing' => 2, 'review' => 2], $r['totals']);
        $this->assertSame(3, $r['plans']['pro']['paid']);
        $this->assertSame(1, $r['plans']['base']['trial']);
        $this->assertSame(['monthly' => 1, 'yearly' => 2, 'unknown' => 0], $r['cycles']);
        $this->assertSame(5, $r['converted']);
    }

    public function test_month_end_and_overdue_invoices_do_not_invent_debt_from_expired_coverage(): void
    {
        $this->travelTo(now()->setDate(2026, 1, 31)->setTime(12, 0));
        $s = $this->subscription('Month end store');
        $this->payment($s, 'pay_january', 'captured', 9900, '2026-01-31 10:00:00');
        $this->payment($s, 'pay_future', 'captured', 50000, '2026-02-01 10:00:00');
        foreach (['issued', 'paid'] as $status) {
            \App\Models\SubscriptionInvoice::create(['store_subscription_id' => $s->id, 'gateway_id' => 'inv_'.$status,
                'status' => $status, 'currency' => 'INR', 'amount' => 24900, 'amount_due' => 24900, 'due_at' => now()->subDay()]);
        }
        $r = $this->get('/superadmin')->assertOk()->viewData('analyticsReport');
        $this->assertSame(9900, $r['this_month'][0]['net']);
        $this->assertSame(0, $r['totals']['overdue']);
        $this->assertCount(1, $r['overdue_invoices']);
        $this->assertSame(1, (int) $r['overdue_invoices'][0]->invoice_count);
        $this->assertSame(24900, (int) $r['overdue_invoices'][0]->due);
    }

    public function test_payment_history_filters_failed_attempts_and_is_superadmin_only(): void
    {
        $s = $this->subscription('History store');
        $this->payment($s, 'pay_failed_search', 'failed', 24900, '2026-11-07 12:00:00');
        $this->payment($s, 'pay_success_search', 'captured', 24900, '2026-11-07 12:00:00');
        $this->get('/superadmin/payments?status=failed&q=History&currency=INR&from=2026-11-01&to=2026-11-08')
            ->assertOk()->assertSee('pay_failed_search')->assertDontSee('pay_success_search')->assertViewHas('payments', fn ($p) => $p->total() === 1);
        $this->getJson('/superadmin?from=2026-11-08&to=2026-11-01')->assertUnprocessable();
        $this->getJson('/superadmin/payments?status=invalid')->assertUnprocessable();
        $this->actingAs(User::factory()->create(['type' => 'admin', 'tenant_id' => $s->tenant_id]));
        $this->get('/superadmin/payments')->assertForbidden();
    }
}
