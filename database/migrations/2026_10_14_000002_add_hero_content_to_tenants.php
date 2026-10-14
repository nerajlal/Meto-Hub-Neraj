<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('hero_eyebrow', 120)->nullable();
            $table->string('hero_heading', 200)->nullable();
            $table->text('hero_description')->nullable();
            $table->string('hero_button_label', 60)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['hero_eyebrow', 'hero_heading', 'hero_description', 'hero_button_label']);
        });
    }
};
