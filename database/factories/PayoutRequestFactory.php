<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\BankAccount;

class PayoutRequestFactory extends Factory
{
    protected $model = PayoutRequest::class;

    public function definition()
    {
        return [
            'seller_id' => User::factory(),
            'amount' => $this->faker->numberBetween(10000, 1000000),
            'status' => 'requested',
            'bank_account_id' => BankAccount::factory(),
            'requested_at' => now(),
            'processed_at' => null,
            'processed_by' => null,
            'notes' => null,
        ];
    }
}
