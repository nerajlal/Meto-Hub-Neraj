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
            $this->assertSame(1, preg_match_all('/<h1\\b/i', $html), $path.' needs one main page heading');
            preg_match('/<header data-marketing-header.*?<\/header>/s', $html, $pageHeader);
            preg_match('/<footer data-marketing-footer.*?<\/footer>/s', $html, $pageFooter);
            $this->assertSame($normalizeHeader($header[0]), $normalizeHeader($pageHeader[0] ?? ''), $path);
            $this->assertSame($footer[0], $pageFooter[0] ?? '', $path);
        }
    }

    public function test_public_seo_is_unique_canonical_and_has_valid_safe_structured_data(): void
    {
        config(['app.url' => 'https://metohub.cloud', 'marketing.seo.google_site_verification' => 'example-verification']);
        $titles = [];
        foreach (['/', '/pricing', '/themes', '/about', '/contact', '/privacy-policy', '/terms', '/refund-cancellation'] as $path) {
            $html = $this->get($path.'?utm_source=instagram')->assertOk()->getContent();
            preg_match('/<title>(.*?)<\/title>/s', $html, $title);
            $titles[] = $title[1];
            $canonical = 'https://metohub.cloud'.($path === '/' ? '' : $path);
            $this->assertStringContainsString('rel="canonical" href="'.$canonical.'"', $html);
            $this->assertStringContainsString('name="google-site-verification" content="example-verification"', $html);
            $this->assertStringContainsString('name="twitter:description"', $html);
            $this->assertStringContainsString('https://metohub.cloud/images/landing/social-card.png', $html);
            $this->assertStringNotContainsString('images/og-image.jpg', $html);
            preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $schemas);
            $this->assertCount(1, $schemas[1]);
            $schema = json_decode($schemas[1][0], true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame('metohub@metora.in', $schema['@graph'][0]['email']);
            $this->assertSame($canonical, $schema['@graph'][2]['url']);
            $this->assertStringNotContainsString('AggregateRating', $schemas[1][0]);
        }
        $this->assertCount(count($titles), array_unique($titles));
        $image = getimagesize(public_path('images/landing/social-card.png'));
        $this->assertSame([1200, 630], [$image[0], $image[1]]);
    }

    public function test_staging_and_signup_queries_have_consistent_noindex_rules(): void
    {
        $this->get('/?get_started=1')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('name="robots" content="noindex, nofollow"', false);
        config(['search.indexable' => false]);
        foreach (['/', '/about', '/contact'] as $path) {
            $this->get($path)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')
                ->assertSee('name="robots" content="noindex, nofollow"', false);
        }
        $this->get('/robots.txt')->assertSee('Disallow: /', false);
    }

    public function test_platform_sitemap_includes_public_blog_and_policies_without_private_urls(): void
    {
        config(['app.url' => 'https://metohub.cloud', 'search.indexable' => true]);
        $xml = $this->get('/sitemap-platform.xml')->assertOk()->streamedContent();
        $this->assertNotFalse(simplexml_load_string($xml));
        foreach (['/blogs', '/refund-cancellation', '/security'] as $path) {
            $this->assertStringContainsString('https://metohub.cloud'.$path, $xml);
        }
        $this->assertStringNotContainsString('/admin', $xml);
        $this->assertStringNotContainsString('get_started', $xml);
    }

    public function test_dynamic_blog_sitemap_shares_the_public_feed_cache_and_uses_local_canonicals(): void
    {
        config(['app.url' => 'https://metohub.cloud', 'services.blog.key' => 'sitemap-test', 'search.indexable' => true]);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        $article = ['id' => 42, 'title' => 'A & B', 'content' => '<p>Collection launch.</p>', 'updated_at' => '2020-02-03T12:30:00Z'];
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['blogs' => [$article, $article, ['id' => 'invalid', 'title' => 'Skip']]])]);
        $this->get('/blogs')->assertOk();
        $xml = $this->get('/sitemap-blogs.xml')->assertOk()->streamedContent();
        $this->assertNotFalse(simplexml_load_string($xml));
        $this->assertSame(1, substr_count($xml, '<loc>https://metohub.cloud/blogs/42</loc>'));
        $this->assertStringContainsString('<lastmod>2020-02-03T12:30:00Z</lastmod>', $xml);
        $this->assertStringNotContainsString('blog.metora.in', $xml);
        $this->assertStringNotContainsString('invalid', $xml);
        \Illuminate\Support\Facades\Http::assertSentCount(1);
        $index = $this->get('/sitemap.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('/sitemap-blogs.xml', $index);
        $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: https://metohub.cloud/sitemap.xml', false);
    }

    public function test_blog_sitemap_refreshes_with_new_and_removed_feed_entries(): void
    {
        config(['services.blog.key' => 'refresh-test', 'search.indexable' => true]);
        \Illuminate\Support\Facades\Http::fakeSequence()
            ->push(['blogs' => [['id' => 11, 'title' => 'First post']]])
            ->push(['blogs' => [['id' => 12, 'title' => 'New post', 'updated_at' => 'invalid-date']]]);
        $this->assertStringContainsString('/blogs/11', $this->get('/sitemap-blogs.xml')->streamedContent());
        $this->travel(6)->minutes();
        $xml = $this->get('/sitemap-blogs.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('/blogs/12', $xml);
        $this->assertStringNotContainsString('/blogs/11', $xml);
        $this->assertStringNotContainsString('<lastmod>', $xml);
    }

    public function test_blog_sitemap_uses_saved_feed_then_returns_retryable_error_when_unavailable(): void
    {
        config(['services.blog.key' => 'outage-test', 'search.indexable' => true]);
        \Illuminate\Support\Facades\Http::fakeSequence()
            ->push(['blogs' => [['id' => 9, 'title' => 'Saved post']]])->pushStatus(503)->pushStatus(503);
        $this->get('/sitemap-blogs.xml')->assertOk()->streamedContent();
        $this->travel(6)->minutes();
        $xml = $this->get('/sitemap-blogs.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('/blogs/9', $xml);
        $this->travel(2)->days();
        $this->get('/sitemap-blogs.xml')->assertStatus(503)->assertHeader('Retry-After', '3600');
        $this->get('/sitemap-platform.xml')->assertOk();
        $this->get('/robots.txt')->assertOk();
        \Illuminate\Support\Facades\Http::assertSentCount(3);
    }

    public function test_empty_and_disabled_blog_sitemaps_do_not_publish_fake_urls_or_fetch_on_staging(): void
    {
        config(['services.blog.key' => 'empty-test', 'search.indexable' => true]);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['blogs' => []])]);
        $xml = $this->get('/sitemap-blogs.xml')->assertOk()->streamedContent();
        $this->assertNotFalse(simplexml_load_string($xml));
        $this->assertStringNotContainsString('<url>', $xml);
        \Illuminate\Support\Facades\Http::fake();
        config(['search.indexable' => false]);
        $this->get('/sitemap-blogs.xml')->assertNotFound();
        \Illuminate\Support\Facades\Http::assertNothingSent();
        config(['search.indexable' => true, 'services.blog.key' => null]);
        $xml = $this->get('/sitemap.xml')->assertOk()->streamedContent();
        $this->assertFileDoesNotExist(public_path('sitemap.xml'));
        $this->assertFileDoesNotExist(public_path('robots.txt'));
        $this->assertStringNotContainsString('/sitemap-blogs.xml', $xml);
        $this->get('/sitemap-blogs.xml')->assertStatus(503);
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_legacy_marketing_urls_redirect_and_about_uses_configured_facts(): void
    {
        foreach (['/home', '/home1'] as $path) {
            $this->get($path.'?get_started=1')->assertStatus(301)
                ->assertRedirect(route('landing', ['get_started' => 1]));
        }
        $this->get('/templates')->assertStatus(301)->assertRedirect(route('landing.themes'));
        config(['saas.plans.base.monthly_price' => 129, 'saas.trial_days' => 9]);
        $this->get('/about')->assertOk()->assertSee('₹129')->assertSee('9 days')
            ->assertDontSee('2M+')->assertDontSee('99.9%')->assertDontSee('Stores Powered');
    }

    public function test_public_refund_policy_matches_the_seven_day_trial_and_paid_refund_window(): void
    {
        $this->get('/refund-cancellation')->assertOk()->assertSee('7-day free trial')
            ->assertSee('within 7 days of your first paid')
            ->assertSee('separately from the free trial')->assertDontSee('14-day')
            ->assertDontSee('grace period of 30 days');
        $this->get('/admin/login')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('name="robots" content="noindex, nofollow"', false)
            ->assertSee('<title>Merchant Login | MetoHub</title>', false);
    }

}
