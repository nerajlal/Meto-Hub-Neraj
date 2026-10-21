<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Razorpay\Api\Api;
use Tests\TestCase;

class PlanPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_merchant_and_superadmin_pages_share_current_plan_prices(): void
    {
        $this->get('/')->assertOk()->assertSee('₹99', false)->assertSee('₹249', false)->assertSee('₹749', false);
        $this->get('/pricing')->assertOk()->assertSee('₹99', false)->assertSee('₹249', false)->assertSee('₹749', false)
            ->assertSee('₹1,089', false)->assertSee('₹2,739', false)->assertSee('₹8,239', false)->assertDontSee('₹2,999', false);
        $store = Tenant::create(['name' => 'Pricing Store', 'plan' => 'pro']);
        $admin = User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']);
        $this->actingAs($admin)->get('/'.$store->id.'/admin/billing')->assertOk()->assertSee('₹249', false)->assertSee('₹2,739', false);
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]))
            ->get('/superadmin/plans')->assertOk()->assertSee('₹99', false)->assertSee('₹249', false)->assertSee('₹749', false)
            ->assertSee('₹1,089', false)->assertSee('₹2,739', false)->assertSee('₹8,239', false)->assertSee('Custom Domain Support')->assertSee('Priority support coordination');
    }

    public function test_new_subscriptions_charge_the_displayed_monthly_and_annual_prices(): void
    {
        config(['services.razorpay.key' => 'test_key', 'services.razorpay.secret' => 'test_secret', 'services.razorpay.webhook_secret' => 'test_webhook']);
        foreach (['base' => 99, 'pro' => 249, 'enterprise' => 749] as $tier => $monthly) {
            foreach (['monthly' => $monthly, 'yearly' => $monthly * 11] as $cycle => $amount) {
                $store = Tenant::create(['name' => 'Billing Store', 'plan' => 'base']);
                $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
                $planName = 'MetoHub '.config('saas.plans.'.$tier.'.name').($cycle === 'yearly' ? ' Yearly' : '');
                $plan = Mockery::mock();
                $plan->shouldReceive('all')->once()->andReturn(['items' => [['id' => 'plan_wrong_price', 'item' => ['name' => $planName, 'amount' => 299900, 'currency' => 'INR'], 'period' => $cycle, 'interval' => 1]]]);
                $plan->shouldReceive('create')->once()->with(Mockery::on(fn ($data) => $data['period'] === $cycle && $data['interval'] === 1 && $data['item']['amount'] === $amount * 100 &&
                    $data['item']['currency'] === 'INR' && $data['item']['name'] === $planName))->andReturn((object) ['id' => 'plan_current']);
                $subscription = Mockery::mock();
                $subscription->shouldReceive('create')->once()->with(Mockery::on(fn ($data) => $data['plan_id'] === 'plan_current' && $data['notes']['tenant_id'] === $store->id))
                    ->andReturn((object) ['id' => 'sub_'.$tier.'_'.$cycle]);
                $this->app->bind(Api::class, fn () => (object) ['plan' => $plan, 'subscription' => $subscription]);
                $this->postJson('/'.$store->id.'/admin/billing/subscribe', ['cycle' => $cycle, 'plan_tier' => $tier])->assertOk()->assertJson(['success' => true]);
                $this->assertSame('base', $store->fresh()->plan);
                $this->assertSame($tier, $store->subscriptions()->first()->plan);
                $this->assertSame('sub_'.$tier.'_'.$cycle, $store->fresh()->razorpay_subscription_id);
            }
        }
    }

    public function test_legacy_stores_keep_their_recorded_plan_and_subscription_until_explicitly_changed(): void
    {
        $store = Tenant::create(['name' => 'Legacy Store', 'plan' => 'sprout', 'subscription_status' => 'active', 'razorpay_subscription_id' => 'sub_existing']);
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        $this->get('/'.$store->id.'/admin/billing')->assertOk()->assertSee('Legacy plan: Sprout')->assertSee('Historical price not recorded');
        $this->postJson('/'.$store->id.'/admin/billing/subscribe', ['cycle' => 'monthly', 'plan_tier' => 'sprout'])->assertUnprocessable();
        $this->assertSame('sprout', $store->fresh()->plan);
        $this->assertSame('sub_existing', $store->fresh()->razorpay_subscription_id);
        $this->assertSame('active', $store->fresh()->subscription_status);
    }
}
