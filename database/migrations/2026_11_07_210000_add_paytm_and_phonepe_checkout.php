<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_payment_settings', function (Blueprint $table) {
            $table->boolean('paytm_enabled')->default(false);
            $table->string('paytm_mid', 100)->nullable();
            $table->text('paytm_merchant_key')->nullable();
            $table->string('paytm_environment', 10)->default('test');
            $table->string('paytm_website_name', 100)->nullable();
            $table->string('phonepe_client_id', 150)->nullable();
            $table->unsignedInteger('phonepe_client_version')->nullable();
            $table->text('phonepe_client_secret')->nullable();
            $table->string('phonepe_environment', 10)->default('test');
            $table->string('phonepe_webhook_username', 100)->nullable();
            $table->text('phonepe_webhook_password')->nullable();
        });
        // Legacy V1 salt credentials cannot enable the V2 integration.
        DB::table('tenant_payment_settings')->update(['phonepe_enabled' => false]);
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->string('gateway', 20)->default('razorpay')->index();
            $table->longText('checkout_data')->nullable();
            $table->dropUnique(['provider_payment_id']);
            $table->unique(['gateway', 'provider_payment_id']);
        });
    }

    public function down(): void
    {
        if (DB::table('customer_payments')->where('gateway', '!=', 'razorpay')->exists()) {
            throw new RuntimeException('Alternative gateway payment history exists. Restore a matching application/database backup instead of dropping payment records.');
        }
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropUnique(['gateway', 'provider_payment_id']);
            $table->unique('provider_payment_id');
            $table->dropIndex(['gateway']);
            $table->dropColumn(['gateway', 'checkout_data']);
        });
        Schema::table('tenant_payment_settings', fn (Blueprint $table) => $table->dropColumn([
            'paytm_enabled', 'paytm_mid', 'paytm_merchant_key', 'paytm_environment', 'paytm_website_name',
            'phonepe_client_id', 'phonepe_client_version', 'phonepe_client_secret', 'phonepe_environment',
            'phonepe_webhook_username', 'phonepe_webhook_password']));
    }
};
