<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SuperAdminStoreOverview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 15)->setTime(12, 0));
    }

    private function store(string $name, ?string $status = null, int $ageHours = 0, string $plan = 'base'): Tenant
    {
        $tenant = Tenant::create(['name' => $name, 'plan' => $plan, 'theme' => 'template_15', 'subscription_status' => $status]);
        $tenant->forceFill(['created_at' => now()->subHours($ageHours)])->save();
        User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin', 'email_verified_at' => null]);

        return $tenant;
    }

    private function signIn(): void
    {
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]));
    }

    public function test_only_superadmin_can_open_overview_and_billing_pages(): void
    {
        $store = $this->store('Private store');
        foreach (['admin', 'user'] as $type) {
            $this->actingAs(User::factory()->create(['type' => $type, 'tenant_id' => $store->id]));
            foreach (['/superadmin', '/superadmin/subscriptions', '/superadmin/tenants', '/superadmin/tenants/'.$store->id, '/superadmin/plans'] as $url) {
                $this->get($url)->assertForbidden();
            }
        }
    }

    public function test_dashboard_counts_trial_boundaries_and_recorded_subscription_states(): void
    {
        $this->store('Fresh trial', null, 1);
        $this->store('Ending trial', null, 167);
        $this->store('Expired exactly now', null, 168);
        $this->store('Pending checkout', 'pending', 240);
        $this->store('Authorized mandate', 'authenticated');
        $this->store('Paid store', 'active', 720, 'pro');
        $this->store('Halted store', 'halted', 720);
        $this->store('Unknown provider state', 'unexpected');
        $this->signIn();
        $this->get('/superadmin')->assertOk()->assertViewHas('counts', fn ($counts) => $counts['total'] === 8 && $counts['trial'] === 2 && $counts['trial_expired'] === 1 &&
            $counts['pending'] === 2 && $counts['active'] === 1 && $counts['halted'] === 1 &&
            $counts['unknown'] === 1 && $counts['trial_ending'] === 1 && $counts['unverified'] === 8)
            ->assertSee('Subscriptions & Trials', false)->assertSee('Plan distribution')->assertSee('Pro');
    }

    public function test_store_filters_match_the_same_status_classification_and_keep_query_on_pagination(): void
    {
        $active = $this->store('Active shop', 'active', 240, 'enterprise');
        $expired = $this->store('Expired shop', null, 168);
        $pending = $this->store('Pending shop', 'pending');
        $trial = $this->store('New shop', null, 12);
        $this->signIn();
        foreach (['active' => $active, 'trial_expired' => $expired, 'pending' => $pending, 'trial' => $trial] as $status => $expected) {
            $this->get('/superadmin/subscriptions?status='.$status)->assertOk()->assertViewHas('tenants', fn ($stores) => $stores->total() === 1 && $stores->first()->id === $expected->id);
        }
        $this->get('/superadmin/tenants?plan=enterprise&q=Active')->assertOk()->assertViewHas('tenants', fn ($stores) => $stores->total() === 1 && $stores->first()->id === $active->id);
        $this->get('/superadmin/tenants?q=NoMatch')->assertOk()->assertSee('No stores match these filters.');
        $this->getJson('/superadmin/subscriptions?status=invalid')->assertUnprocessable();
        for ($i = 0; $i < 16; $i++) {
            $this->store('Matching pending '.$i, 'pending');
        }
        $this->get('/superadmin/subscriptions?status=pending&q=Matching')->assertOk()->assertSee('status=pending', false)->assertSee('q=Matching', false);
    }

    public function test_tenant_billing_uses_actual_references_and_does_not_invent_payment_dates_or_amount_due(): void
    {
        $store = $this->store('Billing store', 'pending', 192, 'pro');
        $store->update(['razorpay_subscription_id' => 'sub_billing_store', 'razorpay_customer_id' => 'cus_billing_store']);
        $this->signIn();
        $this->get('/superadmin/tenants/'.$store->id)->assertOk()->assertSee('Subscription & Trial Details', false)
            ->assertSee('Payment pending')->assertSee('Trial expired')->assertSee('sub_billing_store')->assertSee('cus_billing_store')
            ->assertSee('249')->assertSee('Not recorded')->assertSee('Riviera Club')->assertSee($store->storefrontUrl());
        $this->get('/superadmin/subscriptions')->assertOk()->assertSee('not a confirmed overdue invoice');
    }

    public function test_global_order_metrics_are_grouped_by_currency_and_exclude_cancelled_value(): void
    {
        $inr = $this->store('INR store');
        $usd = $this->store('USD store');
        $inr->update(['currency' => 'INR']);
        $usd->update(['currency' => 'USD']);
        foreach ([[$inr, 'pending', 100], [$usd, 'pending', 25], [$inr, 'cancelled', 900]] as [$tenant, $status, $amount]) {
            Order::create(['tenant_id' => $tenant->id, 'order_number' => uniqid('TEST-'), 'status' => $status, 'payment_method' => 'cod',
                'payment_status' => 'pending', 'subtotal' => $amount, 'total_amount' => $amount]);
        }
        $this->signIn();
        $this->withSession(['active_tenant_id' => $inr->id])->get('/superadmin')->assertOk()
            ->assertViewHas('totalOrders', 3)->assertViewHas('salesByCurrency', fn ($sales) => (float) $sales->firstWhere('currency', 'INR')->amount === 100.0 && (float) $sales->firstWhere('currency', 'USD')->amount === 25.0)
            ->assertSee('this is not SaaS revenue');
    }

    public function test_current_plan_prices_and_all_theme_choices_are_available_when_creating_stores(): void
    {
        $this->signIn();
        $this->get('/superadmin/plans')->assertOk()->assertSee('99')->assertSee('249')->assertSee('749')->assertSee('1,089')->assertSee('2,739')->assertSee('8,239')->assertDontSee('Stripe');
        $this->get('/superadmin/create-tenant')->assertOk()->assertSee('Pro')->assertSee('Riviera Club')->assertSee('Maison Luxe')->assertDontSee('$9/mo');
        $this->postJson('/superadmin/create-tenant', ['name' => 'Owner', 'site_name' => 'Store', 'email' => 'owner@example.com',
            'password' => 'password123', 'plan' => 'invented', 'theme' => 'invented'])->assertUnprocessable()->assertJsonValidationErrors(['plan', 'theme']);
        $store = $this->store('Legacy store', null, 1, 'maison');
        $this->assertNull(app(SuperAdminStoreOverview::class)->describe($store)['monthly_price']);
    }
}
