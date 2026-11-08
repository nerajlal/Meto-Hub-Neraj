<?php

namespace Tests\Feature;

use App\Models\{Tenant, Product};
use App\Services\StoreShareThumbnail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Storage, Http};
use Tests\TestCase;

class StoreShareThumbnailTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_theme_uses_compact_home_metadata_but_keeps_product_images(): void
    {
        $tenant = Tenant::create(['name' => 'Sharing Store', 'logo' => 'logo.png']);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'A real product', 'status' => 'active']);
        $product->images()->create(['path' => 'products/real.jpg', 'order' => 0]);
        $product->variants()->create(['price' => 100, 'stock' => 5, 'size' => 'Standard']);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get('/store/'.$tenant->slug)->assertOk()->assertSee('/social/stores/'.$tenant->slug.'/thumbnail.png?v=', false)
                ->assertSee('<meta property="og:image:width" content="200">', false)
                ->assertSee('<meta name="twitter:card" content="summary">', false);
        }
        $this->get('/store/'.$tenant->slug.'/product?id='.$product->id)->assertOk()
            ->assertSee('/storage/products/real.jpg', false)->assertSee('content="summary_large_image"', false)
            ->assertDontSee('/social/stores/'.$tenant->slug.'/thumbnail.png', false);
    }

    public function test_thumbnail_is_small_real_png_with_logo_and_has_cache_headers(): void
    {
        Storage::fake('public');
        $logo = imagecreatetruecolor(800, 400);
        imagefill($logo, 0, 0, imagecolorallocate($logo, 10, 180, 20));
        ob_start(); imagepng($logo); $bytes = ob_get_clean(); imagedestroy($logo);
        Storage::disk('public')->put('logos/test.png', $bytes);
        $tenant = Tenant::create(['name' => 'Logo shop', 'logo' => 'logos/test.png']);
        $url = route('store.share-thumbnail', ['slug' => $tenant->slug]);
        $response = $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/png');
        $dimensions = getimagesizefromstring($response->getContent());
        $this->assertSame([200, 200], array_slice($dimensions, 0, 2));
        $render = imagecreatefromstring($response->getContent());
        $this->assertSame(0x0ab414, imagecolorat($render, 100, 100));
        imagedestroy($render);
        $this->get($url, ['If-None-Match' => $response->headers->get('ETag')])->assertStatus(304);
        $old = app(StoreShareThumbnail::class)->version($tenant);
        $tenant->update(['logo' => null]);
        $this->assertNotSame($old, app(StoreShareThumbnail::class)->version($tenant));
    }

    public function test_missing_or_external_logo_uses_fallback_without_fetching_external_resources(): void
    {
        Http::preventStrayRequests();
        $tenant = Tenant::create(['name' => 'External shop', 'logo' => 'https://untrusted.example/logo.png']);
        $this->get(route('store.share-thumbnail', ['slug' => $tenant->slug]))->assertOk()->assertHeader('Content-Type', 'image/png');
        Http::assertNothingSent();
        $this->get('/social/stores/does-not-exist/thumbnail.png')->assertNotFound();
        $tenant->forceFill(['created_at' => now()->subDays(100), 'trial_ends_at' => now()->subDays(100)])->save();
        $this->get(route('store.share-thumbnail', ['slug' => $tenant->slug]))->assertNotFound();
    }
}
