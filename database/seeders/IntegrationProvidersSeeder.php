<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegrationProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'integration_provider_id' => 1,
                'code' => 'midtrans',
                'name' => 'Midtrans Payment Gateway',
                'created_at' => '2025-12-10 23:31:19',
            ],
            [
                'integration_provider_id' => 2,
                'code' => 'rajaongkir',
                'name' => 'RajaOngkir Rates',
                'created_at' => '2025-12-10 23:31:19',
            ],
            [
                'integration_provider_id' => 3,
                'code' => 'whatsapp',
                'name' => 'WhatsApp (Fonnte)',
                'created_at' => '2025-12-10 23:31:19',
            ],
            [
                'integration_provider_id' => 4,
                'code' => 'binderbyte',
                'name' => 'BinderByte Tracking',
                'created_at' => '2025-12-10 23:31:19',
            ],
            [
                'integration_provider_id' => 5,
                'code' => 'gemini',
                'name' => 'Google Gemini AI',
                'created_at' => '2025-12-10 23:31:19',
            ],
        ];

        DB::table('integration_providers')->insert($providers);
    }
}
