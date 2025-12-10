<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Payment Gateways (Midtrans, Duitku, dll)
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id('payment_gateway_id');
            $table->string('code', 40)->unique(); // e.g., midtrans
            $table->string('name', 120);
            $table->boolean('is_enabled')->default(true);
            $table->json('config_json')->nullable(); // API Keys encyrpted
            $table->timestamps();
        });

        // 2. Payment Methods (Channel Pembayaran: BCA VA, ShopeePay)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->foreignId('gateway_id')->constrained('payment_gateways', 'payment_gateway_id')->onDelete('cascade');
            $table->string('code', 60); // e.g., bca_va
            $table->string('name', 120);
            $table->boolean('is_enabled')->default(true);
            $table->decimal('min_amount', 14, 2)->default(0);
            $table->decimal('max_amount', 14, 2)->nullable();
            $table->json('extra_config')->nullable();
        });

        // 3. Payments (Log Transaksi)
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('gateway_id')->constrained('payment_gateways', 'payment_gateway_id');
            $table->string('method_code', 60)->nullable();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('IDR');
            $table->enum('status', ['pending', 'capture', 'settlement', 'cancel', 'expire', 'deny', 'refund', 'chargeback'])->default('pending');
            $table->string('provider_txn_id', 80)->nullable(); // Transaction ID dari Gateway
            $table->string('provider_order_id', 80)->nullable();
            $table->json('raw_response')->nullable(); // Simpan respon mentah untuk debug
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // 4. Shipping Carriers (JNE, POS, SiCepat)
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id('shipping_carrier_id');
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->enum('provider_type', ['aggregator', 'tracking', 'rates']); // Sesuai SQL
            $table->enum('mode', ['test', 'live'])->default('test');
            $table->boolean('is_enabled')->default(false);
            $table->json('config_json')->nullable();
            $table->timestamps();
        });

        // 5. Shipping Services (Layanan: REG, YES, OKE) - TABEL YANG DITAMBAHKAN
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id('shipping_service_id');
            $table->foreignId('carrier_id')->constrained('shipping_carriers', 'shipping_carrier_id')->onDelete('cascade');
            $table->string('service_code', 60); // e.g., 'reg', 'yes'
            $table->string('service_name', 120); // e.g., 'Reguler', 'Yakin Esok Sampai'
            $table->boolean('is_enabled')->default(true);

            // Mencegah duplikasi service code untuk carrier yang sama
            $table->unique(['carrier_id', 'service_code'], 'uniq_carrier_service');
        });

        // 6. Shipments (Status Pengiriman per Order)
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('carrier_id')->nullable()->constrained('shipping_carriers', 'shipping_carrier_id')->nullOnDelete();
            $table->string('service_code', 60)->nullable(); // Snapshot service code saat order dibuat
            $table->string('tracking_number', 80)->nullable();
            $table->string('label_url')->nullable(); // Link download label pengiriman
            $table->enum('status', ['pending', 'label_created', 'in_transit', 'delivered', 'exception', 'cancelled'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('cost', 14, 2)->default(0); // Biaya ongkir real
            $table->json('metadata')->nullable(); // Log tracking detail

            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_services'); // Hapus services dulu sebelum carriers
        Schema::dropIfExists('shipping_carriers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_gateways');
    }
};
