<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegrationKeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = [
            [
                'integration_key_id' => 1,
                'provider_id' => 1,
                'label' => 'Midtrans Sandbox',
                'is_active' => true,
                'public_k' => 'SB-Mid-client-7uT53o-wBlWymRaF',
                'encrypted_k' => 'eyJpdiI6Ikd5d0dQOTVwWmxTdTd0TFNqdW1INWc9PSIsInZhbHVlIjoiR2c5aEZubUFicVZGQjY4bWVXSkJoa0lrSGgxTVJDdUhVR1BPdGpmYmcxbll4akhzTlhaNURldHVxbXlWSUgxSSIsIm1hYyI6Ijc5ZWI1Zjg5Nzc0MjhlZmI4NzljM2I2YjgxNDlhNzc0OTY3ODMyYjQ1YjZkZGU3NjMzODFjMGZhYWMzNmYzNDciLCJ0YWciOiIifQ==',
                'meta_json' => json_encode([
                    'environment' => 'sandbox',
                    'merchant_id' => 'G777111946'
                ]),
                'created_at' => '2025-12-10 23:31:19',
                'updated_at' => '2025-12-19 17:47:18',
            ],
            [
                'integration_key_id' => 2,
                'provider_id' => 2,
                'label' => 'RajaOngkir Key',
                'is_active' => true,
                'public_k' => 'd5LxeDvW8f8033e2b179a590DEyT4Xf1',
                'encrypted_k' => null,
                'meta_json' => json_encode([
                    'base_url' => 'https://rajaongkir.komerce.id/api/v1'
                ]),
                'created_at' => '2025-12-10 23:31:19',
                'updated_at' => '2025-12-23 15:10:52',
            ],
            [
                'integration_key_id' => 3,
                'provider_id' => 3,
                'label' => 'Fonnte Token',
                'is_active' => true,
                'public_k' => 'MKUF74yNbcb6MZXZRx63',
                'encrypted_k' => null,
                'meta_json' => json_encode([]),
                'created_at' => '2025-12-10 23:31:19',
                'updated_at' => '2025-12-16 09:08:02',
            ],
        ];

        DB::table('integration_keys')->insert($keys);
    }
}
