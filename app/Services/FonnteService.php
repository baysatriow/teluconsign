<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FonnteService extends BaseIntegrationService
{
    protected string $providerCode = 'whatsapp'; // Sesuai data di tabel integration_providers

    /**
     * Kirim pesan WhatsApp
     * @param string $target Nomor HP tujuan
     * @param string $message Pesan yang akan dikirim
     * @return array Status pengiriman
     */
    public function sendMessage(string $target, string $message)
    {
        try {
            // Ambil kredensial dari BaseIntegrationService
            $cred = $this->getCredential($this->providerCode);

            // Jika kredensial tidak ditemukan (belum disetting di DB), lakukan simulasi agar app tidak crash
            if (!$cred || empty($cred->public_k)) {
                Log::info("FonnteService (Simulasi) ke {$target}: {$message}");
                return ['status' => true, 'message' => 'Simulated (No API Key Configured)'];
            }

            // Normalisasi Nomor HP (08 -> 628)
            $target = $this->normalizePhoneNumber($target);

            // Token Fonnte disimpan di kolom public_k pada tabel integration_keys
            $token = $cred->public_k;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                // 'countryCode' => '62', // Optional
            ]);

            if ($response->failed()) {
                Log::error('Fonnte API Error:', $response->json() ?? []);
                return ['status' => false, 'error' => $response->body()];
            }

            return ['status' => true, 'data' => $response->json()];

        } catch (Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            // Jangan throw exception agar flow utama (Register/Login) tidak putus
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mengubah format 08xx menjadi 628xx
     */
    private function normalizePhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 2) === '08') return '62' . substr($phone, 1);
        if (substr($phone, 0, 1) === '8') return '62' . $phone;
        return $phone;
    }
}
