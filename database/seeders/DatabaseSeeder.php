<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            CollectionSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,
            BundleSeeder::class,
            DiscountSeeder::class,
            SliderSeeder::class,
            DeliveryPartnerSeeder::class,
            OrderSeeder::class,
            SuperAdminSeeder::class,
            RajStoreSeeder::class,
            GlobalProductImageSeeder::class,
        ]);

        $tenant = \App\Models\Tenant::first();

        User::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin User',
            'email' => 'admin@nurah.com',
            'password' => bcrypt('password'),
            'type' => 'super_admin',
        ]);
    }
}
