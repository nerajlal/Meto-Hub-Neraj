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
        Schema::table('tenant_payment_settings', function (Blueprint $table) {
            $table->boolean('whatsapp_checkout_enabled')->default(true)->after('cod_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_payment_settings', function (Blueprint $table) {
            $table->dropColumn('whatsapp_checkout_enabled');
        });
    }
};
