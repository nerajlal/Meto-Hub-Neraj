<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_order_alerts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamp('read_at')->nullable();
            $t->timestamp('emailed_at')->nullable();
            $t->unsignedSmallInteger('email_attempts')->default(0);
            $t->timestamp('email_retry_at')->nullable();
            $t->string('email_error')->nullable();
            $t->timestamp('queued_at')->nullable();
            $t->timestamps();
            $t->index(['tenant_id', 'user_id', 'read_at']);
        });
        Schema::create('merchant_push_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->char('endpoint_hash', 64)->unique();
            $t->text('endpoint');
            $t->text('public_key');
            $t->text('auth_token');
            $t->string('origin');
            $t->boolean('enabled')->default(true);
            $t->timestamps();
        });
        Schema::create('merchant_push_deliveries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('merchant_order_alert_id')->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_push_subscription_id')->constrained()->cascadeOnDelete();
            $t->timestamp('sent_at')->nullable();
            $t->timestamp('failed_at')->nullable();
            $t->unsignedSmallInteger('attempts')->default(0);
            $t->timestamp('retry_at')->nullable();
            $t->string('last_error')->nullable();
            $t->timestamps();
            $t->unique(['merchant_order_alert_id', 'merchant_push_subscription_id'], 'merchant_push_delivery_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_push_deliveries');
        Schema::dropIfExists('merchant_push_subscriptions');
        Schema::dropIfExists('merchant_order_alerts');
    }
};
