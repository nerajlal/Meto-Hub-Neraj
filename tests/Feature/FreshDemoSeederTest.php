<?php

namespace Tests\Feature;

use App\Models\StoreSubscription;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\FreshDemoSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FreshDemoSeederTest extends TestCase
{
    use DatabaseMigrations;

    public function runDatabaseMigrations()
    {
        $this->refreshTestDatabase();
        $this->beforeApplicationDestroyed(fn () => \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = false);
    }

    public function test_seven_demo_stores_and_dashboard_records_are_isolated_and_accessible(): void
    {
        config(['hashing.bcrypt.rounds' => 4]);
        Http::preventStrayRequests();
        $this->seed(FreshDemoSeeder::class);
        $this->assertDatabaseCount('tenants', 7);
        $this->assertDatabaseCount('products', 63);
        $this->assertDatabaseCount('orders', 35);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertSame(1, User::withoutGlobalScopes()->where('type', 'super_admin')->count());
        $this->assertSame(7, Tenant::query()->distinct()->count('theme'));
        $this->assertSame(7, Tenant::query()->distinct()->count('primary_color'));
        $this->assertDatabaseCount('sliders', 7);
        foreach (Tenant::all() as $tenant) {
            $this->assertSame(9, DB::table('products')->where('tenant_id', $tenant->id)->count());
            $this->get('/store/'.$tenant->slug)->assertOk();
            $owner = User::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('type', 'admin')->firstOrFail();
            $this->actingAs($owner)->get('/'.$tenant->id.'/admin')->assertOk();
            auth()->forgetGuards();
        }
        foreach (StoreSubscription::all() as $subscription) {
            $this->assertTrue((bool) $subscription->is_demo);
            $this->assertSame($subscription->id, app(\App\Services\SubscriptionBilling::class)->reconcile($subscription)->id);
        }
        $super = User::withoutGlobalScopes()->where('type', 'super_admin')->firstOrFail();
        $this->actingAs($super)->get('/superadmin')->assertOk();
    }
}
