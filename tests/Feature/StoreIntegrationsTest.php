<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StoreIntegration;
use App\Models\Tenant;
use App\Models\User;
use App\Services\IntegrationDomainVerification;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StoreIntegrationsTest extends TestCase
{
    use RefreshDatabase;

    // Laravel's default test URL helper strips trailing slashes; verification properties require them.
    protected function prepareUrlForRequest($uri)
    {
        $url = parent::prepareUrlForRequest($uri);

        return str_ends_with((string) $uri, '/') ? rtrim($url, '/').'/' : $url;
    }

    private function settings(Tenant $tenant, array $values = []): StoreIntegration
    {
        $row = new StoreIntegration;
        $row->forceFill(['tenant_id' => $tenant->id, 'mode' => 'direct', 'ga4_enabled' => true, 'ga4_id' => 'G-TEST123456', 'meta_enabled' => true, 'meta_pixel_id' => '123456789012345'] + $values)->save();

        return $row;
    }

    private function configFrom($response): ?array
    {
        if (! preg_match('~<script id="metohub-tracking-config" type="application/json">(.*?)</script>~s', $response->getContent(), $match)) {
            return null;
        }

        return json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
    }

    private function input(array $values = []): array
    {
        return array_replace(['mode' => 'direct', 'ga4_enabled' => 1, 'ga4_id' => 'G-TEST123456', 'gtm_enabled' => 0, 'gtm_id' => null, 'meta_enabled' => 1, 'meta_pixel_id' => '123456789012345', 'gsc_enabled' => 1, 'gsc_token' => 'verification_token_123456'], $values);
    }

    public function test_settings_are_authorized_validated_and_do_not_report_fake_verification(): void
    {
        $tenant = Tenant::create(['name' => 'Store']);
        $other = Tenant::create(['name' => 'Other']);
        $path = '/'.$tenant->id.'/admin/settings/integrations';
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $this->get($path)->assertOk()->assertSee('Integrations')->assertSee('Disabled');
        $this->assertDatabaseCount('store_integrations', 0);
        $this->post($path, $this->input() + ['tenant_id' => $other->id, 'domain_verified_at' => now()])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('store_integrations', ['tenant_id' => $tenant->id, 'ga4_id' => 'G-TEST123456', 'domain_verified_at' => null]);
        $this->assertDatabaseCount('store_integrations', 1);
        $this->get($path)->assertSee('Configured')->assertSee('does not prove provider verification');
        $this->post('/'.$other->id.'/admin/settings/integrations', $this->input())->assertForbidden();
        foreach (['ga4_id' => 'G-<script>', 'gtm_id' => '<script>alert(1)</script>', 'meta_pixel_id' => '123" onload="alert(1)', 'gsc_token' => '<meta name="x">'] as $field => $value) {
            $this->postJson($path, $this->input([$field => $value]))->assertUnprocessable();
        }
        $this->postJson($path, $this->input(['ga4_id' => null]))->assertUnprocessable();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']))->get($path)->assertForbidden();
    }

    public function test_all_themes_share_tracking_config_and_gsc_is_only_on_isolated_homepages(): void
    {
        $tenant = Tenant::create(['name' => 'Own']);
        $other = Tenant::create(['name' => 'Other']);
        $settings = $this->settings($tenant);
        $settings->update(['gsc_enabled' => true, 'gsc_token' => 'own_verification_123456']);
        $foreign = $this->settings($other);
        $foreign->update(['ga4_id' => 'G-OTHER12345', 'gsc_enabled' => true, 'gsc_token' => 'other_verification_123']);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $response = $this->get($tenant->storefrontUrl().'/')->assertOk()->assertSee('google-site-verification')->assertSee('own_verification_123456')->assertDontSee('G-OTHER12345');
            $config = $this->configFrom($response);
            $this->assertSame((string) $tenant->id, $config['tenant']);
            $this->assertSame('G-TEST123456', $config['ga4']);
            $this->assertNull($config['gtm']);
        }
        $this->get($tenant->storefrontUrl().'/')->assertOk()->assertSee('own_verification_123456');
        $this->get($tenant->storefrontUrl())->assertOk()->assertDontSee('google-site-verification');
        $this->get('/v1?tenant_id='.$tenant->id)->assertOk()->assertDontSee('google-site-verification');
        $this->get($tenant->storefrontUrl().'/about')->assertOk()->assertDontSee('google-site-verification');
        $this->get('/')->assertOk()->assertDontSee('google-site-verification');
        $this->get($other->storefrontUrl().'/')->assertOk()->assertSee('other_verification_123')->assertDontSee('own_verification_123456');
    }

    public function test_tracking_excludes_previews_search_and_private_contexts_and_checkout_excludes_gtm_meta(): void
    {
        $tenant = Tenant::create(['name' => 'Own']);
        $this->settings($tenant);
        foreach (['?preview=1&preview_theme=template_2', '/all-products?q=private@example.com', '/all-products?customer_name=Buyer'] as $suffix) {
            $response = $this->get($tenant->storefrontUrl().$suffix)->assertOk();
            $this->assertNull($this->configFrom($response));
        }
        $response = $this->get($tenant->storefrontUrl())->assertOk();
        $home = $this->configFrom($response);
        $checkout = $this->configFrom($this->get($tenant->storefrontUrl().'/checkout')->assertOk());
        $this->assertNull($checkout['meta']);
        $this->assertNull($checkout['gtm']);
        $this->assertSame($home['consentScope'], $checkout['consentScope']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']));
        $this->get('/account/orders?store='.$tenant->slug)->assertOk()->assertDontSee('metohub-tracking-config');
    }

    public function test_gtm_requires_proven_current_custom_host_entitlement_host_only_sessions_and_exclusive_mode(): void
    {
        config(['app.url' => 'https://platform.example.com', 'session.domain' => null]);
        $tenant = Tenant::create(['name' => 'Own', 'domain' => 'shop.example.org']);
        $settings = $this->settings($tenant);
        $settings->forceFill(['mode' => 'gtm', 'gtm_enabled' => true, 'gtm_id' => 'GTM-TEST1234'])->save();
        $this->assertNull($this->configFrom($this->get('https://shop.example.org/')->assertOk()));
        $settings->forceFill(['verified_domain' => 'shop.example.org', 'domain_token' => 'token', 'domain_verified_at' => now()])->save();
        $config = $this->configFrom($this->get('https://shop.example.org/')->assertOk());
        $this->assertSame('GTM-TEST1234', $config['gtm']);
        $this->assertNull($config['ga4']);
        $this->assertNull($config['meta']);
        $this->assertNull($this->configFrom($this->get('https://platform.example.com/store/'.$tenant->slug)->assertOk()));
        $this->assertNull($this->configFrom($this->get('https://shop.example.org/store/'.$tenant->slug.'/checkout')->assertOk()));
        config(['session.domain' => '.example.org']);
        $this->assertNull($this->configFrom($this->get('https://shop.example.org/')->assertOk()));
        config(['session.domain' => null]);
        $settings->forceFill(['domain_verified_at' => now()->subDays(31)])->save();
        $this->assertNull($this->configFrom($this->get('https://shop.example.org/')->assertOk()));
    }

    public function test_dns_proof_is_scoped_to_exact_domain_and_cannot_verify_platform(): void
    {
        config(['app.url' => 'https://platform.example.com']);
        $tenant = Tenant::create(['name' => 'Own', 'domain' => 'shop.example.org']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $path = '/'.$tenant->id.'/admin/settings/integrations/domain';
        $this->post($path.'/challenge')->assertRedirect()->assertSessionHasNoErrors();
        $settings = StoreIntegration::firstOrFail();
        $this->assertSame('shop.example.org', $settings->verified_domain);
        $this->mock(IntegrationDomainVerification::class, function ($mock) use ($settings) {
            $mock->makePartial()->shouldReceive('records')->with('shop.example.org')->once()->andReturn([['txt' => 'metohub='.$settings->domain_token]]);
        });
        $this->post($path.'/verify')->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNotNull($settings->fresh()->domain_verified_at);
        $tenant->update(['domain' => 'changed.example.org']);
        $this->postJson($path.'/verify')->assertUnprocessable();
        $tenant->update(['domain' => 'platform.example.com']);
        $this->postJson($path.'/challenge')->assertUnprocessable();
    }

    public function test_merchants_cannot_claim_the_platform_host_and_reserved_rows_are_ignored(): void
    {
        config(['app.url' => 'https://platform.example.com']);
        $tenant = Tenant::create(['name' => 'Own']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $this->postJson('/'.$tenant->id.'/admin/settings/domain', ['domain' => 'platform.example.com'])->assertUnprocessable();
        $tenant->update(['domain' => 'platform.example.com']);
        $settings = $this->settings($tenant);
        $settings->update(['gsc_enabled' => true, 'gsc_token' => 'must_not_appear_on_platform']);
        $this->get('https://platform.example.com/')->assertOk()->assertDontSee('must_not_appear_on_platform')->assertDontSee('metohub-tracking-config');
    }

    public function test_analytics_failure_does_not_break_storefront_or_cart_mutations(): void
    {
        $tenant = Tenant::create(['name' => 'Own']);
        $this->settings($tenant);
        $this->mock(\App\Services\StorefrontTracking::class, function ($mock) {
            $mock->makePartial()->shouldReceive('snapshot')->andThrow(new \RuntimeException('Snapshot unavailable'));
        });
        $this->get($tenant->storefrontUrl())->assertOk()->assertDontSee('metohub-tracking-config');
        $product = Product::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'title' => 'Shirt', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => 'M', 'price' => 350, 'stock' => 10]);
        $this->postJson('/cart/add?store='.$tenant->slug, ['id' => $product->id, 'variant_id' => $variant->id, 'size' => 'M', 'quantity' => 1])->assertOk()->assertJsonPath('success', true)->assertJsonMissingPath('tracking');
    }

    public function test_dns_lookup_errors_preserve_proof_but_missing_records_invalidate_it(): void
    {
        $tenant = Tenant::create(['name' => 'Own', 'domain' => 'shop.example.org']);
        $settings = $this->settings($tenant);
        $settings->forceFill(['verified_domain' => $tenant->domain, 'domain_token' => 'verified-token', 'domain_verified_at' => now()])->save();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $this->mock(IntegrationDomainVerification::class, function ($mock) {
            $mock->makePartial()->shouldReceive('records')->once()->andThrow(\Illuminate\Validation\ValidationException::withMessages(['domain' => 'DNS lookup failed. Please retry.']));
        });
        $path = '/'.$tenant->id.'/admin/settings/integrations/domain/verify';
        $this->postJson($path)->assertUnprocessable()->assertJsonValidationErrors('domain');
        $this->assertNotNull($settings->fresh()->domain_verified_at);
        $this->mock(IntegrationDomainVerification::class, function ($mock) {
            $mock->makePartial()->shouldReceive('records')->once()->andReturn([]);
        });
        $this->postJson($path)->assertUnprocessable()->assertJsonValidationErrors('domain');
        $this->assertNull($settings->fresh()->domain_verified_at);
    }

    public function test_domain_changes_clear_dns_proof_and_reverting_does_not_restore_it(): void
    {
        $tenant = Tenant::create(['name' => 'Own', 'domain' => 'first.example.org']);
        $settings = $this->settings($tenant);
        $settings->forceFill(['verified_domain' => $tenant->domain, 'domain_token' => 'verified-token', 'domain_verified_at' => now()])->save();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $path = '/'.$tenant->id.'/admin/settings/domain';
        $this->post($path, ['domain' => 'first.example.org'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNotNull($settings->fresh()->domain_verified_at);
        $this->post($path, ['domain' => 'second.example.org'])->assertRedirect()->assertSessionHasNoErrors();
        foreach (['verified_domain', 'domain_token', 'domain_verified_at'] as $field) {
            $this->assertNull($settings->fresh()->getAttribute($field));
        }
        $this->post($path, ['domain' => 'first.example.org'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse(app(IntegrationDomainVerification::class)->eligible($tenant->fresh(), $settings->fresh()));
    }

    public function test_only_successful_cart_mutations_emit_actual_variant_quantities_and_prices(): void
    {
        $tenant = Tenant::create(['name' => 'Own', 'currency' => 'INR']);
        $this->settings($tenant);
        $product = Product::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'title' => 'Shirt', 'status' => 'active', 'min_order_qty' => 2]);
        $variant = $product->variants()->create(['size' => 'M', 'price' => 350, 'stock' => 10]);
        $url = '/cart/add?store='.$tenant->slug;
        $response = $this->postJson($url, ['id' => $product->id, 'variant_id' => $variant->id, 'size' => 'M', 'quantity' => 1, 'price' => 1])->assertOk();
        $response->assertJsonPath('tracking.events.0.name', 'add_to_cart')->assertJsonPath('tracking.events.0.items.0.quantity', 2)->assertJsonPath('tracking.events.0.items.0.price', 350)->assertJsonPath('tracking.events.0.value', 700);
        $key = $product->id.'-M';
        $this->postJson('/cart/update?store='.$tenant->slug, ['id' => $key, 'quantity' => 3])->assertOk()->assertJsonPath('tracking.events.0.items.0.quantity', 1);
        $this->postJson('/cart/remove?store='.$tenant->slug, ['id' => $key])->assertOk()->assertJsonPath('tracking.events.0.name', 'remove_from_cart')->assertJsonPath('tracking.events.0.items.0.quantity', 3);
        $this->postJson($url, ['id' => 99999, 'quantity' => 1])->assertNotFound()->assertJsonMissingPath('tracking');
    }

    public function test_cod_order_payload_is_not_a_purchase_and_has_no_customer_fields(): void
    {
        Mail::fake();
        $this->mock(WhatsAppService::class, fn ($mock) => $mock->shouldReceive('sendOrderNotification')->andReturn(true));
        $tenant = Tenant::create(['name' => 'Own']);
        $this->settings($tenant);
        $product = Product::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'title' => 'Shirt', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => 'M', 'price' => 350, 'stock' => 10]);
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [['type' => 'product', 'product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 1, 'price' => 1, 'size' => 'M']]]);
        $response = $this->postJson('/order/place', ['name' => 'Private Buyer', 'email' => 'private@example.com', 'phone' => '1234567890', 'address' => 'Private Address', 'city' => 'City', 'state' => 'State', 'pincode' => '123456', 'delivery_date' => now()->addDay()->toDateString(), 'delivery_time_slot' => 'Morning', 'payment_method' => 'cod'])->assertOk();
        $response->assertJsonPath('tracking.events.0.name', 'order_placed')->assertJsonPath('tracking.events.0.value', 350);
        $encoded = json_encode($response->json('tracking'));
        foreach (['Private Buyer', 'private@example.com', 'Private Address', 'customer', 'purchase'] as $private) {
            $this->assertStringNotContainsString($private, $encoded);
        }
    }
}
