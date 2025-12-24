<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| MIDTRANS SERVICE
|--------------------------------------------------------------------------
| Snap API & Core API integration
|--------------------------------------------------------------------------
*/
class MidtransService extends BaseIntegrationService
{
    protected string $providerCode = 'midtrans';

    /*
    |----------------------------------------------------------------------
    | SNAP TOKEN (HOSTED PAYMENT)
    |----------------------------------------------------------------------
    */
    public function createSnapToken(array $orderData)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            $isProduction = $cred->config['environment'] === 'production';
            $baseUrl = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $authKey = base64_encode($cred->secret_key . ':');

            $payload = isset($orderData['transaction_details'])
                ? $orderData
                : [
                    'transaction_details' => [
                        'order_id'     => $orderData['code'],
                        'gross_amount'=> (int) $orderData['total_amount'],
                    ],
                    'customer_details' => [
                        'first_name' => $orderData['customer_name'],
                        'email'      => $orderData['customer_email'],
                        'phone'      => $orderData['customer_phone'],
                    ],
                    'item_details' => $orderData['items'] ?? [],
                ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authKey,
                'Accept'        => 'application/json',
            ])->post($baseUrl, $payload);

            if ($response->failed()) {
                Log::error('Midtrans Snap Error', $response->json());
                throw new \Exception('Gagal membuat transaksi pembayaran.');
            }

            return $response->json();

        } catch (\Exception $e) {
            return null;
        }
    }

    /*
    |----------------------------------------------------------------------
    | CORE API - CREATE CHARGE
    |----------------------------------------------------------------------
    */
    public function createCharge(array $chargeData)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            $isProduction = $cred->config['environment'] === 'production';
            $baseUrl = $isProduction
                ? 'https://api.midtrans.com/v2/charge'
                : 'https://api.sandbox.midtrans.com/v2/charge';

            $authKey = base64_encode($cred->secret_key . ':');

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authKey,
                'Accept'        => 'application/json',
            ])->post($baseUrl, $chargeData);

            if ($response->failed()) {
                Log::error('Midtrans Charge Error', $response->json());
                throw new \Exception(
                    $response->json()['status_message'] ?? 'Gagal membuat charge.'
                );
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Midtrans Charge Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /*
    |----------------------------------------------------------------------
    | CHECK TRANSACTION STATUS
    |----------------------------------------------------------------------
    */
    public function checkStatus(string $orderId)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            $isProduction = $cred->config['environment'] === 'production';
            $baseUrl = $isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

            $authKey = base64_encode($cred->secret_key . ':');

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authKey,
                'Accept'        => 'application/json',
            ])->get($baseUrl);

            return $response->failed() ? null : $response->json();

        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Exception: ' . $e->getMessage());
            return null;
        }
    }

    /*
    |----------------------------------------------------------------------
    | SIMULATE PAYMENT SUCCESS (SANDBOX ONLY)
    |----------------------------------------------------------------------
    | Untuk Testing - finish transaction in sandbox mode
    */
    public function simulatePaymentSuccess(string $orderId)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            if ($cred->config['environment'] === 'production') {
                throw new \Exception('Payment simulation only available in sandbox mode.');
            }

            $baseUrl = "https://api.sandbox.midtrans.com/v2/{$orderId}/status/b2b/settle";
            $authKey = base64_encode($cred->secret_key . ':');

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $authKey,
                'Accept'        => 'application/json',
            ])->post($baseUrl);

            return $response->failed() ? null : $response->json();

        } catch (\Exception $e) {
            Log::error('Midtrans Simulation Exception: ' . $e->getMessage());
            return null;
        }
    }
}
