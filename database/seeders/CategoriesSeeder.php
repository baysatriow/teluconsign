<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_id' => 1,
                'parent_id' => null,
                'name' => 'Pakaian',
                'slug' => 'pakaian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'parent_id' => null,
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'parent_id' => null,
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 4,
                'parent_id' => 3,
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 5,
                'parent_id' => null,
                'name' => 'Perabotan',
                'slug' => 'perabotan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 6,
                'parent_id' => null,
                'name' => 'Otomotif',
                'slug' => 'otomotif',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
