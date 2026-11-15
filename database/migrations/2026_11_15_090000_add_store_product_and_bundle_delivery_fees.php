<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('default_delivery_fee', 10, 2)->default(0)->after('delivery_info');
            $table->decimal('free_delivery_threshold', 10, 2)->nullable()->after('default_delivery_fee');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->nullable()->after('max_order_qty');
        });

        Schema::table('bundles', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->nullable()->after('max_order_qty');
        });
    }

    public function down(): void
    {
        Schema::table('bundles', fn (Blueprint $table) => $table->dropColumn('delivery_fee'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('delivery_fee'));
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['default_delivery_fee', 'free_delivery_threshold']);
        });
    }
};
