<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebhookLogsSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'webhook_log_id' => 1,
                'provider_code' => 'whatsapp',
                'event_type' => 'send_message_test',
                'related_id' => '6281377754080',
                'payload' => json_encode(['result' => ['data' => ['id' => [136013149], 'quota' => ['6281377754080' => ['used' => 1, 'quota' => 974, 'remaining' => 973]], 'status' => true], 'status' => true], 'target' => '6281377754080', 'message' => 'CEK']),
                'received_at' => '2025-12-19 19:03:12',
            ],
            [
                'webhook_log_id' => 2,
                'provider_code' => 'rajaongkir',
                'event_type' => 'cost_check_test',
                'related_id' => 'ADMIN-TEST-1766171022',
                'payload' => json_encode(['origin' => '501', 'result' => ['data' => [['etd' => '8 day', 'code' => 'jne', 'cost' => 20000]], 'status' => true], 'weight' => '1000', 'courier' => 'jne', 'destination' => '114']),
                'received_at' => '2025-12-19 19:03:42',
            ],
        ];

        DB::table('webhook_logs')->insert($logs);
    }
}
