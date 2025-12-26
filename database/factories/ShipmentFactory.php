<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\ShippingCarrier;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'carrier_id' => ShippingCarrier::factory(), // Assuming Carrier has factory, or verify
            'service_code' => 'REG',
            'tracking_number' => $this->faker->bothify('##??####??'),
            'status' => 'pending',
            'cost' => 15000,
            'metadata' => [],
        ];
    }
}
