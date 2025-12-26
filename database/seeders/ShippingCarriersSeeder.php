<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingCarriersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carriers = [
            [
                'shipping_carrier_id' => 1,
                'code' => 'jne',
                'name' => 'JNE',
                'provider_type' => 'rates',
                'mode' => 'test',
                'is_enabled' => true,
                'config_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shipping_carrier_id' => 2,
                'code' => 'sicepat',
                'name' => 'SiCepat',
                'provider_type' => 'aggregator',
                'mode' => 'test',
                'is_enabled' => true,
                'config_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shipping_carrier_id' => 3,
                'code' => 'pos',
                'name' => 'POS Indonesia',
                'provider_type' => 'rates',
                'mode' => 'test',
                'is_enabled' => true,
                'config_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'shipping_carrier_id' => 4,
                'code' => 'jnt',
                'name' => 'JNT EXPRESS',
                'provider_type' => 'aggregator',
                'mode' => 'test',
                'is_enabled' => true,
                'config_json' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('shipping_carriers')->insert($carriers);
    }
}
