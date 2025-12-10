<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->userName(), // Tambahan sesuai tabel Anda

            // HAPUS baris 'email_verified_at' => now(), karena kolomnya tidak ada

            // GANTI 'password' menjadi 'password_hash' sesuai tabel Anda
            'password_hash' => Hash::make('password'),

            'photo_url' => null,
            'role' => 'buyer',   // Default value
            'status' => 'active', // Default value
            'remember_token' => Str::random(10),
        ];
    }

    
}
