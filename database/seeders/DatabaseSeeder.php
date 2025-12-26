<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            IntegrationProvidersSeeder::class,
            IntegrationKeysSeeder::class,
            UsersSeeder::class,
            CategoriesSeeder::class,
            ShippingCarriersSeeder::class,
            ProductsSeeder::class,
            CartsSeeder::class,
            WebhookLogsSeeder::class,
        ]);
    }
}
