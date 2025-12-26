<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->bigIncrements('shipment_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('service_code', 60)->nullable();
            $table->string('tracking_number', 80)->nullable();
            $table->string('label_url', 255)->nullable();
            $table->enum('status', ['pending', 'label_created', 'in_transit', 'delivered', 'exception', 'cancelled'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('cost', 14, 2)->default(0.00);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
                  
            $table->foreign('carrier_id')
                  ->references('shipping_carrier_id')
                  ->on('shipping_carriers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
