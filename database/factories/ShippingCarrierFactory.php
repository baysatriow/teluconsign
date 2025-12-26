<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ShippingCarrier;

class ShippingCarrierFactory extends Factory
{
    protected $model = ShippingCarrier::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->slug,
            'name' => $this->faker->company,
            'provider_type' => 'rates',
            'is_enabled' => true
        ];
    }
}
