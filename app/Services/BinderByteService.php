<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| BINDERBYTE TRACKING SERVICE
|--------------------------------------------------------------------------
*/
class BinderByteService extends BaseIntegrationService
{
    protected string $providerCode = 'binderbyte';

    /*
    |--------------------------------------------------------------------------
    | TRACK PACKAGE
    |--------------------------------------------------------------------------
    */
    public function trackPackage(string $courier, string $awb): array
    {
        try {
            $cred = $this->getCredential($this->providerCode);

            $response = Http::get('https://api.binderbyte.com/v1/track', [
                'api_key' => $cred->public_k,
                'courier' => $courier,
                'awb'     => $awb,
            ]);

            $result = $response->json();

            if ($response->status() !== 200 || ($result['status'] ?? 0) !== 200) {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal melacak resi.',
                ];
            }

            return [
                'success' => true,
                'data'    => $result['data'],
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
