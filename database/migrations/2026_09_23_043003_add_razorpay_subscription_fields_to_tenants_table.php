<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('razorpay_customer_id')->nullable();
            $table->string('razorpay_subscription_id')->nullable();
            $table->string('subscription_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_customer_id',
                'razorpay_subscription_id',
                'subscription_status',
            ]);
        });
    }
};
