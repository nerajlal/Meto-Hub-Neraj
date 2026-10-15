<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('checkout_attempt')->unique();
            $table->string('gateway_id')->nullable()->unique();
            $table->string('gateway_plan_id')->nullable();
            $table->string('plan')->nullable();
            $table->string('cycle')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('creating')->index();
            $table->unsignedInteger('paid_count')->default(0);
            $table->timestamp('current_start')->nullable();
            $table->timestamp('current_end')->nullable();
            $table->timestamp('next_charge_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('invoices_synced_at')->nullable();
            $table->boolean('cancel_at_cycle_end')->default(false);
            $table->timestamps();
        });
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_subscription_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_id')->unique();
            $table->string('invoice_id')->nullable()->index();
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_refunded')->default(0);
            $table->string('currency', 3);
            $table->string('status');
            $table->string('method')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_subscription_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_id')->unique();
            $table->string('payment_id')->nullable();
            $table->string('status');
            $table->string('currency', 3);
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_paid')->default(0);
            $table->unsignedBigInteger('amount_due')->default(0);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
        Schema::create('billing_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->string('payload_hash', 64);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_webhook_events');
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('store_subscriptions');
    }
};
