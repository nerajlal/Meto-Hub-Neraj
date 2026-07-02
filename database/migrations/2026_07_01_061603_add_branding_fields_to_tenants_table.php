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
            $table->string('logo')->nullable()->after('mobile_grid_cols');
            $table->string('primary_color')->default('#10b981')->after('logo');
            $table->string('dark_color')->default('#0f172a')->after('primary_color');
            $table->string('accent_color')->default('#ecfdf5')->after('dark_color');
            $table->string('currency')->default('INR')->after('accent_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo', 'primary_color', 'dark_color', 'accent_color', 'currency']);
        });
    }
};
