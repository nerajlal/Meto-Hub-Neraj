<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('mode')->default('direct');
            $table->boolean('ga4_enabled')->default(false);
            $table->string('ga4_id')->nullable();
            $table->boolean('gtm_enabled')->default(false);
            $table->string('gtm_id')->nullable();
            $table->boolean('meta_enabled')->default(false);
            $table->string('meta_pixel_id')->nullable();
            $table->boolean('gsc_enabled')->default(false);
            $table->string('gsc_token')->nullable();
            $table->string('verified_domain')->nullable();
            $table->string('domain_token')->nullable();
            $table->timestamp('domain_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_integrations');
    }
};
