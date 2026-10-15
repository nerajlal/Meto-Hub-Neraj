<?php

namespace Tests\Feature;

use App\Mail\MerchantVerificationMail;
use App\Models\Tenant;
use App\Models\TenantPaymentSetting;
use App\Models\User;
use App\Services\MerchantVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LaunchReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function signup(): array
    {
        return ['name' => 'Merchant', 'business' => 'Fresh Store', 'country' => 'India',
            'email' => 'merchant@example.com', 'whatsapp' => '+91 1234567890',
            'password' => 'secure-password', 'plan' => 'tree', 'theme' => 'template_6'];
    }

    private function verificationUrl(): string
    {
        $url = '';
        Mail::assertSent(MerchantVerificationMail::class, function ($mail) use (&$url) {
            $url = $mail->verificationUrl;

            return $mail->hasTo('merchant@example.com');
        });

        return $url;
    }

    public function test_signup_sends_private_single_use_verification_and_requires_password_login(): void
    {
        Mail::fake();
        config(['app.url' => 'https://betametohub.metora.in']);
        $this->postJson('/saas/register', $this->signup())->assertCreated()
            ->assertJson(['success' => true, 'email_sent' => true])->assertJsonMissingPath('verify_url');
        $user = User::withoutGlobalScopes()->firstOrFail();
        $this->assertTrue($user->email_verification_required);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('base', $user->tenant->plan);
        $this->assertSame('template_6', $user->tenant->theme);
        $this->assertSame('merchant@example.com', $user->tenant->contact_email);
        $this->assertSame('+91 1234567890', $user->tenant->contact_phone);
        $this->assertSame('911234567890', $user->tenant->whatsapp_number);
        $url = $this->verificationUrl();
        $this->assertStringStartsWith('https://betametohub.metora.in/', $url);
        $this->get($url)->assertRedirect(route('admin.common.login'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->email_verification_token_hash);
        $this->assertGuest();
        $this->get($url)->assertForbidden();
    }

    public function test_unsigned_tampered_and_expired_verification_links_are_rejected(): void
    {
        Mail::fake();
        $this->postJson('/saas/register', $this->signup())->assertCreated();
        $user = User::withoutGlobalScopes()->firstOrFail();
        $url = $this->verificationUrl();
        $this->get('/saas/verify/'.$user->id.'/'.sha1($user->email))->assertForbidden();
        $this->get($url.'&unexpected=value')->assertForbidden();
        $this->travel(61)->minutes();
        $this->get($url)->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertGuest();
    }

    public function test_resend_invalidates_old_link_and_does_not_expose_new_link(): void
    {
        Mail::fake();
        $this->postJson('/saas/register', $this->signup())->assertCreated();
        $old = $this->verificationUrl();
        Mail::fake();
        $this->postJson('/saas/verification/resend', ['email' => 'merchant@example.com'])
            ->assertOk()->assertJsonMissingPath('verify_url');
        $new = $this->verificationUrl();
        $this->assertNotSame($old, $new);
        $this->get($old)->assertForbidden();
        $this->get($new)->assertRedirect();
    }

    public function test_mail_failure_is_reported_honestly_and_account_remains_unverified(): void
    {
        $this->mock(MerchantVerification::class, function ($mock) {
            $mock->shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        });
        $this->postJson('/saas/register', $this->signup())->assertCreated()
            ->assertJson(['success' => true, 'email_sent' => false])->assertJsonMissingPath('verify_url');
        $this->assertDatabaseHas('users', ['email' => 'merchant@example.com', 'email_verified_at' => null]);
        $this->assertGuest();
    }

    public function test_unverified_merchant_cannot_sign_in_or_bypass_dashboard_guard(): void
    {
        Mail::fake();
        $this->postJson('/saas/register', $this->signup())->assertCreated();
        $user = User::withoutGlobalScopes()->firstOrFail();
        $this->post(route('admin.common.login.submit'), ['email' => $user->email, 'password' => 'secure-password'])
            ->assertRedirect(route('admin.common.login'))->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->getJson("/{$user->tenant_id}/admin/products")->assertForbidden();
    }

    public function test_resend_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/saas/verification/resend', ['email' => 'unknown@example.com'])->assertOk();
        }
        $this->postJson('/saas/verification/resend', ['email' => 'unknown@example.com'])->assertStatus(429);
    }

    public function test_retired_zoho_routes_are_absent(): void
    {
        foreach (Route::getRoutes() as $route) {
            $this->assertStringNotContainsString('zoho', strtolower($route->uri()));
        }
    }

    public function test_online_checkout_and_disabled_cod_cannot_create_orders(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Store', 'theme' => 'template_1']);
        $this->withSession(['active_tenant_id' => $tenant->id])->postJson('/order/place', ['payment_method' => 'online'])
            ->assertUnprocessable()->assertJsonValidationErrors('payment_method');
        TenantPaymentSetting::create(['tenant_id' => $tenant->id, 'cod_enabled' => false]);
        $this->postJson('/order/place', ['payment_method' => 'cod'])
            ->assertUnprocessable()->assertJsonValidationErrors('payment_method');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_gateway_settings_cannot_enable_unimplemented_payments(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Store', 'theme' => 'template_1']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($admin)->postJson("/{$tenant->id}/admin/settings/payment", ['razorpay_enabled' => true])
            ->assertUnprocessable()->assertJsonValidationErrors('razorpay_enabled');
        $this->post("/{$tenant->id}/admin/settings/payment", ['cod_enabled' => '0'])->assertRedirect();
        $this->assertDatabaseHas('tenant_payment_settings', ['tenant_id' => $tenant->id, 'cod_enabled' => false, 'razorpay_enabled' => false]);
    }

    public function test_every_theme_pauses_checkout_when_cod_is_disabled(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Store', 'theme' => 'template_1']);
        $product = \App\Models\Product::create(['title' => 'Rice', 'status' => 'active', 'tenant_id' => $tenant->id]);
        $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        TenantPaymentSetting::create(['tenant_id' => $tenant->id, 'cod_enabled' => false]);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [
                'rice' => ['product_id' => $product->id, 'size' => '1kg', 'type' => 'product', 'quantity' => 1, 'price' => 100, 'name' => 'Rice'],
            ]])->get('/v1/checkout')->assertOk()->assertSee('Ordering is temporarily unavailable.')
                ->assertSee('disabled class="btn-complete-order"', false)->assertDontSee('value="online"', false);
        }
    }

    public function test_signup_ui_uses_supported_plans_and_all_six_themes(): void
    {
        $response = $this->get('/admin/login')->assertOk()->assertSee('Resend verification link');
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $response->assertSee('value="'.$theme.'"', false);
        }
        $response->assertSee('value="base"', false)->assertDontSee('Simulate Email Verification Link')
            ->assertDontSee('value="sprout"', false);
    }

    public function test_route_names_are_unique_for_production_caching(): void
    {
        $names = [];
        foreach (Route::getRoutes() as $route) {
            if ($name = $route->getName()) $names[] = $name;
        }
        $this->assertCount(count(array_unique($names)), $names);
    }

    public function test_production_verification_refuses_log_only_email_delivery(): void
    {
        $this->app['env'] = 'production';
        config(['mail.default' => 'log']);
        $user = User::factory()->make(['id' => 1]);
        $this->expectException(\RuntimeException::class);
        app(MerchantVerification::class)->send($user);
    }

    public function test_preflight_fails_for_debug_and_non_delivery_mailer(): void
    {
        config(['app.env' => 'production', 'app.debug' => true, 'mail.default' => 'log']);
        $this->artisan('app:preflight')->expectsOutput('APP_DEBUG must be false.')
            ->expectsOutput('Use a real delivery mailer; log/array and fallback logging cannot verify email delivery.')
            ->assertExitCode(1);
    }
}
