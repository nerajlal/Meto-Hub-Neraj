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
            $table->boolean('maintenance_mode')->default(false)->after('theme');
            $table->string('maintenance_message', 500)->nullable()->after('maintenance_mode');
            $table->timestamp('deletion_requested_at')->nullable()->after('subscription_status');
            $table->text('deletion_reason')->nullable()->after('deletion_requested_at');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['maintenance_mode', 'maintenance_message', 'deletion_requested_at', 'deletion_reason', 'deleted_at']);
        });
    }
};
