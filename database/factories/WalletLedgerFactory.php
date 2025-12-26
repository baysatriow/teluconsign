<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WalletLedger;
use App\Models\User;

class WalletLedgerFactory extends Factory
{
    protected $model = WalletLedger::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'direction' => $this->faker->randomElement(['credit', 'debit']),
            'source_type' => 'deposit', // Simplified
            'source_id' => null,
            'amount' => $this->faker->numberBetween(10000, 500000),
            'balance_after' => $this->faker->numberBetween(10000, 1000000),
            'memo' => $this->faker->sentence(),
            'posted_at' => now(), // Important for not null
        ];
    }
}
