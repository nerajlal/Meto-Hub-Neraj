<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('tenants', 'trial_ends_at')) Schema::table('tenants', fn (Blueprint $table) => $table->timestamp('trial_ends_at')->nullable()->index());
        Schema::create('tenant_trial_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->unsignedBigInteger('changed_by');
            $table->dateTime('previous_ends_at');
            $table->dateTime('new_ends_at');
            $table->string('reason', 500);
            $table->dateTime('created_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('tenant_trial_changes');
        Schema::table('tenants', fn (Blueprint $table) => $table->dropColumn('trial_ends_at'));
    }
};
