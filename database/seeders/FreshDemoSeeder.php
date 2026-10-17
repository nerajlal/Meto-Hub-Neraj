<?php

namespace Database\Seeders;

use App\Models\Bundle;
use App\Models\Collection;
use App\Models\DeliveryPartner;
use App\Models\Discount;
use App\Models\MerchantOrderAlert;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FreshDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Demo data is only available in local/testing environments.');
        }
        if (Tenant::count() || User::withoutGlobalScopes()->count()) {
            throw new \RuntimeException('Run this seeder on a fresh database only.');
        }
        $password = Str::password(18);
        $accounts = [];
        DB::transaction(function () use ($password, &$accounts) {
            $super = User::forceCreate(['name' => 'MetoHub Super Admin', 'email' => 'superadmin@metohub.test', 'password' => $password, 'type' => 'super_admin', 'email_verified_at' => now()]);
            $accounts[] = ['role' => 'super_admin', 'email' => $super->email, 'url' => '/superadmin'];
            $stores = [
                ['Thread & Form', 'mens-clothing', '#263c50', 'template_6', 'trial', ['Shirts' => ['Oxford Cotton Shirt', 899, 'M'], 'Essentials' => ['Everyday Crew T-shirt', 499, 'M'], 'Trousers' => ['Relaxed Chino', 1299, '32']]],
                ['Fresh Basket', 'grocery', '#27634a', 'template_2', 'active', ['Vegetables' => ['Farm Fresh Tomatoes', 60, '1 kg'], 'Fruits' => ['Sweet Royal Apples', 180, '1 kg'], 'Pantry' => ['Premium Basmati Rice', 149, '1 kg']]],
                ['Circuit Cart', 'gadgets', '#344da8', 'template_3', 'pending', ['Audio' => ['Wireless Headphones', 2499, 'Black'], 'Accessories' => ['Fast USB-C Charger', 799, '30 W'], 'Smart Home' => ['Portable Bluetooth Speaker', 1499, 'Black']]],
                ['Aurelia Jewels', 'jewelry', '#986c36', 'template_4', 'active', ['Necklaces' => ['Pearl Pendant Necklace', 1899, 'Standard'], 'Earrings' => ['Gold Tone Hoop Earrings', 999, 'Standard'], 'Bracelets' => ['Minimal Cuff Bracelet', 1299, 'Standard']]],
                ['Hour House', 'watches', '#354a49', 'template_5', 'expired', ['Classic' => ['Classic Leather Watch', 2999, '40 mm'], 'Sport' => ['Sport Chronograph', 4499, '42 mm'], 'Minimal' => ['Minimal Mesh Watch', 2499, '38 mm']]],
                ['Loom & Grace', 'womens-clothing', '#8d4d68', 'template_1', 'trial', ['Dresses' => ['Linen Midi Dress', 1899, 'M'], 'Tops' => ['Cotton Everyday Blouse', 899, 'M'], 'Ethnic' => ['Printed Cotton Kurta', 1299, 'M']]],
                ['Maison Mist', 'perfume', '#75527d', 'template_6', 'halted', ['Fresh' => ['Citrus Dawn Eau de Parfum', 1499, '50 ml'], 'Floral' => ['Velvet Rose Eau de Parfum', 1899, '50 ml'], 'Woody' => ['Amber Woods Eau de Parfum', 2199, '50 ml']]],
            ];
            foreach ($stores as $n => [$name,$slug,$color,$theme,$state,$catalog]) {
                $tenant = Tenant::create(['name' => $name, 'theme' => $theme, 'primary_color' => $color, 'dark_color' => '#14251e', 'accent_color' => $color, 'currency' => 'INR', 'plan' => $n % 2 ? 'pro' : 'base', 'contact_email' => "owner@{$slug}.test", 'contact_phone' => '900000000'.($n + 1), 'contact_address' => ($n + 1).' Demo Market Road, Kochi, Kerala 682001', 'hero_eyebrow' => 'Welcome to '.$name, 'hero_heading' => array_key_first($catalog).' and more, selected for you.', 'hero_description' => 'Explore our sample collection. Demo store for testing MetoHub.', 'hero_button_label' => 'Explore the collection', 'about_title' => 'About '.$name, 'about_text' => 'A demo independent store, thoughtfully curated for everyday shopping.', 'shipping_policy' => 'Demo: dispatch within two business days.', 'return_policy' => 'Demo: contact the store within seven days of delivery.', 'terms_of_service' => 'Demo catalog for testing only. No real payments or shipments.', 'mobile_grid_cols' => 2, 'search_indexable' => false]);
                $tenant->forceFill(['slug' => $slug, 'created_at' => in_array($state, ['trial']) ? now()->subDay() : now()->subDays(20), 'subscription_status' => $state === 'trial' ? 'created' : $state])->save();
                $owner = User::forceCreate(['tenant_id' => $tenant->id, 'name' => $name.' Owner', 'email' => 'demo'.($n + 1).'@gmail.com', 'phone' => $tenant->contact_phone, 'password' => 'password', 'type' => 'admin', 'email_verified_at' => now()]);
                $accounts[] = ['role' => 'owner', 'password' => 'password', 'store' => $name, 'state' => $state, 'email' => $owner->email, 'url' => "/{$tenant->id}/admin", 'storefront' => '/store/'.$slug];
                $products = [];
                foreach ($catalog as $category => [$title,$price,$size]) {
                    $coll = Collection::create(['tenant_id' => $tenant->id, 'name' => $category, 'slug' => $slug.'-'.Str::slug($category), 'status' => true, 'description' => 'Discover '.$category.' at '.$name]);
                    for ($i = 0; $i < 3; $i++) {
                        $titleVariant = $title.(['', ' — Signature', ' — Edition Two'][$i]);
                        $p = Product::create(['tenant_id' => $tenant->id, 'title' => $titleVariant, 'slug' => $slug.'-'.Str::slug($titleVariant), 'description' => '<p>'.$titleVariant.'. Carefully selected by '.$name.'. Sample product for exploring the store experience.</p>', 'status' => 'active', 'type' => 'product', 'vendor' => $name, 'collection_id' => $coll->id, 'tags' => [$i ? 'Bestseller' : 'New']]);
                        foreach ([$size, $slug === 'mens-clothing' || $slug === 'womens-clothing' ? 'L' : ($slug === 'perfume' ? '100 ml' : 'Premium')] as $v => $label) {
                            ProductVariant::create(['product_id' => $p->id, 'size' => $label, 'price' => $price + $i * 50 + $v * 100, 'compare_at_price' => $price + $i * 50 + $v * 100 + 200, 'stock' => $i === 2 ? 3 : 25, 'sku' => strtoupper(substr($slug, 0, 3)).'-'.$p->id.'-'.$v]);
                        }
                        $path = 'demo-catalog/'.$slug.'-'.$p->id.'.svg';
                        Storage::disk('public')->put($path, $this->illustration($slug, $titleVariant, $color, $i));
                        ProductImage::create(['product_id' => $p->id, 'path' => $path, 'type' => 'image', 'order' => 0]);
                        if ($i === 0) {
                            $coll->update(['image' => $path]);
                        }
                        $products[] = $p;
                    }
                }
                $bundle = Bundle::create(['tenant_id' => $tenant->id, 'title' => $name.' Starter Pair', 'slug' => $slug.'-starter-pair', 'type' => 'bundle', 'description' => 'Two demo favorites together.', 'image' => $products[0]->images()->first()->path, 'status' => 'active', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_quantity' => 1]);
                foreach (array_slice($products, 0, 2) as $p) {
                    $bundle->products()->attach($p->id, ['quantity' => 1, 'product_variant_id' => $p->variants()->first()->id]);
                }
                Discount::create(['tenant_id' => $tenant->id, 'code' => 'DEMO10-'.$tenant->id, 'type' => 'percentage', 'value' => 10, 'status' => 'active', 'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth()]);
                $partner = DeliveryPartner::create(['tenant_id' => $tenant->id, 'name' => 'Demo Local Delivery', 'status' => true, 'is_default' => true]);
                foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $j => $status) {
                    $customer = User::forceCreate(['tenant_id' => $tenant->id, 'name' => 'Demo Customer '.($j + 1), 'email' => 'customer'.($j + 1).'@'.$slug.'.test', 'phone' => '900001000'.($j + 1), 'password' => $password, 'type' => 'user', 'email_verified_at' => now()]);
                    $p = $products[$j];
                    $v = $p->variants()->first();
                    $paid = $status === 'delivered';
                    $order = Order::forceCreate(['tenant_id' => $tenant->id, 'user_id' => $customer->id, 'order_number' => 'DEMO-'.($n + 1).'-'.sprintf('%04d', $j + 1), 'status' => $status, 'payment_method' => 'cod', 'payment_status' => $paid ? 'paid' : 'pending', 'payment_recorded_at' => $paid ? now()->subDay() : null, 'payment_recorded_by' => $paid ? $owner->id : null, 'subtotal' => $v->price * 2, 'shipping_cost' => 50, 'total_amount' => $v->price * 2 + 50, 'customer_name' => $customer->name, 'customer_email' => $customer->email, 'customer_phone' => $customer->phone, 'shipping_address' => ['address' => '10 Sample Customer Lane', 'city' => 'Kochi', 'state' => 'Kerala', 'zip' => '682001', 'country' => 'India'], 'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 'DEMO-TRACK-'.$n.'-'.$j : null, 'delivery_partner_id' => $partner->id, 'placed_at' => now()->subDays($j), 'created_at' => now()->subDays($j)]);
                    $order->items()->create(['product_id' => $p->id, 'name' => $p->title, 'size' => $v->size, 'quantity' => 2, 'price' => $v->price, 'total' => $v->price * 2, 'type' => 'product']);
                    MerchantOrderAlert::create(['tenant_id' => $tenant->id, 'order_id' => $order->id, 'user_id' => $owner->id, 'read_at' => $j ? now() : null, 'emailed_at' => now(), 'email_error' => null]);
                }
                DemoStoreDesign::apply($tenant);
                if ($state !== 'trial') {
                    $this->billing($tenant, $state);
                }
            }
        });
        if (app()->environment('testing')) {
            return;
        }
        $path = storage_path('app/private/demo-credentials.json');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0700, true);
        }
        file_put_contents($path, json_encode(['password' => $password, 'accounts' => $accounts], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        chmod($path, 0600);
        $this->command?->info('Seeded 7 stores, 63 products, 35 orders and super admin. Login credentials: storage/app/private/demo-credentials.json');
    }

    private function billing(Tenant $tenant, string $state): void
    {
        $active = $state === 'active';
        $expired = $state === 'expired';
        $paid = $active || $expired;
        $start = $expired ? now()->subMonths(2) : now()->subDays(5);
        $end = $expired ? now()->subMonth() : now()->addDays(25);
        $sub = StoreSubscription::create(['tenant_id' => $tenant->id, 'checkout_attempt' => (string) Str::uuid(), 'gateway_id' => null, 'is_demo' => true, 'plan' => $tenant->plan, 'cycle' => 'monthly', 'amount' => $tenant->plan === 'pro' ? 24900 : 9900, 'currency' => 'INR', 'status' => $state, 'paid_count' => $paid ? 1 : 0, 'paid_from' => $paid ? $start : null, 'paid_through' => $paid ? $end : null, 'current_start' => $start, 'current_end' => $end, 'synced_at' => now()]);
        $sub->invoices()->create(['gateway_id' => 'demo_inv_'.$tenant->id, 'status' => $paid ? 'paid' : 'issued', 'currency' => 'INR', 'amount' => $sub->amount, 'amount_paid' => $paid ? $sub->amount : 0, 'amount_due' => $paid ? 0 : $sub->amount, 'issued_at' => $start, 'paid_at' => $paid ? $start : null, 'due_at' => $end, 'billing_start' => $start, 'billing_end' => $end]);
        if ($paid) {
            $sub->payments()->create(['gateway_id' => 'demo_pay_'.$tenant->id, 'invoice_id' => 'demo_inv_'.$tenant->id, 'amount' => $sub->amount, 'currency' => 'INR', 'status' => 'captured', 'method' => 'upi', 'paid_at' => $start, 'attempted_at' => $start]);
        }
    }

    private function illustration(string $kind, string $title, string $color, int $i): string
    {
        $shape = match ($kind) {
            'mens-clothing','womens-clothing' => '<path d="M170 140L230 110Q260 145 290 110L350 140L390 215L340 240L320 205V380H200V205L180 240L130 215Z"/>',
            'grocery' => '<path d="M170 230H350L330 380H190Z"/><path d="M195 230Q170 130 225 150Q265 170 245 230M265 230Q240 110 300 135Q350 170 310 230" fill="#78a36a"/>',
            'gadgets' => '<rect x="175" y="110" width="170" height="280" rx="24"/><rect x="190" y="140" width="140" height="210" rx="8" fill="#cad8ed"/><circle cx="260" cy="370" r="8" fill="white"/>',
            'jewelry' => '<circle cx="260" cy="265" r="100" fill="none" stroke="'.$color.'" stroke-width="22"/><path d="M225 160L260 120L295 160L260 200Z"/>',
            'watches' => '<rect x="230" y="90" width="60" height="320" rx="18"/><circle cx="260" cy="250" r="86" fill="#fdfaf5" stroke="'.$color.'" stroke-width="14"/><path d="M260 190V250L300 270" fill="none" stroke="'.$color.'" stroke-width="8"/>',
            default => '<rect x="190" y="190" width="140" height="190" rx="16"/><rect x="220" y="120" width="80" height="65" rx="8"/><rect x="208" y="240" width="104" height="75" fill="#fdfaf5"/>',
        };
        $safe = htmlspecialchars(Str::limit($title, 36), ENT_XML1, 'UTF-8');

        return '<svg xmlns="http://www.w3.org/2000/svg" width="520" height="560" viewBox="0 0 520 560"><rect width="520" height="560" fill="#f4f1eb"/><circle cx="260" cy="250" r="195" fill="white"/><g fill="'.$color.'">'.$shape.'</g><text x="260" y="475" text-anchor="middle" font-family="sans-serif" font-size="18" fill="#26312b">'.$safe.'</text><text x="260" y="510" text-anchor="middle" font-family="sans-serif" font-size="12" fill="#738078">DEMO COLLECTION · '.($i + 1).'</text></svg>';
    }
}
