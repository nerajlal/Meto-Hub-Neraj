<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_subscriptions', function (Blueprint $table) {
            $table->timestamp('paid_from')->nullable();
            $table->timestamp('paid_through')->nullable();
        });
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->timestamp('billing_start')->nullable();
            $table->timestamp('billing_end')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_invoices', fn (Blueprint $table) => $table->dropColumn(['billing_start', 'billing_end']));
        Schema::table('store_subscriptions', fn (Blueprint $table) => $table->dropColumn(['paid_from', 'paid_through']));
    }
};
