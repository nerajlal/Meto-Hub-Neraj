<?php
$project = dirname(__DIR__, 2);
require $project.'/vendor/autoload.php';
$app = require $project.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','session.driver'=>'array','cache.default'=>'array','app.url'=>'http://127.0.0.1:8000']);
Illuminate\Support\Facades\DB::purge('sqlite');
if(config('database.default')!=='sqlite'||config('database.connections.sqlite.database')!==':memory:')throw new RuntimeException('In-memory fixtures only');
Illuminate\Support\Facades\Artisan::call('migrate:fresh',['--force'=>true]);
$brands=['mens-clothing'=>'Thread & Form','grocery'=>'Fresh Basket','gadgets'=>'Circuit Cart','jewelry'=>'Aurelia Jewels','watches'=>'Hour House','womens-clothing'=>'Loom & Grace','perfume'=>'Maison Mist'];
foreach(Database\Seeders\DemoStoreDesign::profiles() as $slug=>$profile){
 [$theme,$primary,$dark,$accent,$eyebrow,$heading,$description,$button]=$profile;
 $tenant=App\Models\Tenant::create(['name'=>$brands[$slug],'theme'=>$theme,'primary_color'=>$primary,'dark_color'=>$dark,'accent_color'=>$accent,'currency'=>'INR','hero_eyebrow'=>$eyebrow,'hero_heading'=>$heading,'hero_description'=>$description,'hero_button_label'=>$button]);
 $tenant->forceFill(['slug'=>$slug])->save();
 foreach(['Essentials','Collection'] as $i=>$name){
  $collection=App\Models\Collection::create(['tenant_id'=>$tenant->id,'name'=>$name,'slug'=>$slug.'-'.$i,'status'=>true]);
  $p=App\Models\Product::create(['tenant_id'=>$tenant->id,'title'=>$brands[$slug].' sample '.$i,'slug'=>$slug.'-product-'.$i,'collection_id'=>$collection->id,'status'=>'active']);
  $p->variants()->create(['size'=>'Standard','price'=>499+$i*100,'stock'=>10]);
  if (($argv[2] ?? '') === '--store-themes') {
   $p->variants()->create(['size'=>'Large-Pack','price'=>799,'compare_at_price'=>899,'stock'=>5]);
   $p->variants()->create(['size'=>'Sold out','price'=>999,'stock'=>0]);
   $p->variants()->create(['size'=>'Family','price'=>1299,'stock'=>5]);
  }
  $p->images()->create(['path'=>'images/landing/hero.png','type'=>'image','order'=>0]);
  if (($argv[2] ?? '') === '--store-themes' && $i === 0) {
   $single = App\Models\Product::create(['tenant_id'=>$tenant->id,'title'=>'Single variant product with a longer title','slug'=>$slug.'-single-variant','collection_id'=>$collection->id,'status'=>'active']);
   $single->variants()->create(['size'=>'150g','price'=>299,'compare_at_price'=>350,'stock'=>10]);
   $single->images()->create(['path'=>'images/landing/hero.png','type'=>'image','order'=>0]);
  }

 }
}
$kernel=$app->make(Illuminate\Contracts\Http\Kernel::class);
$request=Illuminate\Http\Request::create('http://127.0.0.1:8000/','GET');
$response=$kernel->handle($request);
if($response->getStatusCode()!==200)throw new RuntimeException('Fixture failed');
if (empty($argv[1])) { throw new RuntimeException('An output HTML file is required.'); }
file_put_contents($argv[1],$response->getContent());
echo "Rendered landing fixture from isolated in-memory database.\n";

if (($argv[2] ?? '') === '--public-pages') {
    config(['services.blog.key' => 'browser-fixture']);
    Illuminate\Support\Facades\Http::fake(['*' => Illuminate\Support\Facades\Http::response(['blogs' => [['id' => 7, 'title' => 'Your first collection launch', 'content' => '<h2>A place for your brand</h2><p>Introduce your collection with clear product details and a store link.</p>', 'cover_image' => 'http://127.0.0.1:8000/images/landing/reference/hero.jpg', 'created_at' => '2026-10-01', 'updated_at' => '2026-10-02']]])]);
    foreach (['demo', 'features', 'security', 'pricing', 'privacy-policy', 'terms', 'refund-cancellation', 'about', 'contact', 'themes', 'blogs', 'blogs/7'] as $path) {
        $pageRequest = Illuminate\Http\Request::create('http://127.0.0.1:8000/'.$path, 'GET');
        $pageResponse = $kernel->handle($pageRequest);
        if ($pageResponse->getStatusCode() !== 200) throw new RuntimeException('Page fixture failed: '.$path);
        file_put_contents(dirname($argv[1]).'/'.str_replace('/', '-', $path).'.html', $pageResponse->getContent());
    }
}

if (($argv[2] ?? '') === '--store-themes') {
    $tenant = App\Models\Tenant::where('slug', 'mens-clothing')->firstOrFail();
    $product = App\Models\Product::withoutGlobalScopes()->where('tenant_id', $tenant->id)->firstOrFail();
    foreach (['bundle', 'pack', 'bundle', 'pack'] as $index => $kind) {
        $bundle = App\Models\Bundle::create(['tenant_id' => $tenant->id, 'title' => ['Everyday essentials duo', 'Pack of three favourites', 'The weekend collection', 'Family favourites pack'][$index], 'slug' => 'bundle-fixture-'.$index, 'card_background' => $index === 0 ? ['mode' => 'solid', 'color' => '#183a14'] : ($index === 1 ? ['mode' => 'gradient', 'color' => '#fff0bd', 'color_end' => '#dce9fa', 'angle' => 90] : null), 'type' => $kind, 'status' => 'active', 'description' => 'A selection from our store, ready to explore together.', 'discount_type' => 'fixed', 'discount_value' => 25]);
        $bundle->products()->attach($product->id, ['product_variant_id' => $product->variants->first()->id, 'quantity' => $kind === 'pack' ? 3 : 1]);
    }
    $pool = App\Models\Bundle::create(['tenant_id' => $tenant->id, 'title' => 'Mix your favourites', 'slug' => 'pool-fixture', 'type' => 'pool', 'status' => 'active', 'min_quantity' => 2, 'discount_type' => 'fixed', 'discount_value' => 50]);
    $pool->products()->attach($product->id);
    $tenant->update(['about_title' => 'The story behind our collection', 'about_text' => 'A merchant-written introduction to the products, people and ideas behind this store. Discover our latest collection and learn more about our business.', 'about_image' => 'images/landing/hero.png']);
    App\Models\Review::create(['tenant_id' => $tenant->id, 'product_id' => $product->id, 'name' => 'Fixture shopper', 'review' => 'A published review used only for local layout testing.', 'rating' => 5, 'status' => 'published']);
    $offerCollection = App\Models\Collection::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('system_key', 'offers')->firstOrFail();
    $offerCollection->update(['image' => 'images/landing/hero.png']);
    foreach (range(1, 5) as $index) {
        App\Models\Reel::create(['tenant_id' => $tenant->id, 'title' => 'A closer look at our favourites '.$index, 'instagram_url' => 'https://www.instagram.com/reel/FixtureReel'.$index.'/', 'link_type' => 'product', 'product_id' => $product->id, 'status' => true, 'order' => $index]);
    }
    foreach (array_keys(config('storefront_themes')) as $theme) {
        $tenant->update(['theme' => $theme]);
        if ($theme === 'template_2') {
            foreach ([1, 2] as $number) App\Models\Slider::create(['tenant_id' => $tenant->id, 'title' => 'Market banner '.$number, 'image_desktop' => 'images/landing/hero.png', 'image_mobile' => 'images/landing/hero.png', 'status' => true, 'order' => $number]);
        }
        foreach (['home' => '', 'catalog' => '/all-products', 'product' => '/product?id='.$product->id, 'about' => '/about', 'offers' => '/collections?slug='.$offerCollection->slug] as $name => $suffix) {
            $request = Illuminate\Http\Request::create('http://127.0.0.1:8000/store/'.$tenant->slug.$suffix);
            $response = $kernel->handle($request);
            if ($response->getStatusCode() !== 200) throw new RuntimeException('Theme fixture failed: '.$theme.' '.$name);
            file_put_contents(dirname($argv[1]).'/'.$theme.'-'.$name.'.html', $response->getContent());
        }
    }
}
