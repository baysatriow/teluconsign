<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\User;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'buyer_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
