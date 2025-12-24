<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'label' => fake()->word(),
            'recipient' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'detail_address' => fake()->address(),
            'province' => fake()->state(),
            'city' => fake()->city(),
            'district' => fake()->streetName(),
            'village' => fake()->streetSuffix(),
            'postal_code' => fake()->postcode(),
            'country' => 'ID',
            'is_default' => false,
            'is_shop_default' => false,
        ];
    }
}
