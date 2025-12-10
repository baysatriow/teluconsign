<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService extends BaseIntegrationService
{
    protected string $providerCode = 'midtrans';

    /**
     * Membuat Snap Token untuk Transaksi
     */
    public function createSnapToken(array $orderData)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // Cek Environment dari meta_json (sandbox/production)
            $isProduction = $cred->config['environment'] === 'production';
            $baseUrl = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            // Server Key (encrypted_k) wajib di-base64 encode untuk Basic Auth
            $serverKey = $cred->secret_key;
            $authKey = base64_encode($serverKey . ':');

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderData['code'], // INV/2025/...
                    'gross_amount' => (int) $orderData['total_amount'],
                ],
                'customer_details' => [
                    'first_name' => $orderData['customer_name'],
                    'email' => $orderData['customer_email'],
                    'phone' => $orderData['customer_phone'],
                ],
                'item_details' => $orderData['items'] ?? [],
                // 'callbacks' => [ ... ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($baseUrl, $payload);

            if ($response->failed()) {
                Log::error('Midtrans Snap Error:', $response->json());
                throw new \Exception('Gagal membuat transaksi pembayaran.');
            }

            return $response->json(); // Berisi 'token' dan 'redirect_url'

        } catch (\Exception $e) {
            Log::error('Midtrans Service Exception: ' . $e->getMessage());
            return null;
        }
    }
}
