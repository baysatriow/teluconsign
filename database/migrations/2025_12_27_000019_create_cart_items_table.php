<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->foreignId('cart_id')->constrained('carts', 'cart_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('subtotal', 14, 2);
            
            $table->unique(['cart_id', 'product_id'], 'uniq_cart_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
