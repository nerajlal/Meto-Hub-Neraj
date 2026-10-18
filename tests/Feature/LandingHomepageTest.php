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
    public function test_signup_uses_existing_saas_routes_and_all_configured_themes(): void
    {
        $response = $this->get('/')->assertOk()
            ->assertSee('action="'.route('saas.register').'"', false)
            ->assertSee('name="country"', false)->assertSee('name="business"', false)
            ->assertSee('name="whatsapp"', false)->assertSee('name="password_confirmation"', false)
            ->assertSee(route('admin.common.login'), false)
            ->assertDontSee('Switch to dark theme')->assertDontSee('data-live');
        preg_match('/<script id="landing-config" type="application\/json">(.*?)<\/script>/s', $response->getContent(), $matches);
        $configuration = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(route('saas.verification.resend'), $configuration['resend']);
        $this->assertSame(route('saas.register'), $configuration['register']);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $response->assertSee('value="'.$theme.'"', false);
        }
    }

    public function test_trial_and_upgrade_prices_follow_configuration(): void
    {
        config(['saas.trial_days' => 9, 'saas.plans.pro.monthly_price' => 333, 'saas.plans.enterprise.monthly_price' => 888]);
        $this->get('/')->assertOk()->assertSee('9-day free trial')->assertSee('₹333')->assertSee('₹888');
    }
    public function test_client_reviews_only_publish_complete_approved_feedback_and_escape_quotes(): void
    {
        config(['landing_reviews' => []]);
        $this->get('/')->assertOk()->assertDontSee('id="client-reviews"', false);
        config(['landing_reviews' => [
            ['approved' => true, 'name' => 'Approved Seller', 'store' => 'Example Brand', 'quote' => '<script>example</script>'],
            ['approved' => false, 'name' => 'Unapproved Seller', 'quote' => 'Private feedback'],
            ['approved' => true, 'name' => '', 'quote' => 'Incomplete feedback'],
        ]]);
        $this->get('/')->assertOk()->assertSee('id="client-reviews"', false)
            ->assertSee('Approved Seller')->assertSee('Example Brand')
            ->assertSee('&lt;script&gt;example&lt;/script&gt;', false)
            ->assertDontSee('<script>example</script>', false)
            ->assertDontSee('Unapproved Seller')->assertDontSee('Private feedback')
            ->assertDontSee('Incomplete feedback');
    }

    public function test_sample_seller_stories_are_explicitly_disclosed(): void
    {
        $this->get('/')->assertOk()->assertSee('Anjali Menon')
            ->assertSee('Fathima Nazeer')->assertSee('Arjun Nair')
            ->assertSee('Names and quotes are fictional examples, not MetoHub client reviews.')
            ->assertSee('Illustrative example')->assertDontSee('Trusted by 500');
    }

    public function test_public_pages_share_the_homepage_header_and_footer(): void
    {
        config(['services.blog.key' => 'test-key']);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['blogs' => [
            ['id' => 7, 'title' => 'Example article', 'content' => '<p>Article content.</p>'],
        ]])]);
        $homepage = $this->get('/')->assertOk()->getContent();
        preg_match('/<footer data-marketing-footer.*?<\/footer>/s', $homepage, $footer);
        preg_match('/<header data-marketing-header.*?<\/header>/s', $homepage, $header);
        $normalizeHeader = fn ($html) => preg_replace('/\s+/', ' ', str_replace('data-start="" data-plan="base"', '', $html));
        foreach (['/privacy-policy', '/terms', '/refund-cancellation', '/about', '/contact', '/features', '/solutions',
            '/pricing', '/themes', '/analytics', '/careers', '/press', '/independent-grocers', '/grocery-chains',
            '/farmers-markets', '/help-center', '/api-docs', '/guides', '/security', '/blogs', '/blogs/7'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            preg_match('/<header data-marketing-header.*?<\/header>/s', $html, $pageHeader);
            preg_match('/<footer data-marketing-footer.*?<\/footer>/s', $html, $pageFooter);
            $this->assertSame($normalizeHeader($header[0]), $normalizeHeader($pageHeader[0] ?? ''), $path);
            $this->assertSame($footer[0], $pageFooter[0] ?? '', $path);
        }
    }

}
