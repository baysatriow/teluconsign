<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id('cart_id');
            $table->foreignId('buyer_id')->unique()->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('total_price', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->foreignId('cart_id')->constrained('carts', 'cart_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->restrictOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->unique(['cart_id', 'product_id'], 'uniq_cart_product');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('code', 40)->unique();
            $table->foreignId('buyer_id')->constrained('users', 'user_id');
            $table->foreignId('seller_id')->constrained('users', 'user_id');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses', 'address_id');
            $table->json('shipping_address_snapshot')->nullable();
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending', 'settlement', 'expire', 'deny'])->default('pending');
            $table->decimal('subtotal_amount', 14, 2);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('platform_fee', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('seller_earnings', 14, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products', 'product_id')->nullOnDelete();
            $table->string('product_title_snapshot', 160);
            $table->decimal('unit_price', 14, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 14, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
