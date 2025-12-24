<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seller_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(10000, 1000000),
            'weight' => fake()->numberBetween(100, 5000),
            'stock' => fake()->numberBetween(1, 100),
            'condition' => fake()->randomElement(['new', 'used']),
            'status' => 'active', // Assuming string or enum mapping works
            'main_image' => 'products/default.jpg',
        ];
    }
}
