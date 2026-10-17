<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tenants', 'products', 'collections'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('seo_title', 120)->nullable();
                $t->text('seo_description')->nullable();
                $t->string('seo_image', 2048)->nullable();
            });
        }
        Schema::table('tenants', fn (Blueprint $t) => $t->boolean('search_indexable')->default(true));
    }

    public function down(): void
    {
        foreach (['tenants', 'products', 'collections'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn(['seo_title', 'seo_description', 'seo_image']));
        }
        Schema::table('tenants', fn (Blueprint $t) => $t->dropColumn('search_indexable'));
    }
};
