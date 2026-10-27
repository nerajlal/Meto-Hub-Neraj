<?php

namespace Tests\Feature;

use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PlanAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSubdomainsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://metohub.cloud', 'store_domains.base' => null]);
    }

    public function test_saved_subdomain_renders_the_same_store_as_the_slug(): void
    {
        $store = Tenant::create(['name' => 'Thread Store', 'theme' => 'template_1', 'domain' => 'thread.metohub.cloud']);
        $this->get('https://thread.metohub.cloud/')->assertOk()->assertSee('Thread Store');
        $this->get('https://metohub.cloud/store/'.$store->slug)->assertOk()->assertSee('Thread Store');
    }

    public function test_unknown_hosts_cannot_fall_back_to_session_or_slug_tenants(): void
    {
        $store = Tenant::create(['name' => 'Private Store', 'theme' => 'template_1']);
        foreach (['/', '/store/'.$store->slug, '/v1?tenant_id='.$store->id, '/cart/state?store='.$store->slug] as $path) {
            $this->withSession(['active_tenant_id' => $store->id])->get('https://missing.metohub.cloud'.$path)
                ->assertNotFound()->assertSee('Store not found')->assertDontSee('Private Store')
                ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
    }

    public function test_platform_and_test_environment_hosts_keep_the_platform_homepage(): void
    {
        $this->get('https://metohub.cloud/')->assertOk();
        $this->get('https://www.metohub.cloud/')->assertOk();
        config(['app.url' => 'https://test.metohub.cloud']);
        $this->get('https://test.metohub.cloud/')->assertOk();
    }

    public function test_reserved_invalid_and_multilevel_subdomains_cannot_be_saved(): void
    {
        $store = Tenant::create(['name' => 'Shop', 'domain' => 'shop.metohub.cloud']);
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        foreach (['test', 'admin', 'www', 'api', 'nested.shop', '-bad', str_repeat('a', 64)] as $label) {
            $this->postJson('https://metohub.cloud/'.$store->id.'/admin/settings/domain', ['domain' => $label.'.metohub.cloud'])
                ->assertUnprocessable()->assertJsonValidationErrors('domain');
        }
        $this->assertSame('shop.metohub.cloud', $store->fresh()->domain);
    }

    public function test_normalized_hostname_cannot_claim_another_stores_domain(): void
    {
        Tenant::create(['name' => 'First', 'domain' => 'first.metohub.cloud']);
        $store = Tenant::create(['name' => 'Second']);
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        $this->postJson('https://metohub.cloud/'.$store->id.'/admin/settings/domain', ['domain' => ' FIRST.METOHUB.CLOUD '])->assertUnprocessable();
        $this->post('https://metohub.cloud/'.$store->id.'/admin/settings/domain', ['domain' => ' SECOND.METOHUB.CLOUD '])->assertSessionHasNoErrors();
        $this->assertSame('second.metohub.cloud', $store->fresh()->domain);
    }

    public function test_paid_base_plan_keeps_free_subdomain_but_cannot_use_paid_custom_domain(): void
    {
        $store = Tenant::create(['name' => 'Base Store', 'theme' => 'template_1', 'plan' => 'base', 'domain' => 'base-store.metohub.cloud']);
        $store->forceFill(['created_at' => now()->subDays(60), 'razorpay_subscription_id' => 'sub_base'])->save();
        StoreSubscription::create(['tenant_id' => $store->id, 'gateway_id' => 'sub_base', 'checkout_attempt' => fake()->uuid(),
            'plan' => 'base', 'cycle' => 'monthly', 'status' => 'active', 'paid_count' => 1,
            'paid_from' => now()->subDay(), 'paid_through' => now()->addDays(29), 'synced_at' => now()]);
        $this->assertFalse(app(PlanAccess::class)->allows($store, 'custom_domain'));
        $this->get('https://base-store.metohub.cloud/')->assertOk()->assertSee('Base Store');
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        $this->post('https://metohub.cloud/'.$store->id.'/admin/settings/domain', ['domain' => ' NEW-BASE.METOHUB.CLOUD '])->assertSessionHasNoErrors();
        $this->assertSame('new-base.metohub.cloud', $store->fresh()->domain);
        $this->postJson('https://metohub.cloud/'.$store->id.'/admin/settings/domain', ['domain' => 'shop.example.org'])->assertForbidden();
    }

    public function test_reserved_existing_domain_is_not_rendered_as_a_store(): void
    {
        Tenant::create(['name' => 'Reserved Store', 'domain' => 'admin.metohub.cloud']);
        $this->get('https://admin.metohub.cloud/')->assertNotFound()->assertSee('Store not found');
    }

    public function test_admin_does_not_claim_dns_or_tls_is_verified_and_normalizes_legacy_hidden_value(): void
    {
        $store = Tenant::create(['name' => 'Shop', 'plan' => 'base', 'domain' => 'shop.metohub.com']);
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        $this->get('https://metohub.cloud/'.$store->id.'/admin/settings/domain')->assertOk()
            ->assertSee('Saved — setup required')->assertSee('value="shop.metohub.cloud"', false);
        $store->update(['domain' => 'shop.metohub.cloud']);
        config(['store_domains.wildcard_ready' => true]);
        $this->get('https://metohub.cloud/'.$store->id.'/admin/settings/domain')->assertOk()->assertSee('Subdomain configured');
    }
}
