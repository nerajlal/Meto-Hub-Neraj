<?php

namespace Tests\Feature;

use App\Models\StoreIntegration;
use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SettingsOverviewTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(): array
    {
        $tenant = Tenant::create(['name' => 'My Boutique', 'theme' => 'template_12', 'plan' => 'base']);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($user);
        Http::preventStrayRequests();

        return [$tenant, $user];
    }

    public function test_overview_is_read_only_and_links_to_current_store_settings(): void
    {
        [$tenant, $user] = $this->merchant();
        $writes = [];
        DB::listen(function ($query) use (&$writes) {
            if (preg_match('/^\s*(insert|update|delete|replace)\b/i', $query->sql)) $writes[] = $query->sql;
        });
        $response = $this->get("/{$tenant->id}/admin/settings")->assertOk()->assertSee('Free trial active')
            ->assertSee($tenant->name)->assertSee($user->email)->assertSee('Bloom Avenue')->assertSee('Not configured')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertDontSee('Delete Site')->assertDontSee('Tax &amp; GST', false);
        $response->assertSee(route('admin.settings.theme', ['tenant' => $tenant->id]), false)
            ->assertSee(route('admin.settings.integrations', ['tenant' => $tenant->id]).'#integration-ga4', false);
        $this->assertSame([], $writes);
        Http::assertNothingSent();
    }

    public function test_expired_trial_and_pending_payment_do_not_claim_paid_access(): void
    {
        [$tenant] = $this->merchant();
        $tenant->forceFill(['created_at' => now()->subDays(15), 'subscription_status' => 'expired'])->save();
        $this->get("/{$tenant->id}/admin/settings")->assertOk()->assertSee('Subscription required')->assertSee('Choose a plan');
        $tenant->update(['subscription_status' => 'pending']);
        $this->get("/{$tenant->id}/admin/settings")->assertOk()->assertSee('Payment pending')->assertDontSee('Paid access active');
    }

    public function test_cancelled_subscription_can_retain_paid_access_until_period_ends(): void
    {
        [$tenant] = $this->merchant();
        $tenant->forceFill(['created_at' => now()->subDays(15), 'plan' => 'pro', 'subscription_status' => 'cancelled', 'razorpay_subscription_id' => 'sub_settings'])->save();
        StoreSubscription::create(['tenant_id' => $tenant->id, 'gateway_id' => 'sub_settings', 'checkout_attempt' => 'settings-test', 'plan' => 'pro',
            'cycle' => 'monthly', 'status' => 'cancelled', 'paid_count' => 1, 'paid_from' => now()->subDays(2), 'paid_through' => now()->addDays(28), 'synced_at' => now()]);
        $this->get("/{$tenant->id}/admin/settings")->assertOk()->assertSee('Paid access active')->assertSee('Manage billing')->assertSee('Last recorded provider status: Cancelled');
    }

    public function test_integrations_are_scoped_and_saved_ids_are_not_exposed(): void
    {
        [$tenant] = $this->merchant();
        $other = Tenant::create(['name' => 'Other shop']);
        StoreIntegration::create(['tenant_id' => $other->id, 'gsc_token' => 'other-private-token', 'gsc_enabled' => true]);
        StoreIntegration::create(['tenant_id' => $tenant->id, 'mode' => 'gtm', 'ga4_id' => 'G-SECRETID', 'ga4_enabled' => true,
            'meta_pixel_id' => '12345678', 'meta_enabled' => false, 'gtm_id' => 'GTM-SECRET', 'gtm_enabled' => true]);
        $this->get("/{$tenant->id}/admin/settings")->assertOk()->assertSee('Paused in GTM mode')->assertSee('Disabled')
            ->assertSee('Configured')->assertDontSee('G-SECRETID')->assertDontSee('GTM-SECRET')->assertDontSee('other-private-token');
    }

    public function test_other_store_and_non_merchant_roles_cannot_access_overview(): void
    {
        [$tenant] = $this->merchant();
        $other = Tenant::create(['name' => 'Private shop']);
        $this->get("/{$other->id}/admin/settings")->assertForbidden();
        foreach (['user', 'delivery_boy'] as $role) {
            $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => $role]));
            $this->get("/{$tenant->id}/admin/settings")->assertForbidden();
        }
    }

    public function test_guest_is_redirected_and_integration_links_target_real_sections(): void
    {
        $tenant = Tenant::create(['name' => 'Shop']);
        $this->get("/{$tenant->id}/admin/settings")->assertRedirect();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $response = $this->get("/{$tenant->id}/admin/settings/integrations")->assertOk();
        foreach (['ga4', 'gtm', 'gsc', 'meta'] as $provider) {
            $response->assertSee('id="integration-'.$provider.'"', false);
        }
    }
}
