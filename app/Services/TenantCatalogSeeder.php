<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Slider;
use App\Models\DeliveryPartner;
use Illuminate\Support\Str;

class TenantCatalogSeeder
{
    /**
     * Seeds the initial base catalog, sliders, and delivery configurations for a newly provisioned tenant.
     * Ensure we do NOT touch existing tenant_id = 1 data.
     */
    public static function seedForTenant(int $tenantId): void
    {
        // Seeding disabled for now as default sample data is not required.
        return;

        // 1. Seed Collections
        $collectionsData = [
            [
                'name' => 'Signature Collection',
                'description' => 'Our most exclusive and premium fragrances.',
                'image' => 'Images/category-oriental-woody.webp',
                'status' => 1,
            ],
            [
                'name' => 'Floral Symphony',
                'description' => 'A bouquet of fresh and vibrant floral scents.',
                'image' => 'Images/category-floral.webp',
                'status' => 1,
            ],
            [
                'name' => 'Fresh & Zesty',
                'description' => 'Invigorating citrus and aquatic notes for everyday freshness.',
                'image' => 'Images/category-fresh.webp',
                'status' => 1,
            ],
        ];

        $createdCollections = [];
        foreach ($collectionsData as $data) {
            $data['slug'] = Str::slug($data['name']) . '-' . $tenantId;
            $data['tenant_id'] = $tenantId;
            $createdCollections[] = Collection::create($data);
        }

        // 2. Seed Sample Products
        $productsData = [
            [
                'title' => 'Amber Elixir',
                'image' => 'Images/product-amber-elixir.webp',
                'type' => 'Perfume Oil',
                'vendor' => 'Premium Brand',
                'price' => 1299.00,
            ],
            [
                'title' => 'Bangalore Bloom',
                'image' => 'Images/product-bangalore-bloom.webp',
                'type' => 'Eau de Parfum',
                'vendor' => 'Premium Brand',
                'price' => 1499.00,
            ],
            [
                'title' => 'California Sunshine',
                'image' => 'Images/product-california-sunshine.webp',
                'type' => 'Body Mist',
                'vendor' => 'Premium Brand',
                'price' => 899.00,
            ],
            [
                'title' => 'Midnight Jasmine',
                'image' => 'Images/product-midnight-jasmine.webp',
                'type' => 'Perfume Oil',
                'vendor' => 'Premium Brand',
                'price' => 1350.00,
            ],
        ];

        foreach ($productsData as $data) {
            $randomColl = count($createdCollections) > 0 ? $createdCollections[array_rand($createdCollections)] : null;
            
            $product = Product::create([
                'tenant_id' => $tenantId,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . $tenantId,
                'description' => "Experience the enchanting aroma of {$data['title']}. A perfect blend for any occasion.",
                'status' => 'active',
                'type' => $data['type'],
                'vendor' => $data['vendor'],
                'collection_id' => $randomColl ? $randomColl->id : null,
                'gender' => collect(['Men', 'Women', 'Unisex'])->random(),
                'olfactory_family' => collect(['Floral', 'Woody', 'Fresh', 'Oriental'])->random(),
                'intensity' => collect(['Light', 'Moderate', 'Strong'])->random(),
            ]);

            // Add Main Image
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $data['image'],
                'type' => 'main',
                'order' => 1,
            ]);

            // Add Variants (Sizes)
            $sizes = ['50ml', '100ml'];
            foreach ($sizes as $index => $size) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => Str::upper(Str::slug($data['title'])) . '-' . $size,
                    'size' => $size,
                    'price' => $index === 0 ? $data['price'] : $data['price'] * 1.8,
                    'stock' => rand(20, 100),
                ]);
            }
        }

        // 3. Seed Banners / Sliders
        $slidersData = [
            [
                'title' => 'Luxury Fragrances',
                'image_desktop' => 'Images/slider-1-desktop.webp',
                'image_mobile' => 'Images/slider-1-mobile.webp',
                'status' => true,
                'order' => 1,
            ],
            [
                'title' => 'Summer Collection 2026',
                'image_desktop' => 'Images/slider-2-desktop.webp',
                'image_mobile' => 'Images/slider-2-mobile.webp',
                'status' => true,
                'order' => 2,
            ],
        ];

        foreach ($slidersData as $data) {
            $data['tenant_id'] = $tenantId;
            Slider::create($data);
        }

        // 4. Seed Delivery Partners
        $partnersData = array_merge(array_values(config('delivery_partners')), [
            [
                'name' => 'BlueDart',
                'site_url' => 'https://www.bluedart.com',
                'tracking_url_template' => 'https://www.bluedart.com/tracking?id={tracking_number}',
                'status' => true,
            ],
            [
                'name' => 'Delhivery',
                'site_url' => 'https://www.delhivery.com',
                'tracking_url_template' => 'https://www.delhivery.com/track/package/{tracking_number}',
                'status' => true,
            ],
        ]);

        foreach ($partnersData as $data) {
            $data['tenant_id'] = $tenantId;
            $data['status'] ??= true;
            DeliveryPartner::create($data);
        }
    }
}
