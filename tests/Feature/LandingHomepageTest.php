<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LandingHomepageTest extends TestCase
{
    use DatabaseMigrations;

    public function runDatabaseMigrations()
    {
        $this->refreshTestDatabase();
        $this->beforeApplicationDestroyed(fn () => \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = false);
    }

    public function test_homepage_uses_configured_prices_and_has_an_empty_demo_fallback(): void
    {
        config(['saas.plans.base.monthly_price' => 129]);
        $this->get('/')->assertOk()->assertSee('₹129')->assertSee('Explore themes')->assertDontSee('/store/mens-clothing')->assertSee('cash on delivery')->assertDontSee('Global edge caching');
    }

    public function test_homepage_only_links_available_curated_demo_stores_and_real_signup_triggers(): void
    {
        $tenant = Tenant::create(['name' => 'Thread & Form', 'theme' => 'template_7']);
        $tenant->forceFill(['slug' => 'mens-clothing'])->save();
        Tenant::create(['name' => 'Private Merchant']);
        $this->get('/')->assertOk()->assertSee($tenant->storefrontUrl(), false)->assertDontSee('/store/grocery')->assertDontSee('Private Merchant')->assertSee('data-plan="base"', false)->assertSee('data-plan="pro"', false)->assertSee('data-plan="enterprise"', false)->assertSee('Explore stores')->assertSee('No coding needed')->assertSee('monthly subscriptions')->assertDontSee('Watch Demo');
    }
}
