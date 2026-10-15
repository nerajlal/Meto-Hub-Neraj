<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('checkout_guard')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('store_subscriptions', function (Blueprint $table) {
            $table->dropUnique(['checkout_guard']);
            $table->dropColumn('checkout_guard');
        });
    }
};
