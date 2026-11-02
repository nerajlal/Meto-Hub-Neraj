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
        Schema::create('product_image_product_variant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('product_image_id')->constrained('product_images')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['product_variant_id', 'product_image_id'], 'variant_image_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_image_product_variant');
    }
};
