<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id('payment_gateway_id');
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->boolean('is_enabled')->default(true);
            $table->json('config_json')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->foreignId('gateway_id')->constrained('payment_gateways', 'payment_gateway_id')->onDelete('cascade');
            $table->string('code', 60);
            $table->string('name', 120);
            $table->boolean('is_enabled')->default(true);
            $table->decimal('min_amount', 14, 2)->default(0);
            $table->decimal('max_amount', 14, 2)->nullable();
            $table->json('extra_config')->nullable();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained('payment_gateways', 'payment_gateway_id');
            $table->string('method_code', 60)->nullable();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('IDR');
            $table->enum('status', ['pending', 'capture', 'settlement', 'cancel', 'expire', 'deny', 'refund', 'chargeback'])->default('pending');
            $table->string('provider_txn_id', 80)->nullable();
            $table->string('provider_order_id', 80)->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id('shipping_carrier_id');
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->enum('provider_type', ['aggregator', 'tracking', 'rates']);
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->boolean('is_enabled')->default(false);
            $table->json('config_json')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id('shipping_service_id');
            $table->foreignId('carrier_id')->constrained('shipping_carriers', 'shipping_carrier_id')->onDelete('cascade');
            $table->string('service_code', 60);
            $table->string('service_name', 120);
            $table->boolean('is_enabled')->default(true);

            $table->unique(['carrier_id', 'service_code'], 'uniq_carrier_service');
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('carrier_id')->nullable()->constrained('shipping_carriers', 'shipping_carrier_id')->nullOnDelete();
            $table->string('service_code', 60)->nullable();
            $table->string('tracking_number', 80)->nullable();
            $table->string('label_url')->nullable();
            $table->enum('status', ['pending', 'label_created', 'in_transit', 'delivered', 'exception', 'cancelled'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('cost', 14, 2)->default(0);
            $table->json('metadata')->nullable();

            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_services');
        Schema::dropIfExists('shipping_carriers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_gateways');
    }
};
