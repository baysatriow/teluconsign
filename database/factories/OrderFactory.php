<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $subtotal = $this->faker->numberBetween(10000, 1000000);
        $shipping = $this->faker->numberBetween(5000, 50000);
        $platformFee = 2500;
        
        return [
            'code' => 'ORD-' . $this->faker->unique()->numerify('##########'),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'shipping_address_id' => Address::factory(),
            'shipping_address_snapshot' => [],
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal_amount' => $subtotal,
            'shipping_cost' => $shipping,
            'platform_fee_buyer' => $platformFee,
            'platform_fee_seller' => $platformFee,
            'total_amount' => $subtotal + $shipping + $platformFee,
            'seller_earnings' => $subtotal - $platformFee,
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
