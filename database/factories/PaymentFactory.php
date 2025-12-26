<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Order;
use App\Models\IntegrationProvider;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'provider_id' => IntegrationProvider::factory(), // Assuming IntegrationProvider has a factory or we create one onfly
            'provider_order_id' => 'PAY-' . $this->faker->unique()->uuid,
            'provider_txn_id' => 'TXN-' . $this->faker->uuid,
            'amount' => $this->faker->numberBetween(10000, 1000000),
            'currency' => 'IDR',
            'status' => 'pending',
            'raw_response' => [],
        ];
    }
}
