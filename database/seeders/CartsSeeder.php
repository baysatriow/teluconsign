<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartsSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Carts
        $carts = [
            [
                'cart_id' => 1,
                'buyer_id' => 6,
                'total_price' => 26000.00,
                'created_at' => '2025-12-10 21:13:32',
                'updated_at' => '2025-12-23 21:57:03',
            ],
            [
                'cart_id' => 2,
                'buyer_id' => 7,
                'total_price' => 7830000.00,
                'created_at' => '2025-12-10 21:20:24',
                'updated_at' => '2025-12-23 18:00:12',
            ],
            [
                'cart_id' => 4,
                'buyer_id' => 1,
                'total_price' => 1551000.00,
                'created_at' => '2025-12-11 04:39:25',
                'updated_at' => '2025-12-23 21:56:36',
            ],
            [
                'cart_id' => 6,
                'buyer_id' => 18,
                'total_price' => 0.00,
                'created_at' => '2025-12-23 13:06:14',
                'updated_at' => '2025-12-23 13:17:27',
            ],
            [
                'cart_id' => 7,
                'buyer_id' => 22,
                'total_price' => 250000.00,
                'created_at' => '2025-12-23 21:26:14',
                'updated_at' => '2025-12-23 21:26:31',
            ],
        ];

        DB::table('carts')->insert($carts);

        // Seed Cart Items
        $cartItems = [
            ['cart_item_id' => 7, 'cart_id' => 4, 'product_id' => 20, 'quantity' => 1, 'unit_price' => 20000.00, 'subtotal' => 20000.00],
            ['cart_item_id' => 8, 'cart_id' => 4, 'product_id' => 19, 'quantity' => 1, 'unit_price' => 1000.00, 'subtotal' => 1000.00],
            ['cart_item_id' => 9, 'cart_id' => 4, 'product_id' => 18, 'quantity' => 1, 'unit_price' => 30000.00, 'subtotal' => 30000.00],
            ['cart_item_id' => 35, 'cart_id' => 1, 'product_id' => 23, 'quantity' => 1, 'unit_price' => 25000.00, 'subtotal' => 25000.00],
            ['cart_item_id' => 43, 'cart_id' => 1, 'product_id' => 25, 'quantity' => 1, 'unit_price' => 1000.00, 'subtotal' => 1000.00],
            ['cart_item_id' => 48, 'cart_id' => 2, 'product_id' => 56, 'quantity' => 1, 'unit_price' => 1500000.00, 'subtotal' => 1500000.00],
            ['cart_item_id' => 50, 'cart_id' => 2, 'product_id' => 60, 'quantity' => 1, 'unit_price' => 3300000.00, 'subtotal' => 3300000.00],
            ['cart_item_id' => 51, 'cart_id' => 2, 'product_id' => 55, 'quantity' => 1, 'unit_price' => 3030000.00, 'subtotal' => 3030000.00],
            ['cart_item_id' => 59, 'cart_id' => 7, 'product_id' => 51, 'quantity' => 1, 'unit_price' => 250000.00, 'subtotal' => 250000.00],
            ['cart_item_id' => 60, 'cart_id' => 4, 'product_id' => 56, 'quantity' => 1, 'unit_price' => 1500000.00, 'subtotal' => 1500000.00],
        ];

        DB::table('cart_items')->insert($cartItems);
    }
}
