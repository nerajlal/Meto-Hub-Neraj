<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('instagram_url');
            $table->string('title')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('link_type', ['product', 'collection'])->default('product');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('collection_id')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
