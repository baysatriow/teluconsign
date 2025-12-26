<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // WAJIB: Integration seeders harus dijalankan terlebih dahulu
        $this->call([
            IntegrationProvidersSeeder::class,
            IntegrationKeysSeeder::class,
            CategoriesSeeder::class,
            ShippingCarriersSeeder::class,
        ]);
        
        // Uncomment untuk menjalankan seeder lainnya setelah membuat model dan factory
        // $this->call([
        //     UsersSeeder::class,
        //     ProductsSeeder::class,
        // ]);
    }
}
