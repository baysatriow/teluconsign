<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| RAJAONGKIR SERVICE
|--------------------------------------------------------------------------
*/

class RajaOngkirService extends BaseIntegrationService
{
    protected string $providerCode = 'rajaongkir';

    /*
    |--------------------------------------------------------------------------
    | CHECK DOMESTIC COST
    |--------------------------------------------------------------------------
    */
    public function checkCost($origin, $originType, $destination, $destinationType, $weight, $courier)
    {
        try {
            $cred = $this->getCredential($this->providerCode);
            $baseUrl = $cred->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';
            
            $response = Http::asForm()->withHeaders([
                'key' => $cred->public_k,
            ])->post("{$baseUrl}/calculate/domestic-cost", [
                'origin' => $origin,        
                'destination' => $destination, 
                'weight' => $weight,
                'courier' => $courier
            ]);

            if ($response->failed()) {
                if ($response->clientError()) {
                    return ['status' => true, 'data' => []];
                }

                Log::error("RajaOngkir Error: " . $response->body());
                return ['status' => false, 'message' => 'Gagal koneksi API Ongkir.'];
            }

            $result = $response->json();

            $data = $result['data'] ?? [];

            return [
                'status' => true,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getProvinces()
    {
        return $this->fetchData('/destination/province');
    }

    public function getCities($provinceId = null)
    {
        $params = $provinceId ? ['province' => $provinceId] : [];
        return $this->fetchData('/destination/city', $params);
    }

    public function getSubdistricts($cityId)
    {
        return $this->fetchData("/destination/district/{$cityId}");
    }

    private function fetchData($endpoint, $params = [])
    {
        try {
            $cred = $this->getCredential($this->providerCode);
            $baseUrl = $cred->config['base_url'] ?? 'https://rajaongkir.komerce.id/api/v1';

            $response = Http::withHeaders([
                'key' => $cred->public_k,
            ])->get("{$baseUrl}{$endpoint}", $params);

            if ($response->failed()) {
                Log::error("RajaOngkir Fetch Error ({$endpoint})", $response->json());
                return [];
            }

            $result = $response->json();

            return $result['data'] ?? $result['rajaongkir']['results'] ?? [];

        } catch (\Exception $e) {
            Log::error("RajaOngkir Exception ({$endpoint}): " . $e->getMessage());
            return [];
        }
    }
}
