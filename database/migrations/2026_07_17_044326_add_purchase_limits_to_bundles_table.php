<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->unsignedInteger('min_order_qty')->nullable()->after('total_sales');
            $table->unsignedInteger('max_order_qty')->nullable()->after('min_order_qty');
        });
    }

    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['min_order_qty', 'max_order_qty']);
        });
    }
};
