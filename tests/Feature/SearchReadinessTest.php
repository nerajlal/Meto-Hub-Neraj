<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchReadinessTest extends TestCase
{
    use RefreshDatabase;

    private function product(Tenant $tenant, array $extra = []): Product
    {
        return Product::withoutGlobalScopes()->create($extra + ['tenant_id' => $tenant->id, 'title' => 'Shirt "blue" </script>', 'description' => 'Cotton & linen', 'status' => 'active']);
    }

    public function test_store_search_settings_are_validated_isolated_and_used_by_all_themes(): void
    {
        $tenant = Tenant::create(['name' => 'Our Store']);
        $other = Tenant::create(['name' => 'Other']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $path = '/'.$tenant->id.'/admin/settings/seo';
        $this->get($path)->assertOk()->assertSee('Search Readiness')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->post($path, ['seo_title' => 'Our unique title', 'seo_description' => 'Selected for you', 'seo_image' => 'https://example.com/social.jpg', 'search_indexable' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($other->fresh()->seo_title);
        $this->post('/'.$other->id.'/admin/settings/seo', ['search_indexable' => 1])->assertForbidden();
        $this->postJson($path, ['seo_title' => str_repeat('a', 121), 'seo_image' => 'javascript:alert(1)', 'search_indexable' => 1])->assertUnprocessable();
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get($tenant->storefrontUrl())->assertOk()->assertSee('<title>Our unique title</title>', false)->assertSee('content="Selected for you"', false)->assertSee('https://example.com/social.jpg', false);
        }
    }

    public function test_product_identity_safe_schema_and_real_availability_across_all_themes(): void
    {
        $tenant = Tenant::create(['name' => 'Our Store', 'currency' => 'USD']);
        $product = $this->product($tenant, ['seo_title' => 'Blue shirt search title']);
        $product->variants()->create(['size' => 'M', 'price' => 25, 'stock' => 0]);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $url = $tenant->storefrontUrl().'/product?id='.$product->id;
            $response = $this->get($url.'&utm_source=instagram')->assertOk()->assertSee('<title>Blue shirt search title</title>', false);
            $response->assertSee('rel="canonical" href="'.$url.'"', false);
            preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
            $this->assertCount(1, $matches[1]);
            $schema = json_decode($matches[1][0], true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame($product->title, $schema['@graph'][1]['name']);
            $this->assertSame('USD', $schema['@graph'][1]['offers']['priceCurrency']);
            $this->assertSame('https://schema.org/OutOfStock', $schema['@graph'][1]['offers']['availability']);
        }
        $product->variants()->update(['stock' => 3]);
        $this->get($url)->assertSee('https://schema.org/InStock', false);
    }

    public function test_sitemaps_only_include_public_current_tenant_catalog_and_escape_xml(): void
    {
        $tenant = Tenant::create(['name' => 'Visible']);
        $other = Tenant::create(['name' => 'Hidden', 'search_indexable' => false]);
        $active = $this->product($tenant);
        $draft = $this->product($tenant, ['status' => 'draft']);
        $foreign = $this->product($other);
        $collection = Collection::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'name' => 'A & B', 'slug' => 'a-b', 'status' => true]);
        Collection::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'name' => 'Draft', 'slug' => 'draft', 'status' => false]);
        $index = $this->get('/sitemap.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('/store/'.$tenant->slug.'/sitemap/products/1.xml', $index);
        $this->assertStringNotContainsString('/store/'.$other->slug.'/', $index);
        $xml = $this->get('/store/'.$tenant->slug.'/sitemap/products/1.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('/product?id='.$active->id, $xml);
        $this->assertStringNotContainsString('/product?id='.$draft->id, $xml);
        $this->assertStringNotContainsString('/product?id='.$foreign->id, $xml);
        $this->assertNotFalse(simplexml_load_string($xml));
        $collections = $this->get('/store/'.$tenant->slug.'/sitemap/collections/1.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString('slug=a-b', $collections);
        $this->assertStringNotContainsString('slug=draft', $collections);
        $this->get('/store/'.$tenant->slug.'/collections?slug=draft')->assertNotFound();
        $this->get('/store/'.$tenant->slug.'/collections?slug=missing')->assertNotFound();
        $this->get('/store/'.$tenant->slug.'/sitemap/products/2.xml')->assertNotFound();
    }

    public function test_noindex_store_preview_staging_and_private_pages(): void
    {
        $tenant = Tenant::create(['name' => 'No index', 'search_indexable' => false]);
        $this->get($tenant->storefrontUrl())->assertOk()->assertSee('content="noindex, follow"', false);
        $this->get('/store/'.$tenant->slug.'/sitemap/products/1.xml')->assertNotFound();
        $tenant->update(['search_indexable' => true]);
        $this->get($tenant->storefrontUrl().'?preview=1&preview_theme=template_2')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get('/robots.txt')->assertOk()->assertDontSee('goslot')->assertSee('/sitemap.xml');
        config(['search.indexable' => false]);
        $this->get('/robots.txt')->assertSee('Disallow: /', false);
        $this->assertStringNotContainsString('/store/', $this->get('/sitemap.xml')->streamedContent());
        $this->get($tenant->storefrontUrl())->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_catalog_overrides_save_and_home_metadata_does_not_use_loop_variables(): void
    {
        $tenant = Tenant::create(['name' => 'Catalog Store', 'seo_title' => 'Our homepage']);
        $other = Tenant::create(['name' => 'Other']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $prefix = '/'.$tenant->id.'/admin';
        $fields = ['seo_title' => 'Custom catalog title', 'seo_description' => 'Useful catalog details', 'seo_image' => 'https://example.com/catalog.jpg'];
        $this->post($prefix.'/collections', $fields + ['name' => 'Shirts', 'status' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $collection = Collection::where('tenant_id', $tenant->id)->firstOrFail();
        $this->assertSame($fields['seo_title'], $collection->seo_title);
        $product = $this->product($tenant, ['collection_id' => $collection->id]);
        $this->put($prefix.'/products/'.$product->id, $fields + ['title' => $product->title, 'status' => 'active'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame($fields['seo_title'], $product->fresh()->seo_title);
        $this->get($prefix.'/products/'.$product->id.'/edit')->assertOk()->assertSee('name="seo_description"', false);
        $this->get($prefix.'/collections/'.$collection->id.'/edit')->assertOk()->assertSee('name="seo_description"', false);
        $this->get($tenant->storefrontUrl())->assertOk()->assertSee('<title>Our homepage</title>', false)->assertDontSee('<title>Custom catalog title</title>', false);
        $response = $this->get($tenant->storefrontUrl().'/collections?slug='.$collection->slug)->assertOk()->assertSee('<title>Custom catalog title</title>', false);
        $response->assertSee('rel="canonical" href="'.$tenant->storefrontUrl().'/collections?slug='.$collection->slug.'"', false);
        $foreign = $this->product($other);
        $this->put($prefix.'/products/'.$foreign->id, $fields)->assertNotFound();
    }

    public function test_legacy_theme_links_keep_product_and_collection_identity(): void
    {
        $tenant = Tenant::create(['name' => 'Legacy Store']);
        $product = $this->product($tenant);
        $collection = Collection::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'name' => 'Shirts', 'slug' => 'shirts', 'status' => true]);
        foreach (['/v1/product?id='.$product->id, '/v2/product/'.$product->id.'?x=1', '/v3/product?id='.$product->id, '/v4/product?id='.$product->id, '/v5/product?id='.$product->id] as $url) {
            $join = str_contains($url, '?') ? '&' : '?';
            $this->get($url.$join.'tenant_id='.$tenant->id)->assertOk()->assertSee('rel="canonical" href="'.$tenant->storefrontUrl().'/product?id='.$product->id.'"', false);
        }
        $this->get('/v2/collection/shirts?tenant_id='.$tenant->id)->assertOk()->assertSee('rel="canonical" href="'.$tenant->storefrontUrl().'/collections?slug=shirts"', false);
    }

    public function test_custom_host_sitemap_and_canonical_never_leak_other_tenants(): void
    {
        $tenant = Tenant::create(['name' => 'Custom', 'domain' => 'shop.example.com']);
        $other = Tenant::create(['name' => 'Other']);
        $product = $this->product($tenant);
        $this->get('https://shop.example.com/')->assertOk()->assertSee('rel="canonical" href="https://shop.example.com"', false);
        $index = $this->get('https://shop.example.com/sitemap.xml')->assertOk()->streamedContent();
        $this->assertStringNotContainsString('/store/'.$other->slug.'/', $index);
        $this->assertStringContainsString('https://shop.example.com/store/'.$tenant->slug.'/sitemap/', $index);
        $this->get('https://shop.example.com/store/'.$other->slug.'/sitemap.xml')->assertNotFound();
        $this->get('https://shop.example.com/store/'.$tenant->slug.'/product?id='.$product->id)->assertOk()->assertSee('rel="canonical" href="https://shop.example.com/store/'.$tenant->slug.'/product?id='.$product->id.'"', false);
    }
}
