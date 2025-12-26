<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\IntegrationProvider;

class IntegrationProviderFactory extends Factory
{
    protected $model = IntegrationProvider::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->slug,
            'name' => $this->faker->company
        ];
    }
}
