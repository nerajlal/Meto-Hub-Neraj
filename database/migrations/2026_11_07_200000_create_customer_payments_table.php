<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenant_payment_settings', function (Blueprint $table) {
            $table->text('razorpay_webhook_secret')->nullable();
            foreach (['razorpay_secret', 'stripe_secret', 'phonepe_salt_key'] as $column) $table->text($column)->nullable()->change();
        });
        \Illuminate\Support\Facades\DB::table('tenant_payment_settings')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                $values = [];
                foreach (['razorpay_secret', 'stripe_secret', 'phonepe_salt_key'] as $column) {
                    if (!$row->$column) continue;
                    try { \Illuminate\Support\Facades\Crypt::decryptString($row->$column); }
                    catch (\Illuminate\Contracts\Encryption\DecryptException $e) { $values[$column] = \Illuminate\Support\Facades\Crypt::encryptString($row->$column); }
                }
                if ($values) \Illuminate\Support\Facades\DB::table('tenant_payment_settings')->where('id', $row->id)->update($values);
            }
        });
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('session_hash', 64);
            $table->string('fingerprint', 64);
            $table->string('status')->default('creating')->index();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('INR');
            $table->string('provider_order_id')->nullable()->unique();
            $table->string('provider_payment_id')->nullable()->unique();
            $table->text('credentials');
            $table->longText('snapshot');
            $table->string('review_reason')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'session_hash', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
        Schema::table('tenant_payment_settings', fn (Blueprint $table) => $table->dropColumn('razorpay_webhook_secret'));
    }
};
