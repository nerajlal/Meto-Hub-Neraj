<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CheckoutPaymentPolicy;
use App\Services\PlanAccess;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubscriptionExpiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-10-21 12:00:00'));
        Http::preventStrayRequests();
        Mail::fake();
    }

    private function store(int $days, bool $paid = true): Tenant
    {
        $tenant = Tenant::create(['name' => 'Grace Store', 'plan' => 'pro', 'theme' => 'template_2']);
        $tenant->forceFill(['created_at' => now()->subDays($paid ? 120 : config('saas.trial_days') + $days),
            'subscription_status' => 'cancelled', 'razorpay_subscription_id' => $paid ? 'sub_grace' : null])->save();
        if ($paid) {
            StoreSubscription::create(['tenant_id' => $tenant->id, 'gateway_id' => 'sub_grace', 'checkout_attempt' => fake()->uuid(),
                'plan' => 'pro', 'cycle' => 'monthly', 'status' => 'cancelled', 'paid_count' => 1, 'paid_from' => now()->subDays($days + 30),
                'paid_through' => now()->subDays($days), 'synced_at' => now()]);
        }

        return $tenant;
    }

    public function test_paid_and_trial_expiry_keep_store_open_for_exactly_thirty_days(): void
    {
        foreach ([true, false] as $paid) {
            $tenant = $this->store(29, $paid);
            $this->get($tenant->storefrontUrl())->assertOk();
            $this->assertSame(['cod'], app(CheckoutPaymentPolicy::class)->availableMethods($tenant));
            $end = app(PlanAccess::class)->storefrontGraceEndsAt($tenant);
            $this->travelTo($end->copy()->subSecond());
            $this->get($tenant->storefrontUrl())->assertOk();
            $this->travelTo($end);
            $this->get($tenant->storefrontUrl())->assertStatus(503)->assertSee('Store temporarily unavailable')
                ->assertHeader('Cache-Control', 'no-store, private')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
            $this->assertSame([], app(CheckoutPaymentPolicy::class)->availableMethods($tenant));
            $this->travelTo(Carbon::parse('2026-10-21 12:00:00'));
        }
    }

    public function test_expired_admin_sees_only_billing_and_other_routes_are_enforced_server_side(): void
    {
        $tenant = $this->store(5);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($user);
        foreach (['', '/orders', '/products', '/settings', '/settings/theme', '/notifications'] as $path) {
            $this->get("/{$tenant->id}/admin".$path)->assertRedirect(route('admin.billing', ['tenant' => $tenant->id]));
        }
        $response = $this->get("/{$tenant->id}/admin/billing")->assertOk()->assertSee('Your subscription has ended.')
            ->assertSee('Your storefront stays open until')->assertDontSee('merchant-notifications-config');
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertCount(1, $xpath->query('//*[@id="adminSidebar"]//a'));
        $this->assertStringContainsString('/admin/billing', $xpath->query('//*[@id="adminSidebar"]//a')->item(0)->getAttribute('href'));
        $this->postJson("/{$tenant->id}/admin/settings/theme", ['theme' => 'template_6'])->assertStatus(402)->assertJsonPath('billing_url', route('admin.billing', ['tenant' => $tenant->id]));
        $this->assertSame('template_2', $tenant->fresh()->theme);
        $this->getJson("/{$tenant->id}/admin/orders")->assertStatus(402);
    }

    public function test_login_goes_directly_to_billing_even_with_an_intended_admin_url(): void
    {
        $tenant = $this->store(5);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin', 'password' => 'password']);
        $this->withSession(['url.intended' => url("/{$tenant->id}/admin/orders")])->post('/admin/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('admin.billing', ['tenant' => $tenant->id]));
        $this->assertAuthenticatedAs($user);
        $this->get('/admin/login')->assertRedirect(route('admin.billing', ['tenant' => $tenant->id]));
    }

    public function test_custom_domain_is_available_during_grace_then_shows_the_unavailable_page(): void
    {
        $tenant = $this->store(29);
        $tenant->update(['domain' => 'grace-shop.example.com']);
        $this->get('https://grace-shop.example.com')->assertOk();
        $this->travelTo(now()->addDay());
        $this->get('https://grace-shop.example.com')->assertStatus(503)->assertSee('subscription payment needs renewal');
    }

    public function test_expired_public_routes_and_direct_order_submission_are_blocked_after_grace(): void
    {
        $tenant = $this->store(31);
        foreach (['', '/all-products', '/checkout', '/product?id=1'] as $path) {
            $this->get(rtrim($tenant->storefrontUrl(), '/').$path)->assertStatus(503);
        }
        $this->postJson('/order/place?store='.$tenant->slug, [])->assertStatus(503);
        $this->get('/v2?tenant_id='.$tenant->id)->assertStatus(503);
        $this->assertDatabaseCount('orders', 0);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/store/'.$tenant->slug.'/sitemap');
    }

    public function test_customers_can_place_cod_orders_during_grace(): void
    {
        $tenant = $this->store(29);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Test shirt', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => 'M', 'price' => 100, 'stock' => 5]);
        $this->mock(\App\Services\WhatsAppService::class, fn ($mock) => $mock->shouldReceive('sendOrderNotification')->andReturn(true));
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => ['shirt' => ['type' => 'product', 'product_id' => $product->id, 'variant_id' => $variant->id, 'size' => 'M', 'quantity' => 1, 'price' => 100, 'name' => 'Test shirt']]])
            ->postJson('/order/place?store='.$tenant->slug, ['name' => 'Test buyer', 'email' => 'buyer@example.test', 'phone' => '9000000000',
                'address' => 'Test address', 'city' => 'Kochi', 'state' => 'Kerala', 'pincode' => '682001', 'payment_method' => 'cod'])->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', ['tenant_id' => $tenant->id]);
        $this->assertEquals(4, $variant->fresh()->stock);
    }

    public function test_renewal_restores_store_and_admin_without_changing_the_expiry_anchor(): void
    {
        $tenant = $this->store(31);
        $this->get($tenant->storefrontUrl())->assertStatus(503);
        $oldEnd = app(PlanAccess::class)->expiredAt($tenant);
        $tenant->update(['name' => 'Renamed shop']);
        $this->assertEquals($oldEnd, app(PlanAccess::class)->expiredAt($tenant->fresh()));
        $tenant->subscriptions()->first()->update(['status' => 'active', 'paid_through' => now()->addDays(30)]);
        $tenant->update(['subscription_status' => 'active']);
        $this->get($tenant->storefrontUrl())->assertOk();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']))->get("/{$tenant->id}/admin/settings")->assertOk();
    }

    public function test_legacy_active_flag_without_a_paid_period_does_not_bypass_expiry(): void
    {
        $tenant = $this->store(31, false);
        $tenant->update(['subscription_status' => 'active']);
        $this->assertFalse(app(PlanAccess::class)->hasPaidAccess($tenant));
        $this->get($tenant->storefrontUrl())->assertStatus(503);
    }
}
