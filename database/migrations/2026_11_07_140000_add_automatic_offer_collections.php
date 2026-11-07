<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->string('system_key', 30)->nullable();
            $table->unique(['tenant_id', 'system_key']);
        });
        Schema::table('bundles', function (Blueprint $table) {
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
        });
        // Include existing offers as well as offers created after deployment.
        DB::table('bundles')->whereNotNull('tenant_id')->whereIn('type', ['bundle', 'pack', 'pool'])->select('tenant_id')->distinct()->orderBy('tenant_id')->get()->each(function ($row) {
            if (!DB::table('tenants')->where('id', $row->tenant_id)->exists()) return;
            $collection = app(\App\Services\OfferCollection::class)->forTenant((int) $row->tenant_id);
            DB::table('bundles')->where('tenant_id', $row->tenant_id)->whereIn('type', ['bundle', 'pack', 'pool'])->update(['collection_id' => $collection->id]);
        });
    }
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) { $table->dropConstrainedForeignId('collection_id'); });
        Schema::table('collections', function (Blueprint $table) { $table->dropUnique(['tenant_id', 'system_key']); $table->dropColumn('system_key'); });
        // Preserve merchant-edited collection names, descriptions and images on rollback.
    }
};
