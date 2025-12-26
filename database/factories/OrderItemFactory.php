<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $price = $this->faker->numberBetween(10000, 500000);
        $qty = $this->faker->numberBetween(1, 5);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_title_snapshot' => $this->faker->words(3, true),
            'unit_price' => $price,
            'quantity' => $qty,
            'subtotal' => $price * $qty,
        ];
    }
}
