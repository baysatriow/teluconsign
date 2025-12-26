<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->string('code', 40)->unique();
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->json('shipping_address_snapshot')->nullable();
            $table->string('status', 50)->default('pending');
            $table->enum('payment_status', ['pending', 'settlement', 'expire', 'deny'])->default('pending');
            $table->decimal('subtotal_amount', 14, 2);
            $table->decimal('shipping_cost', 14, 2)->default(0.00);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('seller_earnings', 14, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->decimal('platform_fee_buyer', 10, 2)->default(2500.00)->comment('Platform fee charged to buyer');
            $table->decimal('platform_fee_seller', 10, 2)->default(2500.00)->comment('Platform fee charged to seller');
            $table->timestamps();
            
            $table->foreign('buyer_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('seller_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('shipping_address_id')
                  ->references('address_id')
                  ->on('addresses')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
