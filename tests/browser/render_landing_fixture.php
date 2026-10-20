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
  $p->images()->create(['path'=>'../images/landing/hero.png','type'=>'image','order'=>0]);
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
    foreach (['features', 'security', 'pricing', 'privacy-policy', 'terms', 'refund-cancellation', 'about', 'contact', 'themes', 'blogs', 'blogs/7'] as $path) {
        $pageRequest = Illuminate\Http\Request::create('http://127.0.0.1:8000/'.$path, 'GET');
        $pageResponse = $kernel->handle($pageRequest);
        if ($pageResponse->getStatusCode() !== 200) throw new RuntimeException('Page fixture failed: '.$path);
        file_put_contents(dirname($argv[1]).'/'.str_replace('/', '-', $path).'.html', $pageResponse->getContent());
    }
}
