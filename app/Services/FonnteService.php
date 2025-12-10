<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService extends BaseIntegrationService
{
    protected string $providerCode = 'whatsapp'; // Sesuai data SQL Anda

    /**
     * Kirim pesan WhatsApp
     * * @param string $target Nomor HP tujuan (format: 08xx atau 628xx)
     * @param string $message Pesan yang akan dikirim
     * @return array Response dari Fonnte
     */
    public function sendMessage(string $target, string $message)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // Fonnte biasanya menggunakan header Authorization dengan token
            // Di tabel integration_keys, simpan Token Fonnte di kolom 'public_k'

            $response = Http::withHeaders([
                'Authorization' => $cred->public_k,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                // 'countryCode' => '62', // Optional jika nomor tidak pakai kode negara
            ]);

            if ($response->failed()) {
                Log::error('Fonnte Error:', $response->json());
                return ['status' => false, 'error' => $response->body()];
            }

            return ['status' => true, 'data' => $response->json()];

        } catch (\Exception $e) {
            Log::error('Fonnte Service Exception: ' . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}
