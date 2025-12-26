<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BankAccount;
use App\Models\User;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'bank_name' => 'BCA',
            'account_name' => $this->faker->name,
            'account_no' => $this->faker->bankAccountNumber,
            'is_default' => false,
        ];
    }
}
