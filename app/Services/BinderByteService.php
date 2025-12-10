<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BinderByteService extends BaseIntegrationService
{
    protected string $providerCode = 'binderbyte';

    public function trackPackage(string $courier, string $awb)
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            // BinderByte butuh API Key
            $apiKey = $cred->public_k;
            $baseUrl = 'https://api.binderbyte.com/v1/track';

            $response = Http::get($baseUrl, [
                'api_key' => $apiKey,
                'courier' => $courier,
                'awb' => $awb
            ]);

            $result = $response->json();

            if ($response->status() != 200 || ($result['status'] ?? 0) != 200) {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal melacak resi.'
                ];
            }

            return [
                'success' => true,
                'data' => $result['data']
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
