<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/*
|--------------------------------------------------------------------------
| FONNTE WHATSAPP SERVICE
|--------------------------------------------------------------------------
*/
class FonnteService extends BaseIntegrationService
{
    protected string $providerCode = 'whatsapp';

    /*
    |--------------------------------------------------------------------------
    | SEND MESSAGE
    |--------------------------------------------------------------------------
    */
    public function sendMessage(string $target, string $message): array
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // Fallback jika token belum dikonfigurasi
            if (!$cred || empty($cred->public_k)) {
                Log::info("FonnteService (Simulasi) ke {$target}: {$message}");

                return [
                    'status'  => true,
                    'message' => 'Simulated (No API Key Configured)',
                ];
            }

            $target = $this->normalizePhoneNumber($target);

            $response = Http::withHeaders([
                'Authorization' => $cred->public_k,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $target,
                'message' => $message,
            ]);

            if ($response->failed()) {
                Log::error('Fonnte API Error', $response->json() ?? []);

                return [
                    'status' => false,
                    'error'  => $response->body(),
                ];
            }

            return [
                'status' => true,
                'data'   => $response->json(),
            ];

        } catch (Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());

            return [
                'status' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PHONE NORMALIZATION
    |--------------------------------------------------------------------------
    */
    private function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '08')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
