<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('tenants', function (Blueprint $table) {
        $table->boolean('is_frozen')->default(false);
        $table->timestamp('frozen_at')->nullable();
        $table->text('freeze_reason')->nullable();
    }); }
    public function down(): void { Schema::table('tenants', fn (Blueprint $table) => $table->dropColumn(['is_frozen','frozen_at','freeze_reason'])); }
};
